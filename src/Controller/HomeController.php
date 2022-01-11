<?php

namespace App\Controller;

use App\Service\FootballDataService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(FootballDataService $footballDataService): Response
    {
        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );

        dump($competitions);

        return $this->render('home/index.html.twig', [
            'competitions' => $competitions,
        ]);
    }

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

        return $this->render('home/competition.html.twig', [
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

        return $this->render('home/table.html.twig', [
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

        dump($competitionResults);

        foreach ($competitionResults->matches as $match) {
            dump($match->score);
        }

        return $this->render('home/result.html.twig', [
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

        return $this->render('home/schedule.html.twig', [
            'competitionId' => $id,
            'competitions' => $competitions,
            'competitionSchedule' => $competitionSchedule
        ]);
    }

    /**
     * @Route("/admin", name="app_admin")
     */
    public function admin(): Response
    {
        return new Response('Pretend admin page');
    }

    /**
     * @Route("/admin/login")
     */
    public function adminLogin()
    {
        return new Response('Pretend admin login page, that should be public');
    }

    /**
     * @Route("/question", name="app_question")
     */
    public function question(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController-Question',
        ]);
    }

    /**
     * @Route("/admin/comments")
     */
    public function adminComments()
    {
        $this->denyAccessUnlessGranted('ROLE_COMMENT_ADMIN');

        return new Response('Pretend comments admin page');
    }
}
