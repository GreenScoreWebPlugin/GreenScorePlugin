<?php

namespace App\Controller;

use App\Repository\MonitoredWebsiteRepository;
use App\Repository\UserRepository;
use App\Repository\AdviceRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MODashboardController extends BaseDashboardController
{

    #[Route('/mon-organisation', name: 'app_my_organisation')]
    public function myOrganisation(MonitoredWebsiteRepository $monitoredWebsiteRepository, UserRepository $userRepository, AdviceRepository $adviceRepository): Response
    {
        $noDatas = false;
        $user = $this->getUser();

        $noDatas = true;

        if ($user) {
            $orga = $user->getOrganisation();
            if ($orga && ($idOrga = $orga->getId())) {
                $usersIdsOrga = $userRepository->getUsersOrga($idOrga);
                $noDatas = !$usersIdsOrga;
            } else {
                $orga = $user->getIsAdminOf();
                if ($orga && ($idOrga = $orga->getId())) {
                    $usersIdsOrga = $userRepository->getUsersOrga($idOrga);
                    $noDatas = !$usersIdsOrga;
                }
            }
        }

        
        if (!$noDatas){
            // AverageDailyCarbonFootprint 
            $averageFootprint = $monitoredWebsiteRepository->getAverageDailyCarbonFootprint($usersIdsOrga);
            // Equivalent : Recuperer un equivalent aleatoire
            try {
                $equivalentsAverage = $this->equivalentCalculatorService->calculateEquivalents($averageFootprint, 1);
                
                if (count($equivalentsAverage) >= 1) {
                    $equivalentAverage = $equivalentsAverage[0];
                }
            } catch (Exception $e) {
                $this->logger->error('Erreur lors du calcul des équivalents : ' . $e->getMessage());
            }

            // Total Consumption
            $totalConsu = $monitoredWebsiteRepository->getTotalConsuOrga($usersIdsOrga);

            // Advices : Recuperer deux conseils aleatoire
            $adviceEntity = $adviceRepository->findRandomByIsDev(false);
            if ($adviceEntity) {
                $advice = $adviceEntity->getAdvice();
            }
            $adviceDevEntity = $adviceRepository->findRandomByIsDev(true);
            if ($adviceDevEntity) {
                $adviceDev = $adviceDevEntity->getAdvice();
            }

            if ($totalConsu) {
                // Equivalents : Recuperer deux equivalents aleatoires
                try {
                    $equivalents = $this->equivalentCalculatorService->calculateEquivalents($totalConsu, 2);
                    if (count($equivalents) >= 2) {
                        $equivalent1 = $equivalents[0];
                        $equivalent2 = $equivalents[1];
                    }
                } catch (Exception $e) {
                    $this->logger->error('Erreur lors du calcul des équivalents : ' . $e->getMessage());
                }

                // Environmental Impact Badge : Recuperer les donnees du badge GreenScore
                try {
                    $calculateGreenScore = $this->calculateGreenScoreService->calculateGreenScore($totalConsu, 'mon-organisation');
                    if($calculateGreenScore) {
                        $envNomination = $calculateGreenScore[0]['envNomination'];
                        $letterGreenScore = $calculateGreenScore[0]['letterGreenScore'];
                    }

                } catch (Exception $e) {
                    $this->logger->error('Erreur lors de la récupération du GreenScore : ' . $e->getMessage());
                }
            }
        }

        if($user)
            return $this->render('dashboards/my_organisation.html.twig', [
                'page' => 'mon-organisation',
                'title' => 'Mon Organisation',
                'description' => 'Toutes les données sur les membres de : ' . $orga->getOrganisationName(),
                'equivalentAverage' => $equivalentAverage ?? null,
                'totalConsu' => $this->formatConsumption($totalConsu ?? null) ?? null,
                'totalConsuUnit' => $this->formatUnitConsumption($totalConsu ?? null) ?? null,
                'advice' => $advice ?? null,
                'adviceDev' => $adviceDev ?? null,
                'averageFootprint' => $averageFootprint ?? null,
                'equivalent1' => $equivalent1 ?? null,
                'equivalent2' => $equivalent2 ?? null,
                'usersIdsCharts' => implode(',', array_map(fn($user) => $user->getId(), $usersIdsOrga ?? [])) ?? null,
                'noDatas' => $noDatas,
                'letterGreenScore' => $letterGreenScore ?? null,
                'envNomination' => $envNomination ?? null
            ]);
        else
            return $this->redirectToRoute('app_login');
    }
}
