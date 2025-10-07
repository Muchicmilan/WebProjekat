<?php

namespace App\Entity;

use App\Repository\PlanRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\ErrorHandler\Collecting;
use Symfony\Component\Validator\Constraints\Cascade;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy:'plans')]
    private Collection $users;

    #[ORM\Column(length: 50)]
    private ?string $planName = null;

    #[ORM\OneToMany(mappedBy: 'plan', targetEntity: WorkoutPlan::class, cascade:['persist', 'remove'], orphanRemoval: true)]
    private Collection $workoutPlans;

    #[ORM\OneToMany(mappedBy: 'plan', targetEntity: MealPlan::class,cascade:['persist', 'remove'], orphanRemoval: true)]
    private Collection $mealPlans;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->workoutPlans = new ArrayCollection();
        $this->mealPlans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

        public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addPlan($this);
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removePlan($this);
        }
        return $this;
    }

    public function getPlanName() {
        return $this->planName;
    }

    public function setPlanName(?string $name): static {
        $this->planName = $name;
        return $this;
    }

        public function getWorkoutPlans(): Collection
    {
        return $this->workoutPlans;
    }

    public function addWorkoutPlan(WorkoutPlan $workoutPlan): static
    {
        if (!$this->workoutPlans->contains($workoutPlan)) {
            $this->workoutPlans->add($workoutPlan);
            $workoutPlan->setPlan($this);
        }

        return $this;
    }

    public function removeWorkoutPlan(WorkoutPlan $workoutPlan): static
    {
        $this->workoutPlans->removeElement($workoutPlan);
        return $this;
    }

     public function getMealPlans(): Collection
    {
        return $this->mealPlans;
    }

    public function addMealPlan(MealPlan $mealPlan): static
    {
        if (!$this->mealPlans->contains($mealPlan)) {
            $this->mealPlans->add($mealPlan);
            $mealPlan->setPlan($this);
        }

        return $this;
    }

    public function removeMealPlan(MealPlan $mealPlan): static
    {
        $this->mealPlans->removeElement($mealPlan);
        return $this;
    }

}
