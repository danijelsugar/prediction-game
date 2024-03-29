<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PredictionRepository::class)]
class Prediction
{
    public const SPOT_ON = 6;
    public const CORRECT_OUTCOME_SCORE_OR_DIFF = 4;
    public const CORRECT_OUTCOME = 3;
    public const ONE_TEAM_SCORE = 1;
    public const NONE = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\ManyToOne(inversedBy: 'predictions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false)]
    private ?RoundMatch $match = null;

    #[ORM\ManyToOne()]
    private ?Competition $competition = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $matchStartTime = null;

    #[ORM\Column(nullable: true)]
    private ?int $homeTeamScore = null;

    #[ORM\Column(nullable: true)]
    private ?int $awayTeamScore = null;

    #[ORM\Column]
    private ?int $homeTeamPrediction = null;

    #[ORM\Column]
    private ?int $awayTeamPrediction = null;

    #[ORM\Column]
    private bool $finished = false;

    #[ORM\Column(nullable: true)]
    private ?int $points = null;

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

    public function setMatch(RoundMatch $match): self
    {
        $this->match = $match;

        return $this;
    }

    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    public function setCompetition(?Competition $competition): self
    {
        $this->competition = $competition;

        return $this;
    }

    public function getMatchStartTime(): ?\DateTimeInterface
    {
        return $this->matchStartTime;
    }

    public function setMatchStartTime(?\DateTimeInterface $matchStartTime): self
    {
        $this->matchStartTime = $matchStartTime;

        return $this;
    }

    public function getHomeTeamScore(): ?int
    {
        return $this->homeTeamScore;
    }

    public function setHomeTeamScore(?int $homeTeamScore): self
    {
        $this->homeTeamScore = $homeTeamScore;

        return $this;
    }

    public function getAwayTeamScore(): ?int
    {
        return $this->awayTeamScore;
    }

    public function setAwayTeamScore(?int $awayTeamScore): self
    {
        $this->awayTeamScore = $awayTeamScore;

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

    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): self
    {
        if (in_array($points, [self::SPOT_ON, self::CORRECT_OUTCOME_SCORE_OR_DIFF, self::CORRECT_OUTCOME, self::ONE_TEAM_SCORE, self::NONE])) {
            $this->points = $points;
        }

        return $this;
    }
}
