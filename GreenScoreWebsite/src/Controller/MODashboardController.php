<?php

namespace App\Controller;

use App\Repository\MonitoredWebsiteRepository;
use App\Repository\UserRepository;
use App\Repository\AdviceRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/*!
 * Cette classe contient les routes pour consulter les dashboards d'une organisation.
 * MOD : my organisation dashboard
 */
class MODashboardController extends BaseDashboardController
{

    #[Route('/mon-organisation', name: 'app_my_organisation')]
    public function myOrganisation(
        MonitoredWebsiteRepository $monitoredWebsiteRepository,
        UserRepository $userRepository,
        AdviceRepository $adviceRepository
    ): Response {
        $user = $this->getUser();
        $noDatas = false;

        if ($user) {
            // Récupération de l'organisation
            $orga = $user->getOrganisation() ?? $user->getIsAdminOf();
            $usersIdsOrga = $orga ? $userRepository->getUsersOrga($orga->getId()) : null;
            $noDatas = empty($usersIdsOrga);
        }

        

        if (!$noDatas && $user) {

            // Récupération des données
            $averageFootprint = $monitoredWebsiteRepository->getAverageDailyCarbonFootprint($usersIdsOrga);
            $totalConsu = $monitoredWebsiteRepository->getTotalConsuOrga($usersIdsOrga);
            
            $equivalentAverage = $this->getEquivalents($averageFootprint, 1)[0] ?? null;
            [$equivalent1, $equivalent2] = $this->getEquivalents($totalConsu, 2);
            [$envNomination, $letterGreenScore] = $this->getGreenScore($totalConsu);
            
            $advice = $this->getRandomAdvice($adviceRepository, false);
            $adviceDev = $this->getRandomAdvice($adviceRepository, true);
        }

        
        if ($user) {
            return $this->render('dashboards/my_organisation.html.twig', [
                'page' => 'mon-organisation',
                'title' => 'Mon Organisation',
                'description' => 'Toutes les données sur les membres de : ' . ($orga != null ? ($orga->getOrganisationName() ?? null) : null),
                'equivalentAverage' => $equivalentAverage ?? null,
                'totalConsu' => $this->formatConsumption($totalConsu ?? null) ?? null,
                'totalConsuUnit' => $this->formatUnitConsumption($totalConsu ?? null) ?? null,
                'advice' => $advice ?? null,
                'adviceDev' => $adviceDev ?? null,
                'averageFootprint' => $averageFootprint ?? null,
                'equivalent1' => $equivalent1 ?? null,
                'equivalent2' => $equivalent2 ?? null,
                'usersIdsCharts' => $usersIdsOrga != null ? implode(',', array_map(fn($user) => $user->getId(), $usersIdsOrga)) ?? null : null,
                'noDatas' => $noDatas,
                'letterGreenScore' => $letterGreenScore ?? null,
                'envNomination' => $envNomination ?? null
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    // Récupérer des équivalents
    private function getEquivalents(?float $value, int $count): array
    {
        if (!$value) {
            return [];
        }
        try {
            $equivalents = $this->equivalentCalculatorService->calculateEquivalents($value, $count);
            return $equivalents ?: [];
        } catch (Exception $e) {
            $this->logger->error('Erreur lors du calcul des équivalents : ' . $e->getMessage());
            return [];
        }
    }

    // Récupérer le GreenScore
    private function getGreenScore(?float $totalConsu): array
    {
        if (!$totalConsu) {
            return [null, null];
        }
        try {
            $calculateGreenScore = $this->calculateGreenScoreService->calculateGreenScore($totalConsu, 'mon-organisation');
            if ($calculateGreenScore) {
                return [
                    $calculateGreenScore[0]['envNomination'] ?? null,
                    $calculateGreenScore[0]['letterGreenScore'] ?? null
                ];
            }
        } catch (Exception $e) {
            $this->logger->error('Erreur lors de la récupération du GreenScore : ' . $e->getMessage());
        }
        return [null, null];
    }

    // Récupérer un conseil aléatoire (dev ou non dev)
    private function getRandomAdvice(AdviceRepository $adviceRepository, bool $isDev): ?string
    {
        $adviceEntity = $adviceRepository->findRandomByIsDev($isDev);
        return $adviceEntity ? $adviceEntity->getAdvice() : null;
    }
}
