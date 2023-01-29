<?php

namespace App\Helper;

use App\Dto\CompetitionDto;
use App\Dto\MatchDto;

interface FootballInterface
{
    public function fetchData(string $uri, array $filters = []);

    /**
     * @return CompetitionDto[]
     */
    public function getCompetitions(array $filters = []): array;

    /**
     * @return MatchDto[]
     */
    public function getCompetitionMatches($competition, array $filters = []): array;
}
