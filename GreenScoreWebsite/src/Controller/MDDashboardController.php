<?php

namespace App\Controller;

use App\Repository\MonitoredWebsiteRepository;
use App\Repository\UserRepository;
use App\Repository\AdviceRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MDDashboardController extends BaseDashboardController
{

    #[Route('/mes-donnees', name: 'app_my_datas')]
    public function myDatas(UserRepository $userRepository, AdviceRepository $adviceRepository, MonitoredWebsiteRepository $monitoredWebsiteRepository): Response
    {
        $messageAverageFootprint = "Bravo ! Votre empreinte carbone journalière est plus basse que la moyenne !!";

        $noDatas = false;
        $user = $this->getUser();

        if ($user){
            $userId = $user->getId();
            $usersIdsCharts = [$user->getId()];
            // Total Consumption
            try {
                $user = $userRepository->find($userId);
        
                if (!$user) {
                    throw new Exception('Utilisateur non trouvé');
                }

                $totalConsu = $user->getTotalCarbonFootprint();
            } catch (Exception $e) {
                $totalConsu = null;
                $noDatas = true;
            }
        }else{
            $noDatas = true;
        }

        if (!$noDatas){
            // AverageDailyCarbonFootprint
            $myAverageDailyCarbonFootprint = $monitoredWebsiteRepository->getAverageDailyCarbonFootprint([$userId]);
            $averageDailyCarbonFootprint = $monitoredWebsiteRepository->getGlobalAverageDailyCarbonFootprint();

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
                    $calculateGreenScore = $this->calculateGreenScoreService->calculateGreenScore($totalConsu, 'mes-donnees');
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
            return $this->render('dashboards/my_datas.html.twig', [
                'page' => 'mes-donnees',
                'title' => 'Mes Données',
                'description' => 'bla bla bla',
                'totalConsu' => $this->formatConsumption($totalConsu ?? null) ?? null,
                'totalConsuUnit' => $this->formatUnitConsumption($totalConsu ?? null) ?? null,
                'advice' => $advice ?? null,
                'adviceDev' => $adviceDev ?? null,
                'averageDailyCarbonFootprint' => $averageDailyCarbonFootprint ?? null,
                'equivalent1' => $equivalent1 ?? null,
                'equivalent2' => $equivalent2 ?? null,
                'myAverageDailyCarbonFootprint' => $myAverageDailyCarbonFootprint ?? null,
                'messageAverageFootprint' => $messageAverageFootprint ?? null,
                'usersIdsCharts' => implode(',', $usersIdsCharts ?? null) ?? null,
                'noDatas' => $noDatas,
                'letterGreenScore' => $letterGreenScore ?? null,
                'envNomination' => $envNomination ?? null,
            ]);
        else
            return $this->redirectToRoute('app_login');
    }
}
