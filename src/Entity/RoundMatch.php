<?php

namespace App\Entity;

use App\Repository\RoundMatchRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoundMatchRepository::class)
 */
class RoundMatch
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Round::class, inversedBy="roundMatches")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Round $round;

    /**
     * @ORM\Column(type="integer")
     */
    private int $matchId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $stage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $groupName;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $homeTeamName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $awayTeamName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $fullTimeHomeTeamScore;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $fullTimeAwayTeamScore;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $extraTimeHomeTeamScore;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $extraTimeAwayTeamScore;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $winner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $lastUpdated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRound(): ?Round
    {
        return $this->round;
    }

    public function setRound(?Round $round): self
    {
        $this->round = $round;

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

    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(?string $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(?string $groupName): self
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHomeTeamName(): ?string
    {
        return $this->homeTeamName;
    }

    public function setHomeTeamName(?string $homeTeamName): self
    {
        $this->homeTeamName = $homeTeamName;

        return $this;
    }

    public function getAwayTeamName(): ?string
    {
        return $this->awayTeamName;
    }

    public function setAwayTeamName(?string $awayTeamName): self
    {
        $this->awayTeamName = $awayTeamName;

        return $this;
    }

    public function getFullTimeHomeTeamScore(): ?int
    {
        return $this->fullTimeHomeTeamScore;
    }

    public function setFullTimeHomeTeamScore(?int $fullTimeHomeTeamScore): self
    {
        $this->fullTimeHomeTeamScore = $fullTimeHomeTeamScore;

        return $this;
    }

    public function getFullTimeAwayTeamScore(): ?int
    {
        return $this->fullTimeAwayTeamScore;
    }

    public function setFullTimeAwayTeamScore(?int $fullTimeAwayTeamScore): self
    {
        $this->fullTimeAwayTeamScore = $fullTimeAwayTeamScore;

        return $this;
    }

    public function getExtraTimeHomeTeamScore(): ?int
    {
        return $this->extraTimeHomeTeamScore;
    }

    public function setExtraTimeHomeTeamScore(?int $extraTimeHomeTeamScore): self
    {
        $this->extraTimeHomeTeamScore = $extraTimeHomeTeamScore;

        return $this;
    }

    public function getExtraTimeAwayTeamScore(): ?int
    {
        return $this->extraTimeAwayTeamScore;
    }

    public function setExtraTimeAwayTeamScore(?int $extraTimeAwayTeamScore): self
    {
        $this->extraTimeAwayTeamScore = $extraTimeAwayTeamScore;

        return $this;
    }

    public function getWinner(): ?string
    {
        return $this->winner;
    }

    public function setWinner(?string $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getLastUpdated(): ?string
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(string $lastUpdated): self
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }
}
