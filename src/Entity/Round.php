<?php

namespace App\Entity;

use App\Repository\RoundRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoundRepository::class)]
class Round
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rounds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Competition $competition = null;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column]
    private ?string $stage = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateFrom = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateTo = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\OneToMany(targetEntity: RoundMatch::class, mappedBy: 'round')]
    private Collection $roundMatches;

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

    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(string $stage): self
    {
        $this->stage = $stage;

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
