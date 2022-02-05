<?php

namespace App\Service;

use App\Entity\Prediction;

class PointsService
{
    public function calculatePoints(array $match, Prediction $prediction): int
    {
        if ($this->correctOutcomeAndScore($match, $prediction)) {
            return 6;
        } elseif ($this->correctOutcome($match, $prediction) && ($this->oneTeamScore($match, $prediction) || $this->checkGoalDiff($match, $prediction))) {
            return 4;
        } elseif ($this->correctOutcome($match, $prediction) && !$this->oneTeamScore($match, $prediction)) {
            return 3;
        } if (!$this->correctOutcome($match, $prediction) && $this->oneTeamScore($match, $prediction)) {
            return 1;
        } else {
            return 0;
        }
    }

    private function correctOutcomeAndScore(array $match, Prediction $prediction)
    {
        if ($match['homeTeamScore'] === $prediction->getHomeTeamPrediction() && $match['awayTeamScore'] === $prediction->getAwayTeamPrediction()) {
            return true;
        }

        return false;
    }

    private function correctOutcome(array $match, Prediction $prediction)
    {
        if ($match['homeTeamScore'] > $match['awayTeamScore'] && $prediction->getHomeTeamPrediction() > $prediction->getAwayTeamPrediction() ||
            $match['homeTeamScore'] < $match['awayTeamScore'] && $prediction->getHomeTeamPrediction() < $prediction->getAwayTeamPrediction() ||
            $match['homeTeamScore'] === $match['awayTeamScore'] && $prediction->getHomeTeamPrediction() === $prediction->getAwayTeamPrediction()) {
            return true;
        }

        return false;
    }

    private function oneTeamScore(array $match, Prediction $prediction)
    {
        if ($match['homeTeamScore'] === $prediction->getHomeTeamPrediction() || $match['awayTeamScore'] === $prediction->getAwayTeamPrediction()) {
            return true;
        }

        return false;
    }

    private function checkGoalDiff(array $match, Prediction $prediction)
    {
        $matchDiff = $match['homeTeamScore'] - $match['awayTeamScore'];
        $predictionDiff = $prediction->getHomeTeamPrediction() - $prediction->getAwayTeamPrediction();
        if ($matchDiff === $predictionDiff) {
            return true;
        }

        return false;
    }
}
