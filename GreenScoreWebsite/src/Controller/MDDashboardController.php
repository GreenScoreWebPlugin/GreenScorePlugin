<?php

namespace App\Controller;

use App\Repository\MonitoredWebsiteRepository;
use App\Repository\UserRepository;
use App\Repository\AdviceRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/*!
 * Cette classe contient les routes pour consulter les dashboards d'un utilisateur.
 * MDD : my datas dashboard
 */
class MDDashboardController extends BaseDashboardController
{
    #[Route('/mes-donnees', name: 'app_my_datas')]
    public function myDatas(
        UserRepository $userRepository,
        AdviceRepository $adviceRepository,
        MonitoredWebsiteRepository $monitoredWebsiteRepository
    ): Response {
        $user = $this->getUser();
        $usersIdsCharts = [];
        $noData = false;


        if ($user) {

            $userId = $user->getId();
            $usersIdsCharts[] = $userId;

            // Récupérer les données de l'utilisateur
            $totalConsu = $user->getTotalCarbonFootprint();

            if(!$totalConsu)
                $noData = true;
        }

        if(!$noData && $user){
            // Calculs supplémentaires si des données existent
            $myAverageDailyCarbonFootprint = $this->getAverageDailyCarbonFootprint($userId, $monitoredWebsiteRepository);
            $averageDailyCarbonFootprint = $monitoredWebsiteRepository->getGlobalAverageDailyCarbonFootprint();
            $messageAverageFootprint = $this->generateAverageFootprintMessage($myAverageDailyCarbonFootprint, $averageDailyCarbonFootprint);

            // Conseils
            $advice = $this->getRandomAdvice($adviceRepository, false);
            $adviceDev = $this->getRandomAdvice($adviceRepository, true);

            // Équivalents
            [$equivalent1, $equivalent2] = $this->getEquivalents($totalConsu);

            // Badge GreenScore
            [$envNomination, $letterGreenScore] = $this->getGreenScore($totalConsu);
        }

        if ($user) {
            return $this->render('dashboards/my_datas.html.twig', [
                'page' => 'mes-donnees',
                'title' => 'Mes Données',
                'description' => 'Votre analyse de consommation à partir des sites que vous avez consulté',
                'totalConsu' => $this->formatConsumption($totalConsu ?? null),
                'totalConsuUnit' => $this->formatUnitConsumption($totalConsu ?? null),
                'advice' => $advice ?? null,
                'adviceDev' => $adviceDev ?? null,
                'averageDailyCarbonFootprint' => $averageDailyCarbonFootprint ?? null,
                'equivalent1' => $equivalent1 ?? null,
                'equivalent2' => $equivalent2 ?? null,
                'myAverageDailyCarbonFootprint' => $myAverageDailyCarbonFootprint ?? null,
                'messageAverageFootprint' => $messageAverageFootprint ?? null,
                'usersIdsCharts' => implode(',', $usersIdsCharts),
                'noDatas' => $noData,
                'letterGreenScore' => $letterGreenScore ?? null,
                'envNomination' => $envNomination ?? null,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    // Récupérer l'empreinte carbone journalière moyenne de l'utilisateur
    private function getAverageDailyCarbonFootprint(int $userId, MonitoredWebsiteRepository $monitoredWebsiteRepository): ?float
    {
        return $monitoredWebsiteRepository->getAverageDailyCarbonFootprint([$userId]);
    }

    // Générer le message en fonction de l'empreinte carbone journalière
    private function generateAverageFootprintMessage(?float $myAverageDailyCarbonFootprint, ?float $averageDailyCarbonFootprint): ?string
    {
        if ($myAverageDailyCarbonFootprint && $averageDailyCarbonFootprint) {
            return $myAverageDailyCarbonFootprint <= $averageDailyCarbonFootprint
                ? "Bravo ! Votre empreinte carbone journalière est plus basse que la moyenne !!"
                : "Aïe aïe aïe ! Votre empreinte carbone journalière est au-dessus de la moyenne. Vous pouvez encore l'améliorer !";
        }
        return null;
    }

    // Récupérer un conseil aléatoire (dev ou non dev)
    private function getRandomAdvice(AdviceRepository $adviceRepository, bool $isDev): ?string
    {
        $adviceEntity = $adviceRepository->findRandomByIsDev($isDev);
        return $adviceEntity ? $adviceEntity->getAdvice() : null;
    }

    // Récupérer les équivalents pour une consommation donnée
    private function getEquivalents(float $totalConsu): array
    {
        try {
            $equivalents = $this->equivalentCalculatorService->calculateEquivalents($totalConsu, 2);
            return count($equivalents) >= 2 ? [$equivalents[0], $equivalents[1]] : [null, null];
        } catch (Exception $e) {
            $this->logger->error('Erreur lors du calcul des équivalents : ' . $e->getMessage());
            return [null, null];
        }
    }

    // Récupérer le GreenScore en fonction de la consommation
    private function getGreenScore(float $totalConsu): array
    {
        try {
            $greenScore = $this->calculateGreenScoreService->calculateGreenScore($totalConsu, 'mes-donnees');
            return $greenScore ? [$greenScore[0]['envNomination'], $greenScore[0]['letterGreenScore']] : [null, null];
        } catch (Exception $e) {
            $this->logger->error('Erreur lors de la récupération du GreenScore : ' . $e->getMessage());
            return [null, null];
        }
    }
}
