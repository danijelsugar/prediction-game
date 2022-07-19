<?php

namespace App\Service;

use App\Entity\Prediction;

class PointService
{
    public function calculatePoints($match, Prediction $prediction): int
    {
        if ($this->correctScore($match, $prediction)) {
            return Prediction::SPOT_ON;
        } elseif ($this->correctOutcome($match, $prediction) && ($this->oneTeamScore($match, $prediction) || $this->checkGoalDiff($match, $prediction))) {
            return Prediction::CORRECT_OUTCOME_SCORE_OR_DIFF;
        } elseif ($this->correctOutcome($match, $prediction) && !$this->oneTeamScore($match, $prediction)) {
            return Prediction::CORRECT_OUTCOME;
        } elseif (!$this->correctOutcome($match, $prediction) && $this->oneTeamScore($match, $prediction)) {
            return Prediction::ONE_TEAM_SCORE;
        } else {
            return Prediction::NONE;
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
