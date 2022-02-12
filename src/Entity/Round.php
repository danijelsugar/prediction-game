<?php

namespace App\Entity;

use App\Repository\RoundRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoundRepository::class)
 */
class Round
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Competition::class, inversedBy="rounds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $competition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateFrom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=RoundMatch::class, mappedBy="round")
     */
    private $roundMatches;

    public function __construct()
    {
        $this->roundMatches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(\DateTimeInterface $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->dateTo;
    }

    public function setDateTo(\DateTimeInterface $dateTo): self
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|RoundMatch[]
     */
    public function getRoundMatches(): Collection
    {
        return $this->roundMatches;
    }

    public function addRoundMatch(RoundMatch $roundMatch): self
    {
        if (!$this->roundMatches->contains($roundMatch)) {
            $this->roundMatches[] = $roundMatch;
            $roundMatch->setRound($this);
        }

        return $this;
    }

    public function removeRoundMatch(RoundMatch $roundMatch): self
    {
        if ($this->roundMatches->removeElement($roundMatch)) {
            // set the owning side to null (unless already changed)
            if ($roundMatch->getRound() === $this) {
                $roundMatch->setRound(null);
            }
        }

        return $this;
    }
}
