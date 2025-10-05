<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enums\UserRole;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//Koristimo interfejsove iz symfony security-bundle, kako bi implementirali bezbednose metode
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $name = null;

    #[ORM\Column(length: 40)]
    private ?string $surname = null;

    #[ORM\Column(length: 70, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type:"string", length:10, enumType: UserRole::class)]
    private ?UserRole $role=null;

    #[ORM\Column(type: TYPES::DECIMAL, precision:5, scale:2)]
    private ?string $height = null;

    #[ORM\ManyToMany(targetEntity: Plan::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_plan')]
    private Collection $plans;

    public function __construct()
    {
        $this->plans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }


    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password_hash): static
    {
        $this->password = $password_hash;

        return $this;
    }

    public function getRole() : ?UserRole {
        return $this->role;
    }
    public function setRole(UserRole $role): static
    {
        $this->role = $role;

        return $this;
    }

        public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(string $height): static
    {
        $this->height = $height;

        return $this;
    }

        public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(Plan $plan): self
    {
        if (!$this->plans->contains($plan)) {
            $this->plans->add($plan);
            $plan->addUser($this);
        }
        return $this;
    }

    public function removePlan(Plan $plan): self
    {
        if ($this->plans->removeElement($plan)) {
            $plan->removeUser($this);
        }
        return $this;
    }

    public function getRoles(): array {
        $roles = ['ROLE_USER'];

        if($this->role !== null)
            $roles[] = 'ROLE_' . strtoupper($this->role->value);

        return array_unique($roles);
    }

    public function eraseCredentials(): void {
        return;
    }

    public function getUserIdentifier() : string {
        return (string) $this->email;
    }

}
