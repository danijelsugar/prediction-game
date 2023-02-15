<?php

namespace App\Controller;

use App\Helper\FootballInterface;
use App\Repository\CompetitionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;

class CompetitionController extends AbstractController
{
    public function __construct(
        private FootballInterface $footballData, 
        private CompetitionRepository $competitionRepository
    ) {
    }

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

    public function competitions(): Response
    {
        $competitions = $this->competitionRepository->findBy([], ['name' => 'ASC']);

        return $this->render('competition/competitions.html.twig', [
            'competitions' => $competitions,
        ]);
    }

    public function competitionTeamsCache(int $id): Response
    {
        try {
            $competitionTeams = $this->footballData->getCompetitionTeams($id);
        } catch (ClientException) {
        }

        $competition = $this->competitionRepository->findOneBy(['competition' => $id]);

        // $season = $this->footballDataService->getSeason($competitionTeams->season->startDate, $competitionTeams->season->endDate);
        $season = '';

        $response = $this->render('competition/teams_cache.html.twig', [
            'competitionTeams' => $competitionTeams ?? null,
            'competitionName' => $competition->getArea().' - '.$competition->getName(),
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
        Request $request
    ): Response {
        $standingType = $request->query->get('standingType');
        if ($standingType) {
            try {
                $competitionStandings = $this->footballData->getCompetitionStandings($id, [
                    'standingType' => $standingType,
                ]);
            } catch (ClientException) {
            }
        } else {
            try {
                $competitionStandings = $this->footballData->getCompetitionStandings($id);
            } catch (ClientException) {
            }
        }
        if (isset($competitionStandings)) {
            // $season = $this->footballDataService->getSeason($competitionStandings->season->startDate, $competitionStandings->season->endDate);
            $season = '';

            $competition = $this->competitionRepository->findOneBy(['competition' => $id]);

            $competitionName = $competition->getArea().' - '.$competition->getName();
        }

        $response = $this->render('competition/table_cache.html.twig', [
            'competitionId' => $id,
            'competitionStandings' => $competitionStandings ?? '',
            'competitionName' => $competitionName ?? '',
            'season' => $season ?? '',
        ]);

        $response->setPublic();
        $response->setMaxAge(600);

        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }

    public function resultsCache(int $id): Response
    {
        try {
            $competitionResults = $this->footballData->getCompetitionMatches($id, [
                'status' => 'FINISHED',
            ]);
        } catch (ClientException) {
        }

        $roundMatches = [];
        if (isset($competitionResults)) {
            foreach ($competitionResults as $match) {
                if (null === $match->getMatchday() || ('GROUP_STAGE' !== $match->getStage() && 'REGULAR_SEASON' !== $match->getStage())) {
                    $roundMatches[$match->getStage()][] = [
                        'utcDate' => $match->getDate(),
                        'matchday' => $match->getMatchday(),
                        'stage' => $match->getStage(),
                        'group' => $match->getGroupName(),
                        'homeTeamName' => $match->getHomeTeamName(),
                        'awayTeamName' => $match->getAwayTeamName(),
                        'homeTeamFullTimeScore' => $match->getFullTimeHomeTeamScore(),
                        'awayTeamFullTimeScore' => $match->getFullTimeAwayTeamScore(),
                        'homeTeamExtraTimeScore' => $match->getExtraTimeHomeTeamScore(),
                        'awayTeamExtraTimeScore' => $match->getExtraTimeAwayTeamScore(),
                    ];
                } else {
                    $roundMatches['Round '.$match->getMatchday()][] = [
                        'utcDate' => $match->getDate(),
                        'matchday' => $match->getMatchday(),
                        'stage' => $match->getStage(),
                        'group' => $match->getGroupName(),
                        'homeTeamName' => $match->getHomeTeamName(),
                        'awayTeamName' => $match->getAwayTeamName(),
                        'homeTeamFullTimeScore' => $match->getFullTimeHomeTeamScore(),
                        'awayTeamFullTimeScore' => $match->getFullTimeAwayTeamScore(),
                        'homeTeamExtraTimeScore' => $match->getExtraTimeHomeTeamScore(),
                        'awayTeamExtraTimeScore' => $match->getExtraTimeAwayTeamScore(),
                    ];
                }
            }

            $competition = $this->competitionRepository->findOneBy(['competition' => $id]);

            $competitionName = $competition->getArea().' - '.$competition->getName();

            if (!empty($competitionResults)) {
                // $season = $this->footballDataService->getSeason($competitionResults->matches[0]->season->startDate, $competitionResults->matches[0]->season->endDate);
                $season = '';
            }
        }

        $response = $this->render('competition/result_cache.html.twig', [
            'roundMatches' => $roundMatches,
            'competitionName' => $competitionName ?? '',
            'season' => $season ?? '',
        ]);

        $response->setPublic();
        $response->setMaxAge(600);

        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }

    public function scheduleCache(int $id): Response
    {
        try {
            $competitionSchedule = $this->footballData->getCompetitionMatches($id, [
                'status' => 'SCHEDULED',
            ]);
        } catch (ClientException) {
        }

        $roundMatches = [];
        if (isset($competitionSchedule)) {
            foreach ($competitionSchedule as $match) {
                if (null === $match->getMatchday() || ('GROUP_STAGE' !== $match->getStage() && 'REGULAR_SEASON' !== $match->getStage())) {
                    $roundMatches[$match->getStage()][] = [
                        'utcDate' => $match->getDate(),
                        'matchday' => $match->getMatchday(),
                        'stage' => $match->getStage(),
                        'group' => $match->getGroupName(),
                        'homeTeamName' => $match->getHomeTeamName(),
                        'awayTeamName' => $match->getAwayTeamName(),
                        'homeTeamFullTimeScore' => $match->getFullTimeHomeTeamScore(),
                        'awayTeamFullTimeScore' => $match->getFullTimeAwayTeamScore(),
                        'homeTeamExtraTimeScore' => $match->getExtraTimeHomeTeamScore(),
                        'awayTeamExtraTimeScore' => $match->getExtraTimeAwayTeamScore(),
                    ];
                } else {
                    $roundMatches['Round '.$match->getMatchday()][] = [
                        'utcDate' => $match->getDate(),
                        'matchday' => $match->getMatchday(),
                        'stage' => $match->getStage(),
                        'group' => $match->getGroupName(),
                        'homeTeamName' => $match->getHomeTeamName(),
                        'awayTeamName' => $match->getAwayTeamName(),
                        'homeTeamFullTimeScore' => $match->getFullTimeHomeTeamScore(),
                        'awayTeamFullTimeScore' => $match->getFullTimeAwayTeamScore(),
                        'homeTeamExtraTimeScore' => $match->getExtraTimeHomeTeamScore(),
                        'awayTeamExtraTimeScore' => $match->getExtraTimeAwayTeamScore(),
                    ];
                }
            }

            $competition = $this->competitionRepository->findOneBy(['competition' => $id]);

            $competitionName = $competition->getArea().' - '.$competition->getName();

            if (!empty($competitionSchedule)) {
                // $season = $this->footballDataService->getSeason($competitionSchedule->matches[0]->season->startDate, $competitionSchedule->matches[0]->season->endDate);
                $season = '';
            }
        }

        $response = $this->render('competition/schedule_cache.html.twig', [
            'roundMatches' => $roundMatches,
            'competitionName' => $competitionName ?? '',
            'season' => $season ?? '',
        ]);

        $response->setPublic();
        $response->setMaxAge(600);

        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }
}
