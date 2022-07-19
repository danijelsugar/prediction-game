<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PredictionRepository::class)
 */
class Prediction
{
    public const SPOT_ON = 6;
    public const CORRECT_OUTCOME_SCORE_OR_DIFF = 4;
    public const CORRECT_OUTCOME = 3;
    public const ONE_TEAM_SCORE = 1;
    public const NONE = 0;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="predictions")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=RoundMatch::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?RoundMatch $match;

    /**
     * @ORM\ManyToOne(targetEntity=Competition::class)
     */
    private Competition $competition;

    /**
     * @ORM\Column(type="datetime", nullable="true")
     */
    private ?\DateTime $matchStartTime;

    /**
     * @ORM\Column(type="integer", nullable="true")
     */
    private ?int $homeTeamScore;

    /**
     * @ORM\Column(type="integer", nullable="true")
     */
    private ?int $awayTeamScore;

    /**
     * @ORM\Column(type="integer")
     */
    private int $homeTeamPrediction;

    /**
     * @ORM\Column(type="integer")
     */
    private int $awayTeamPrediction;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $finished = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $points = self::NONE;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMatch(): ?RoundMatch
    {
        return $this->match;
    }

    public function setMatch(?RoundMatch $match): self
    {
        $this->match = $match;

        return $this;
    }

    public function getCompetition(): Competition
    {
        return $this->competition;
    }

    public function setCompetition(Competition $competition): self
    {
        $this->competition = $competition;

        return $this;
    }

    public function getHomeTeamScore(): ?int
    {
        return $this->homeTeamScore;
    }

    public function setHomeTeamScore(int $homeTeamScore): self
    {
        $this->homeTeamScore = $homeTeamScore;

        return $this;
    }

    public function getAwayTeamScore(): ?int
    {
        return $this->awayTeamScore;
    }

    public function setAwayTeamScore(int $awayTeamScore): self
    {
        $this->awayTeamScore = $awayTeamScore;

        return $this;
    }

    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    public function getHomeTeamPrediction(): ?int
    {
        return $this->homeTeamPrediction;
    }

    public function setHomeTeamPrediction(int $homeTeamPrediction): self
    {
        $this->homeTeamPrediction = $homeTeamPrediction;

        return $this;
    }

    public function getAwayTeamPrediction(): ?int
    {
        return $this->awayTeamPrediction;
    }

    public function setAwayTeamPrediction(int $awayTeamPrediction): self
    {
        $this->awayTeamPrediction = $awayTeamPrediction;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): self
    {
        if (!in_array($points, [self::SPOT_ON, self::CORRECT_OUTCOME_SCORE_OR_DIFF, self::CORRECT_OUTCOME, self::ONE_TEAM_SCORE, self::NONE])) {
            $this->points = $points;
        }

        return $this;
    }

    public function getMatchStartTime(): ?\DateTime
    {
        return $this->matchStartTime;
    }

    public function setMatchStartTime(\DateTime $matchStartTime): self
    {
        $this->matchStartTime = $matchStartTime;

        return $this;
    }
}
