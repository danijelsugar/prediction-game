<?php

namespace App\Helper;

use App\Dto\CompetitionDto;
use App\Dto\MatchDto;
use App\Dto\StandingDto;
use App\Dto\TeamDto;

interface FootballInterface
{
    /**
     * List all available competitions.
     *
     * @param array<string, string> $filters
     *
     * @return CompetitionDto[]
     */
    public function getCompetitions(array $filters = []): array;

    /**
     * Show Standings for a particular competition.
     *
     * @param array<string, string> $filters
     *
     * @return StandingDto[]
     */
    public function getCompetitionStandings(int $competition, array $filters = []): array;

    /**
     * List all teams for a particular competition.
     *
     * @param array<string, string> $filters
     *
     * @return TeamDto[]
     */
    public function getCompetitionTeams(int $competition, array $filters = []): array;

    /**
     * List all matches for a particular competition.
     *
     * @param array<string, string> $filters
     *
     * @return MatchDto[]
     */
    public function getCompetitionMatches(int $competition, array $filters = []): array;

    /**
     * Show one particular match.
     */
    public function getMatch(int $match): MatchDto;

    /**
     * List matches across (a set of) competitions.
     *
     * @param array<string, string> $filters
     *
     * @return MatchDto[]
     */
    public function getMatches(array $filters = []): array;
}
