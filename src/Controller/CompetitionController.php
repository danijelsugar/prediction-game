<?php

namespace App\Controller;

use App\Service\FootballDataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompetitionController extends AbstractController
{
    /**
     * @Route("competitions/{id}/teams", name="app_competition_teams", requirements={"id"="\d{4}"})
     */
    public function competitionTeams(int $id): Response 
    {
        return $this->render('competition/teams.html.twig', [
            'competitionId' => $id,
        ]);
    }

    /**
     * @Route("competitions/{id}/table", name="app_table", requirements={"id"="\d{4}"})
     */
    public function standings(int $id): Response 
    {
        return $this->render('competition/table.html.twig', [
            'competitionId' => $id,
        ]);
    }

    /**
     * @Route("competition/{id}/results", name="app_results", requirements={"id"="\d{4}"}) 
     */
    public function results(int $id): Response 
    {
        return $this->render('competition/result.html.twig', [
            'competitionId' => $id
        ]);
    }

     /**
      * @Route("competitions/{id}/schedule", name="app_schedule", requirements={"id"="\d{4}"})
      */
    public function schedule(int $id ): Response 
    {
        return $this->render('competition/schedule.html.twig', [
            'competitionId' => $id
        ]);
    }

    public function competitionsCache(FootballDataInterface $footballDataService): Response
    {
        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );
        
        $response = $this->render('competition/competitions.html.twig', [
            'competitions' => $competitions
        ]);
        
        $response->setPublic();
        $response->setMaxAge(120);

        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    public function competitionTeamsCache(
        $id, 
        FootballDataInterface $footballData
    ) : Response {

        $competitionTeams = $footballData->fetchData(
            'competitions/' . $id . '/teams',
        );

        $season = $footballData->getSeason($competitionTeams->season);

        $response = $this->render('competition/teams_cache.html.twig', [
            'competitionTeams' => $competitionTeams,
            'competitionName' => $competitionTeams->competition->area->name . ' - ' . $competitionTeams->competition->name,
            'season' => $season
        ]);

        $response->setPublic();
        $response->setMaxAge(120);

        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    public function standingsCache(
        $id, 
        Request $request, 
        FootballDataInterface $footballData
    ) : Response {

        $standingType = $request->query->get('standingType');
        if ($standingType) {
            $competitionStandings = $footballData->fetchData(
                'competitions/' . $id . '/standings',
                [
                    'standingType' => $standingType
                ]
            );
        } else {
            $competitionStandings = $footballData->fetchData(
                'competitions/' . $id . '/standings'
            );
        }

        $season = $footballData->getSeason($competitionStandings->season);

        $response = $this->render('competition/table_cache.html.twig', [
            'competitionStandings' => $competitionStandings,
            'competitionName' => $competitionStandings->competition->area->name . ' - ' . $competitionStandings->competition->name,
            'season' => $season
        ]);

        $response->setPublic();
        $response->setMaxAge(120);

        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    public function resultsCache(
        $id,
        FootballDataInterface $footballData
    ): Response {

        $competitionResults = $footballData->fetchData(
            'competitions/' . $id . '/matches',
            [
                'status' => 'FINISHED'
            ]
        );

        $season = $footballData->getSeason($competitionResults->matches[0]->season);

        $response = $this->render('competition/result_cache.html.twig', [
            'competitionResults' => $competitionResults,
            'competitionName' => $competitionResults->competition->area->name . ' - ' . $competitionResults->competition->name,
            'season' => $season
        ]);

        $response->setPublic();
        $response->setMaxAge(120);

        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    public function scheduleCache(
        $id,
        FootballDataInterface $footballData
    ): Response {
        $competitionSchedule = $footballData->fetchData(
            'competitions/' . $id . '/matches',
            [
                'status' => 'SCHEDULED'
            ]
        );

        $season = $footballData->getSeason($competitionSchedule->matches[0]->season);

        $response = $this->render('competition/schedule_cache.html.twig', [
            'competitionSchedule' => $competitionSchedule,
            'competitionName' => $competitionSchedule->competition->area->name . ' - ' . $competitionSchedule->competition->name,
            'season' => $season
        ]);

        $response->setPublic();
        $response->setMaxAge(120);

        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}