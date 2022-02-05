<?php

namespace App\Controller;

use App\Entity\Prediction;
use App\Service\PointsService;
use App\Service\FootballDataService;
use App\Repository\PredictionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;

class PredictionController extends AbstractController
{
    /**
     * @Route("/competitions/{id}/predictions", name="app_prediction", requirements={"id"="\d{4}"})
     */
    public function predictions(int $id): Response
    {
        return $this->render('prediction/index.html.twig', [
            'competitionId' => $id,
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
        FootballDataService $footballData,
        PredictionRepository $predictionRepository
    ): Response {
        if (is_numeric($round)) {
            $roundMatches = $footballData->fetchData(
                'competitions/'.$id.'/matches',
                [
                    'matchday' => $round,
                ]
            );
        } elseif (is_string($round)) {
            $roundMatches = $footballData->fetchData(
                'competitions/'.$id.'/matches',
                [
                    'stage' => $round,
                ]
            );
        } else {
            throw $this->createNotFoundException('Page not found');
        }

        $matches = [];
        foreach ($roundMatches->matches as $match) {
            $userPrediction = $predictionRepository->findOneBy([
                'user' => $this->getUser(),
                'matchId' => $match->id,
            ]);

            $finished = false;

            $matchDateTime = new \DateTime($match->utcDate);
            $currentDateTime = new \DateTime();

            if ($currentDateTime > $matchDateTime) {
                $finished = true;
            }

            $matches[] = [
                'id' => $match->id,
                'utcDate' => $match->utcDate,
                'homeTeamName' => $match->homeTeam->name,
                'awayTeamName' => $match->awayTeam->name,
                'score' => $match->score,
                'userPrediction' => $userPrediction,
                'finished' => $finished,
            ];
        }

        $roundsMatches = $footballData->fetchData(
            'competitions/'.$id.'/matches'
        );

        $rounds = $footballData->getPredictionRoundsInfo($roundsMatches->matches);
        $rounds = $footballData->getMatchdayDates($rounds);

        $rounds = array_keys($rounds);

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

        $season = $footballData->getSeason($roundsMatches->matches[0]->season);

        return $this->render('prediction/prediction_round.html.twig', [
            'competitionId' => $id,
            'competitionName' => $roundMatches->competition->area->name.' - '.$roundMatches->competition->name.' '.$season,
            'round' => $round,
            'matches' => $matches,
            'rounds' => $rounds,
        ]);
    }

    public function predictionsCache(
        $id,
        FootballDataService $footballData
    ): Response {
        $roundsMatches = $footballData->fetchData(
            'competitions/'.$id.'/matches'
        );

        $rounds = $footballData->getPredictionRoundsInfo($roundsMatches->matches);

        $matchdayDates = $footballData->getMatchdayDates($rounds);

        $firstAndLastMatchdayDate = $footballData->getFirstAndLastMatchdayDate($matchdayDates);

        $roundStatus = $footballData->getRoundStatus($rounds);

        $roundInfo = [];
        foreach ($firstAndLastMatchdayDate as $key => $value) {
            $roundInfo[$key] = ['date' => $value, 'status' => $roundStatus[$key]];
        }

        $season = $footballData->getSeason($roundsMatches->matches[0]->season);

        $response = $this->render('prediction/rounds_cache.html.twig', [
            'competitionId' => $id,
            'competitionName' => $roundsMatches->competition->area->name.' - '.$roundsMatches->competition->name.' '.$season,
            'roundInfo' => $roundInfo,
        ]);

        $response->setPublic();
        $response->setMaxAge(120);

        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }

    public function predictionNavCache(
        $id,
        FootballDataService $footballData
    ): Response {
        $roundsMatches = $footballData->fetchData(
            'competitions/'.$id.'/matches'
        );

        $rounds = $footballData->getPredictionRoundsInfo($roundsMatches->matches);

        $matchdayDates = $footballData->getMatchdayDates($rounds);

        $firstAndLastMatchdayDate = $footballData->getFirstAndLastMatchdayDate($matchdayDates);

        $roundStatus = $footballData->getRoundStatus($rounds);

        $roundInfo = [];
        foreach ($firstAndLastMatchdayDate as $key => $value) {
            $roundInfo[$key] = ['date' => $value, 'status' => $roundStatus[$key]];
        }

        $response = $this->render('prediction/rounds_nav_cache.html.twig', [
            'competitionId' => $id,
            'roundInfo' => $roundInfo,
        ]);

        $response->setPublic();
        $response->setMaxAge(120);

        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
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
        $user = $this->getUser();
        //dd($user);
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

    /**
     * Checks the outcome of prediction and calculates the points earned.
     *
     * @Route("/predictions/check", name="app_predictions_check")
     */
    public function checkPredictions(
        PredictionRepository $predictionRepository,
        FootballDataService $footballData,
        PointsService $pointsService,
        EntityManagerInterface $entityManager
    ): Response {
        $predictions = $predictionRepository->findBy([
            'finished' => false,
            'points' => null,
        ]);

        $competitions = [];
        $startDates = [];

        foreach ($predictions as $prediction) {
            if (!in_array($prediction->getCompetition(), $competitions)) {
                $competitions[] = $prediction->getCompetition();
            }
            $startDates[] = $prediction->getMatchStartTime();
        }

        $minDate = min($startDates)->format('Y-m-d');

        // $matches = $footballData->fetchData(
        //     'matches',
        //     [
        //         'competitions' => implode(',', $competitions),
        //         'status' => 'SCHEDULED'
        //     ]
        // );

        $matches = [
            [
                'id' => 327126,
                'competition' => 2021,
                'homeTeamScore' => 2,
                'awayTeamScore' => 0,
            ],
            [
                'id' => 327128,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 2,
            ],
            [
                'id' => 327130,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 1,
            ],
            [
                'id' => 327117,
                'competition' => 2021,
                'homeTeamScore' => 2,
                'awayTeamScore' => 0,
            ],
            [
                'id' => 327113,
                'competition' => 2021,
                'homeTeamScore' => 3,
                'awayTeamScore' => 1,
            ],
            [
                'id' => 327119,
                'competition' => 2021,
                'homeTeamScore' => 0,
                'awayTeamScore' => 1,
            ],
            [
                'id' => 327120,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 2,
            ],
            [
                'id' => 327122,
                'competition' => 2021,
                'homeTeamScore' => 0,
                'awayTeamScore' => 0,
            ],
            [
                'id' => 327115,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 0,
            ],
            [
                'id' => 327114,
                'competition' => 2021,
                'homeTeamScore' => 2,
                'awayTeamScore' => 4,
            ],
            [
                'id' => 327116,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 0,
            ],
            [
                'id' => 327121,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 2,
            ],
            [
                'id' => 327118,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 1,
            ],
            [
                'id' => 327131,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 1,
            ]
        ];

        foreach ($matches as $match) {
            $prediction = $predictionRepository->findOneBy(
                [
                    'matchId' => $match['id'],
                    'competition' => $match['competition'],
                ]
            );

            if ($prediction) {
                // $matchDiff = $match['homeTeamScore'] - $match['awayTeamScore'];
                // $predictionDiff = $prediction->getHomeTeamPrediction() - $prediction->getAwayTeamPrediction();

                // echo 'Result: '.$match['homeTeamScore'].':'.$match['awayTeamScore'].'<br>';
                // echo 'Match diff: '.$matchDiff.'<br>';
                // echo 'Prediction: '.$prediction->getHomeTeamPrediction().':'.$prediction->getAwayTeamPrediction().'<br>';
                // echo 'Prediction diff: '.$predictionDiff.'<br>';
                $points = $pointsService->calculatePoints($match, $prediction);
                // echo 'Points: '.$points.'<br>';
                // echo '<hr>';
                //dd($match, $prediction, $points);
                $prediction
                    ->setFinished(true)
                    ->setPoints($points);
                
                $entityManager->persist($prediction);
            }
        }
        $entityManager->flush();
        //dd($minDate, $predictions, implode(',', $competitions), $matches);

        return new Response('Finished');
    }
}
