<?php

namespace App\Controller;

use App\Service\FootballDataService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompetitionController extends AbstractController
{
    /**
     * @Route("competitions/{id}", name="app_competitions")
     */
    public function competitions(FootballDataService $footballDataService, $id): Response
    {
        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );

        $competitionTeams = $footballDataService->fetchData(
            'competitions/' . $id . '/teams',
        );

        return $this->render('competition/index.html.twig', [
            'competitionId' => $id,
            'competitions' => $competitions,
            'competitionTeams' => $competitionTeams,
        ]);
    }

    /**
     * @Route("table/{id}", name="app_table")
     */
    public function standings(Request $request, FootballDataService $footballDataService, $id): Response
    {
        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );

        $standingType = $request->query->get('standingType');
        if ($standingType) {
            $competitionStandings = $footballDataService->fetchData(
                'competitions/' . $id . '/standings',
                [
                    'standingType' => $standingType
                ]
            );
        } else {
            $competitionStandings = $footballDataService->fetchData(
                'competitions/' . $id . '/standings'
            );
        }

        return $this->render('competition/table.html.twig', [
            'competitionId' => $id,
            'competitions' => $competitions,
            'competitionStandings' => $competitionStandings
        ]);
    }

    /**
     * @Route("results/{id}", name="app_results")
     */
    public function results(FootballDataService $footballDataService, $id): Response
    {
        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );

        $competitionResults = $footballDataService->fetchData(
            'competitions/' . $id . '/matches',
            [
                'status' => 'FINISHED'
            ]
        );

        // dump($competitionResults);

        // foreach ($competitionResults->matches as $match) {
        //     dump($match->score);
        // }

        return $this->render('competition/result.html.twig', [
            'competitionId' => $id,
            'competitions' => $competitions,
            'competitionResults' => $competitionResults
        ]);
    }

     /**
      * @Route("schedule/{id}", name="app_schedule")
      */
    public function schedule(FootballDataService $footballDataService, $id): Response
    {
        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );

        $competitionSchedule = $footballDataService->fetchData(
            'competitions/' . $id . '/matches',
            [
                'status' => 'SCHEDULED'
            ]
        );

        dump($competitionSchedule);

        return $this->render('competition/schedule.html.twig', [
            'competitionId' => $id,
            'competitions' => $competitions,
            'competitionSchedule' => $competitionSchedule
        ]);
    }

    /**
     * @Route("/test/{id}", name="app_test")
     */
    public function test(FootballDataService $footballDataService, $id): Response
    {
        $scorers = $footballDataService->fetchData(
            'matches/327148/'
        );

        dd($scorers);

        return $this->json('eeee');
    }
}