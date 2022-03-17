<?php

namespace App\Controller;

use App\Repository\CompetitionRepository;
use App\Service\FootballDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;

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
            'competitionId' => $id,
        ]);
    }

    /**
     * @Route("competitions/{id}/schedule", name="app_schedule", requirements={"id"="\d{4}"})
     */
    public function schedule(int $id): Response
    {
        return $this->render('competition/schedule.html.twig', [
            'competitionId' => $id,
        ]);
    }

    public function competitions(CompetitionRepository $competitionRepository): Response
    {
        $competitions = $competitionRepository->findBy([], ['name' => 'ASC']);

        return $this->render('competition/competitions.html.twig', [
            'competitions' => $competitions,
        ]);
    }

    public function competitionTeamsCache(
        int $id,
        FootballDataService $footballData
    ): Response {
        $competitionTeams = $footballData->fetchData(
            'competitions/'.$id.'/teams',
        );

        $season = $footballData->getSeason($competitionTeams->season);

        $response = $this->render('competition/teams_cache.html.twig', [
            'competitionTeams' => $competitionTeams,
            'competitionName' => $competitionTeams->competition->area->name.' - '.$competitionTeams->competition->name,
            'season' => $season,
        ]);

        $response->setPublic();
        $response->setMaxAge(600);

        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }

    public function standingsCache(
        int $id,
        Request $request,
        FootballDataService $footballData
    ): Response {
        $standingType = $request->query->get('standingType');
        if ($standingType) {
            try {
                $competitionStandings = $footballData->fetchData(
                    'competitions/'.$id.'/standings',
                    [
                        'standingType' => $standingType,
                    ]
                );
            } catch (ClientException $e) {
            }
        } else {
            try {
                $competitionStandings = $footballData->fetchData(
                    'competitions/'.$id.'/standings'
                );
            } catch (ClientException $e) {
            }
        }

        if (isset($competitionStandings)) {
            $season = $footballData->getSeason($competitionStandings->season);
            $competitionName = $competitionStandings->competition->area->name.' - '.$competitionStandings->competition->name;
        }

        $response = $this->render('competition/table_cache.html.twig', [
            'competitionId' => $id,
            'competitionStandings' => isset($competitionStandings) ? $competitionStandings : '',
            'competitionName' => isset($competitionName) ? $competitionName : '',
            'season' => isset($season) ? $season : '',
        ]);

        $response->setPublic();
        $response->setMaxAge(600);

        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }

    public function resultsCache(
        int $id,
        FootballDataService $footballData
    ): Response {
        try {
            $competitionResults = $footballData->fetchData(
                'competitions/'.$id.'/matches',
                [
                    'status' => 'FINISHED',
                ]
            );
        } catch (ClientException $e) {
        }

        $roundMatches = [];
        if (isset($competitionResults)) {
            foreach ($competitionResults->matches as $match) {
                if (null === $match->matchday || ('GROUP_STAGE' !== $match->stage && 'REGULAR_SEASON' !== $match->stage)) {
                    $roundMatches[$match->stage][] = [
                        'utcDate' => $match->utcDate,
                        'matchday' => $match->matchday,
                        'stage' => $match->stage,
                        'group' => $match->group,
                        'homeTeamName' => $match->homeTeam->name,
                        'awayTeamName' => $match->awayTeam->name,
                        'homeTeamFullTimeScore' => $match->score->fullTime->homeTeam,
                        'awayTeamFullTimeScore' => $match->score->fullTime->awayTeam,
                        'homeTeamHalfTimeScore' => $match->score->halfTime->homeTeam,
                        'awayTeamHalfTimeScore' => $match->score->halfTime->awayTeam,
                    ];
                } else {
                    $roundMatches['Round '.$match->matchday][] = [
                        'utcDate' => $match->utcDate,
                        'matchday' => $match->matchday,
                        'stage' => $match->stage,
                        'group' => $match->group,
                        'homeTeamName' => $match->homeTeam->name,
                        'awayTeamName' => $match->awayTeam->name,
                        'homeTeamFullTimeScore' => $match->score->fullTime->homeTeam,
                        'awayTeamFullTimeScore' => $match->score->fullTime->awayTeam,
                        'homeTeamHalfTimeScore' => $match->score->halfTime->homeTeam,
                        'awayTeamHalfTimeScore' => $match->score->halfTime->awayTeam,
                    ];
                }
            }

            $competitionName = $competitionResults->competition->area->name.' - '.$competitionResults->competition->name;

            if (!empty($competitionResults->matches)) {
                $season = $footballData->getSeason($competitionResults->matches[0]->season);
            }
        }

        $response = $this->render('competition/result_cache.html.twig', [
            'roundMatches' => $roundMatches,
            'competitionName' => isset($competitionName) ? $competitionName : '',
            'season' => isset($season) ? $season : '',
        ]);

        $response->setPublic();
        $response->setMaxAge(600);

        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }

    public function scheduleCache(
        int $id,
        FootballDataService $footballData
    ): Response {
        try {
            $competitionSchedule = $footballData->fetchData(
                'competitions/'.$id.'/matches',
                [
                    'status' => 'SCHEDULED',
                ]
            );
        } catch (ClientException $e) {
        }

        $roundMatches = [];
        if (isset($competitionSchedule)) {
            foreach ($competitionSchedule->matches as $match) {
                if (null === $match->matchday || ('GROUP_STAGE' !== $match->stage && 'REGULAR_SEASON' !== $match->stage)) {
                    $roundMatches[$match->stage][] = [
                        'utcDate' => $match->utcDate,
                        'matchday' => $match->matchday,
                        'stage' => $match->stage,
                        'group' => $match->group,
                        'homeTeamName' => $match->homeTeam->name,
                        'awayTeamName' => $match->awayTeam->name,
                        'homeTeamFullTimeScore' => $match->score->fullTime->homeTeam,
                        'awayTeamFullTimeScore' => $match->score->fullTime->awayTeam,
                        'homeTeamHalfTimeScore' => $match->score->halfTime->homeTeam,
                        'awayTeamHalfTimeScore' => $match->score->halfTime->awayTeam,
                    ];
                } else {
                    $roundMatches['Round '.$match->matchday][] = [
                        'utcDate' => $match->utcDate,
                        'matchday' => $match->matchday,
                        'stage' => $match->stage,
                        'group' => $match->group,
                        'homeTeamName' => $match->homeTeam->name,
                        'awayTeamName' => $match->awayTeam->name,
                        'homeTeamFullTimeScore' => $match->score->fullTime->homeTeam,
                        'awayTeamFullTimeScore' => $match->score->fullTime->awayTeam,
                        'homeTeamHalfTimeScore' => $match->score->halfTime->homeTeam,
                        'awayTeamHalfTimeScore' => $match->score->halfTime->awayTeam,
                    ];
                }
            }

            $competitionName = $competitionSchedule->competition->area->name.' - '.$competitionSchedule->competition->name;

            if (!empty($competitionSchedule->matches)) {
                $season = $footballData->getSeason($competitionSchedule->matches[0]->season);
            }
        }

        $response = $this->render('competition/schedule_cache.html.twig', [
            'roundMatches' => $roundMatches,
            'competitionName' => isset($competitionName) ? $competitionName : '',
            'season' => isset($season) ? $season : '',
        ]);

        $response->setPublic();
        $response->setMaxAge(600);

        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }
}
