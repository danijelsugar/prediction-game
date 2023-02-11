<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column()
     */
    private ?int $id = null;

    /**
     * @ORM\Column(length=100, nullable=true)
     */
    private ?string $firstName = null;

    /**
     * @ORM\Column(length=100, nullable=true)
     */
    private ?string $lastName = null;

    /**
     * @ORM\Column(length=100, unique=true)
     */
    private ?string $username = null;

    /**
     * @ORM\Column(length=100, unique=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(length=100)
     */
    private ?string $password = null;

    /**
     * Non-mapped field.
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="json")
     *
     * @var string[]
     */
    private array $roles = [];

    /**
     * @ORM\Column()
     */
    private bool $isVerified = false;

    /**
     * @ORM\Column()
     */
    private int $points = 0;

    /**
     * @ORM\OneToMany(targetEntity=Prediction::class, mappedBy="user")
     */
    private Collection $predictions;

    public function __construct()
    {
        $this->predictions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    /**
     * @return Collection|Prediction[]
     */
    public function getPredictions(): Collection
    {
        return $this->predictions;
    }

    public function addPrediction(Prediction $prediction): self
    {
        if (!$this->predictions->contains($prediction)) {
            $this->predictions[] = $prediction;
            $prediction->setUser($this);
        }

        return $this;
    }

    public function removePrediction(Prediction $prediction): self
    {
        if ($this->predictions->removeElement($prediction)) {
            // set the owning side to null (unless already changed)
            if ($prediction->getUser() === $this) {
                $prediction->setUser(null);
            }
        }

        return $this;
    }
}
