<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PredictionRepository;
use App\Repository\RoundMatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function home(
        RoundMatchRepository $roundMatchRepository,
        PredictionRepository $predictionRepository
    ): Response {
        $dateTo = (new \DateTime())->modify('+5 day');
        $matches = $roundMatchRepository->findMatchesForInterval($dateTo);

        /** @var User|null */
        $user = $this->getUser();

        $sortedMatches = [];
        foreach ($matches as $match) {
            $userPrediction = null;

            if ($user) {
                $userPrediction = $predictionRepository->findPrediction($user, $match['matchId']);
            }

            $finished = false;

            $matchDateTime = new \DateTime($match['date']->format('Y-m-d H:i'));
            $currentDateTime = new \DateTime();

            if ($currentDateTime > $matchDateTime) {
                $finished = true;
            }

            $sortedMatches[$match['date']->format('Y-m-d')][] = [
                'id' => $match['id'],
                'date' => $match['date'],
                'homeTeamName' => $match['homeTeamName'],
                'awayTeamName' => $match['awayTeamName'],
                'fullTimeHomeTeamScore' => $match['fullTimeHomeTeamScore'],
                'fullTimeAwayTeamScore' => $match['fullTimeAwayTeamScore'],
                'extraTimeHomeTeamScore' => $match['extraTimeHomeTeamScore'],
                'extraTimeAwayTeamScore' => $match['extraTimeAwayTeamScore'],
                'finished' => $finished,
                'winner' => $match['winner'],
                'competition' => $match['competition'],
                'competitionName' => $match['competitionName'],
                'competitionCode' => $match['competitionCode'],
                'competitionEmblem' => $match['competitionEmblem'],
                'userPrediction' => $userPrediction,
            ];
        }

        return $this->render('home/index.html.twig', [
            'sortedMatches' => $sortedMatches,
        ]);
    }
}
