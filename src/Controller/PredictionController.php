<?php

namespace App\Controller;

use App\Entity\Prediction;
use App\Entity\User;
use App\Repository\CompetitionRepository;
use App\Repository\PredictionRepository;
use App\Repository\RoundMatchRepository;
use App\Repository\RoundRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PredictionController extends AbstractController
{
    /**
     * @Route("/competitions/{id}/predictions", name="app_prediction", requirements={"id"="\d{4}"})
     */
    public function predictions(int $id, RoundRepository $roundRepository, CompetitionRepository $competitionRepository): Response
    {
        $rounds = $roundRepository->findCompetitionRounds($id);

        $competition = $competitionRepository->findOneBy(
            [
                'competition' => $id,
            ]
        );

        return $this->render('prediction/index.html.twig', [
            'competitionId' => $id,
            'rounds' => $rounds,
            'competitionName' => $competition->getArea().' - '.$competition->getName(),
        ]);
    }

    /**
     * @Route(
     * "competitions/{id}/predictions/round/{round}",
     * name="app_prediction_round",
     * requirements={"id"="\d{4}"})
     */
    public function predictionsRound(
        int $id,
        $round,
        PredictionRepository $predictionRepository,
        RoundMatchRepository $roundMatchRepository,
        RoundRepository $roundRepository,
        CompetitionRepository $competitionRepository
    ): Response {
        $roundMatches = $roundMatchRepository->findRoundMatches($id, $round);

        $competition = $competitionRepository->findOneBy(
            [
                'competition' => $id,
            ]
        );

        $matches = [];
        foreach ($roundMatches as $roundMatch) {
            $userPrediction = $predictionRepository->findOneBy(
                [
                    'user' => $this->getUser(),
                    'matchId' => $roundMatch['matchId'],
                ]
            );

            $finished = false;

            $matchDateTime = new \DateTime($roundMatch['date']->format('Y-m-d'));
            $currentDateTime = new \DateTime();

            if ($currentDateTime > $matchDateTime) {
                $finished = true;
            }

            $matches[] = [
                'id' => $roundMatch['matchId'],
                'round' => $roundMatch['round'],
                'competition' => $roundMatch['competition'],
                'date' => $roundMatch['date'],
                'homeTeamName' => $roundMatch['homeTeamName'],
                'awayTeamName' => $roundMatch['awayTeamName'],
                'fullTimeHomeTeamScore' => $roundMatch['fullTimeHomeTeamScore'],
                'fullTimeAwayTeamScore' => $roundMatch['fullTimeAwayTeamScore'],
                'extraTimeHomeTeamScore' => $roundMatch['extraTimeHomeTeamScore'],
                'extraTimeAwayTeamScore' => $roundMatch['extraTimeAwayTeamScore'],
                'finished' => $finished,
                'winner' => $roundMatch['winner'],
                'userPrediction' => $userPrediction,
            ];
        }

        $competitionRounds = $roundRepository->findCompetitionRounds($id);

        $rounds = [];
        foreach ($competitionRounds as $competitionRound) {
            $rounds[] = $competitionRound['name'];
        }

        $currentRoundKey = array_search($round, $rounds);

        if (false === $currentRoundKey) {
            throw $this->createNotFoundException('Page not found');
        }

        if (!array_key_exists($currentRoundKey, $rounds)) {
            throw $this->createNotFoundException('Page not found');
        }

        $prevRound = $currentRoundKey - 1;
        $prevRound = \key_exists($prevRound, $rounds) ? $rounds[$prevRound] : null;

        $nextRound = $currentRoundKey + 1;
        $nextRound = \key_exists($nextRound, $rounds) ? $rounds[$nextRound] : null;

        $rounds = ['prev' => $prevRound, 'next' => $nextRound];

        return $this->render('prediction/prediction_round.html.twig', [
            'competitionId' => $id,
            'round' => $round,
            'competitionName' => $competition->getArea().' - '.$competition->getName(),
            'matches' => $matches,
            'rounds' => $rounds,
        ]);
    }

    public function predictionNavCache(
        int $id,
        RoundRepository $roundRepository
    ): Response {
        $rounds = $roundRepository->findCompetitionRounds($id);

        return $this->render('prediction/rounds_nav.html.twig', [
            'competitionId' => $id,
            'rounds' => $rounds,
        ]);
    }

    /**
     * @Route("/predictions/save", name="app_predictions_save", methods={"POST"})
     */
    public function savePrediction(
        Request $request,
        EntityManagerInterface $entityManager,
        PredictionRepository $predictionRepository
    ): Response {
        $data = $request->request->get('data');
        $data = json_decode($data);
        /** @var User|null */
        $user = $this->getUser();

        foreach ($data as $d) {
            $previousPrediction = $predictionRepository->findOneBy([
                'user' => $user,
                'matchId' => $d->match,
            ]);

            if (!$previousPrediction) {
                $matchStartTime = new \DateTime($d->startTime);

                $prediction = new Prediction();
                $prediction
                    ->setUser($user)
                    ->setMatchId($d->match)
                    ->setCompetition($d->competition)
                    ->setMatchStartTime($matchStartTime)
                    ->setHomeTeamPrediction($d->homeTeam)
                    ->setAwayTeamPrediction($d->awayTeam);
                $entityManager->persist($prediction);
            } else {
                $previousPrediction->setHomeTeamPrediction($d->homeTeam);
                $previousPrediction->setAwayTeamPrediction($d->awayTeam);
                $entityManager->persist($previousPrediction);
            }
        }
        $entityManager->flush();

        return $this->json(['result' => 'success']);
    }
}
