<?php

namespace App\Entity;

use App\Repository\CompetitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetitionRepository::class)]
class Competition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $competition = null;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $code = null;

    #[ORM\Column]
    private ?string $area = null;

    #[ORM\Column(nullable: true)]
    private ?string $emblemUrl = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $lastUpdated = null;

    #[ORM\OneToMany(targetEntity: Round::class, mappedBy: 'competition')]
    private Collection $rounds;

    public function __construct()
    {
        $this->rounds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(string $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getEmblemUrl(): ?string
    {
        return $this->emblemUrl;
    }

    public function setEmblemUrl(?string $emblemUrl): self
    {
        $this->emblemUrl = $emblemUrl;

        return $this;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(\DateTimeInterface $lastUpdated): self
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * @return Collection|Round[]
     */
    public function getRounds(): Collection
    {
        return $this->rounds;
    }

    public function addRound(Round $round): self
    {
        if (!$this->rounds->contains($round)) {
            $this->rounds[] = $round;
            $round->setCompetition($this);
        }

        return $this;
    }

    public function removeRound(Round $round): self
    {
        if ($this->rounds->removeElement($round)) {
            // set the owning side to null (unless already changed)
            if ($round->getCompetition() === $this) {
                $round->setCompetition(null);
            }
        }

        return $this;
    }
}
