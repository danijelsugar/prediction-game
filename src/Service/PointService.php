<?php

namespace App\Service;

use App\Dto\MatchDto;
use App\Entity\Prediction;

class PointService
{
    public function calculatePoints(MatchDto $match, Prediction $prediction): int
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

    private function correctScore(MatchDto $match, Prediction $prediction): bool
    {
        if (
            $match->getFullTimeHomeTeamScore() === $prediction->getHomeTeamPrediction() &&
            $match->getFullTimeAwayTeamScore() === $prediction->getAwayTeamPrediction()
        ) {
            return true;
        }

        return false;
    }

    private function correctOutcome(MatchDto $match, Prediction $prediction): bool
    {
        if (
            $match->getFullTimeHomeTeamScore() > $match->getFullTimeAwayTeamScore() &&
            $prediction->getHomeTeamPrediction() > $prediction->getAwayTeamPrediction() ||
            $match->getFullTimeHomeTeamScore() < $match->getFullTimeAwayTeamScore() &&
            $prediction->getHomeTeamPrediction() < $prediction->getAwayTeamPrediction() ||
            $match->getFullTimeHomeTeamScore() === $match->getFullTimeAwayTeamScore() &&
            $prediction->getHomeTeamPrediction() === $prediction->getAwayTeamPrediction()
        ) {
            return true;
        }

        return false;
    }

    private function oneTeamScore(MatchDto $match, Prediction $prediction): bool
    {
        if (
            $match->getFullTimeHomeTeamScore() === $prediction->getHomeTeamPrediction() ||
            $match->getFullTimeAwayTeamScore() === $prediction->getAwayTeamPrediction()
        ) {
            return true;
        }

        return false;
    }

    private function checkGoalDiff(MatchDto $match, Prediction $prediction): bool
    {
        $matchDiff = $match->getFullTimeHomeTeamScore() - $match->getFullTimeAwayTeamScore();
        $predictionDiff = $prediction->getHomeTeamPrediction() - $prediction->getAwayTeamPrediction();
        if ($matchDiff === $predictionDiff) {
            return true;
        }

        return false;
    }
}
