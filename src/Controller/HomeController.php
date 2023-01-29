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
                $userPrediction = $predictionRepository->findPrediction($user, $match->getMatchId());
            }

            $finished = false;

            $matchDateTime = new \DateTime($match->getDate()->format('Y-m-d H:i'));
            $currentDateTime = new \DateTime();

            if ($currentDateTime > $matchDateTime) {
                $finished = true;
            }

            $sortedMatches[$match->getDate()->format('Y-m-d')][] = [
                'id' => $match->getId(),
                'date' => $match->getDate(),
                'homeTeamName' => $match->getHomeTeamName(),
                'awayTeamName' => $match->getAwayTeamName(),
                'fullTimeHomeTeamScore' => $match->getFullTimeHomeTeamScore(),
                'fullTimeAwayTeamScore' => $match->getFullTimeAwayTeamScore(),
                'extraTimeHomeTeamScore' => $match->getExtraTimeHomeTeamScore(),
                'extraTimeAwayTeamScore' => $match->getExtraTimeAwayTeamScore(),
                'finished' => $finished,
                'winner' => $match->getWinner(),
                'competition' => $match->getRound()->getCompetition()->getId(),
                'competitionName' => $match->getRound()->getCompetition()->getName(),
                'competitionCode' => $match->getRound()->getCompetition()->getCode(),
                'competitionEmblem' => $match->getRound()->getCompetition()->getEmblemUrl(),
                'userPrediction' => $userPrediction,
            ];
        }

        return $this->render('home/index.html.twig', [
            'sortedMatches' => $sortedMatches,
        ]);
    }
}
