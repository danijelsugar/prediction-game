<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PredictionRepository::class)
 */
class Prediction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="predictions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $matchId;

    /**
     * @ORM\Column(type="integer")
     */
    private $competition;

    /**
     * @ORM\Column(type="datetime", nullable="true")
     */
    private $matchStartTime;

    /**
     * @ORM\Column(type="integer", nullable="true")
     */
    private $homeTeamScore;

    /**
     * @ORM\Column(type="integer", nullable="true")
     */
    private $awayTeamScore;

    /**
     * @ORM\Column(type="integer")
     */
    private $homeTeamPrediction;

    /**
     * @ORM\Column(type="integer")
     */
    private $awayTeamPrediction;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finished = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $points;

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

    public function getMatchId(): ?int
    {
        return $this->matchId;
    }

    public function setMatchId(int $matchId): self
    {
        $this->matchId = $matchId;

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

    public function getCompetition(): ?int
    {
        return $this->competition;
    }

    public function setCompetition(int $competition): self
    {
        $this->competition = $competition;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getMatchStartTime(): ?\DateTimeInterface
    {
        return $this->matchStartTime;
    }

    public function setMatchStartTime(\DateTimeInterface $matchStartTime): self
    {
        $this->matchStartTime = $matchStartTime;

        return $this;
    }
}
