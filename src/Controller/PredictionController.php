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
    public function predictions(
        int $id,
        RoundRepository $roundRepository,
        CompetitionRepository $competitionRepository
    ): Response {
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

        /** @var User|null */
        $user = $this->getUser();

        $matches = [];
        foreach ($roundMatches as $roundMatch) {
            $userPrediction = null;

            if ($user) {
                $userPrediction = $predictionRepository->findPrediction($user, $roundMatch->getMatchId());
            }

            $finished = false;

            $matchDateTime = new \DateTime($roundMatch->getDate()->format('Y-m-d H:i'));
            $currentDateTime = new \DateTime();

            if ($currentDateTime > $matchDateTime) {
                $finished = true;
            }

            $matches[] = [
                'id' => $roundMatch->getMatchId(),
                'date' => $roundMatch->getDate(),
                'homeTeamName' => $roundMatch->getHomeTeamName(),
                'awayTeamName' => $roundMatch->getAwayTeamName(),
                'fullTimeHomeTeamScore' => $roundMatch->getFullTimeHomeTeamScore(),
                'fullTimeAwayTeamScore' => $roundMatch->getFullTimeAwayTeamScore(),
                'extraTimeHomeTeamScore' => $roundMatch->getExtraTimeHomeTeamScore(),
                'extraTimeAwayTeamScore' => $roundMatch->getExtraTimeAwayTeamScore(),
                'finished' => $finished,
                'winner' => $roundMatch->getWinner(),
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
        PredictionRepository $predictionRepository,
        RoundMatchRepository $roundMatchRepository,
        CompetitionRepository $competitionRepository
    ): Response {
        $predictionData = $request->request->get('data');
        $predictionData = json_decode($predictionData);

        /** @var User|null */
        $user = $this->getUser();

        if (!$user) {
            return $this->json('Something went wrong, sign up and try again.', 400);
        }

        if (!$predictionData) {
            return $this->json('No predictions entered!', 400);
        }

        $predictionsEntered = count($predictionData);

        $validPredictions = 0;

        foreach ($predictionData as $data) {
            $matchStartTime = new \DateTime($data->startTime);
            $currentDateTime = new \DateTime();

            if ($currentDateTime >= $matchStartTime) {
                continue;
            }

            if (!is_numeric($data->homeTeam) || !is_numeric($data->awayTeam) || !is_numeric($data->match) || !is_numeric($data->competition)) {
                continue;
            }

            $match = $roundMatchRepository->findOneBy(
                [
                    'id' => $data->match,
                ]
            );

            $competition = $competitionRepository->findOneBy(
                [
                    'id' => $data->competition,
                ]
            );

            if (!$match || !$competition) {
                continue;
            }

            $previousPrediction = $predictionRepository->findPrediction($user, $match->getMatchId());

            if (!$previousPrediction) {
                $prediction = new Prediction();
                $prediction
                    ->setUser($user)
                    ->setMatch($match)
                    ->setCompetition($competition)
                    ->setMatchStartTime($matchStartTime)
                    ->setHomeTeamPrediction($data->homeTeam)
                    ->setAwayTeamPrediction($data->awayTeam);
                $entityManager->persist($prediction);
            } else {
                $previousPrediction->setHomeTeamPrediction($data->homeTeam);
                $previousPrediction->setAwayTeamPrediction($data->awayTeam);
                $entityManager->persist($previousPrediction);
            }
            ++$validPredictions;
        }
        $entityManager->flush();

        if ($predictionsEntered > $validPredictions) {
            return $this->json(
                sprintf(
                    '%s of %s predictions saved successfully. Predictions can be entered before the start of the match, only numbers allowed as score prediction.',
                    $validPredictions,
                    $predictionsEntered
                ), 200
            );
        }

        return $this->json('Predictions saved successfully.', 200);
    }
}
