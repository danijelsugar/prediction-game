<?php

namespace App\Service;

use App\Entity\Prediction;

class PointService
{
    private const SPOT_ON = 6;
    private const CORRECT_OUTCOME_SCORE_OR_DIFF = 4;
    private const CORRECT_OUTCOME = 3;
    private const ONE_TEAM_SCORE = 1;
    private const NONE = 0;

    public function calculatePoints($match, Prediction $prediction): int
    {
        if ($this->correctScore($match, $prediction)) {
            return self::SPOT_ON;
        } elseif ($this->correctOutcome($match, $prediction) && ($this->oneTeamScore($match, $prediction) || $this->checkGoalDiff($match, $prediction))) {
            return self::CORRECT_OUTCOME_SCORE_OR_DIFF;
        } elseif ($this->correctOutcome($match, $prediction) && !$this->oneTeamScore($match, $prediction)) {
            return self::CORRECT_OUTCOME;
        } elseif (!$this->correctOutcome($match, $prediction) && $this->oneTeamScore($match, $prediction)) {
            return self::ONE_TEAM_SCORE;
        } else {
            return self::NONE;
        }
    }

    private function correctScore($match, Prediction $prediction)
    {
        if (
            $match->score->fullTime->homeTeam === $prediction->getHomeTeamPrediction() &&
            $match->score->fullTime->awayTeam === $prediction->getAwayTeamPrediction()
        ) {
            return true;
        }

        return false;
    }

    private function correctOutcome($match, Prediction $prediction)
    {
        if (
            $match->score->fullTime->homeTeam > $match->score->fullTime->awayTeam &&
            $prediction->getHomeTeamPrediction() > $prediction->getAwayTeamPrediction() ||
            $match->score->fullTime->homeTeam < $match->score->fullTime->awayTeam &&
            $prediction->getHomeTeamPrediction() < $prediction->getAwayTeamPrediction() ||
            $match->score->fullTime->homeTeam === $match->score->fullTime->awayTeam &&
            $prediction->getHomeTeamPrediction() === $prediction->getAwayTeamPrediction()
        ) {
            return true;
        }

        return false;
    }

    private function oneTeamScore($match, Prediction $prediction)
    {
        if (
            $match->score->fullTime->homeTeam === $prediction->getHomeTeamPrediction() ||
            $match->score->fullTime->awayTeam === $prediction->getAwayTeamPrediction()
        ) {
            return true;
        }

        return false;
    }

    private function checkGoalDiff($match, Prediction $prediction)
    {
        $matchDiff = $match->score->fullTime->homeTeam - $match->score->fullTime->awayTeam;
        $predictionDiff = $prediction->getHomeTeamPrediction() - $prediction->getAwayTeamPrediction();
        if ($matchDiff === $predictionDiff) {
            return true;
        }

        return false;
    }
}
