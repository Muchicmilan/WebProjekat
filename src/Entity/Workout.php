<?php

namespace App\Entity;

use App\Repository\WorkoutRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: WorkoutRepository::class)]
class Workout
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $sets = null;

    #[ORM\Column]
    private ?int $reps = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $notes = null;

    #[ORM\ManyToMany(targetEntity: WorkoutPlan::class, mappedBy:"workouts")]
    private Collection $workoutPlans;

        public function __construct() {
        $this->workoutPlans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSets(): ?int
    {
        return $this->sets;
    }

    public function setSets(int $sets): static
    {
        $this->sets = $sets;

        return $this;
    }

    public function getReps(): ?int
    {
        return $this->reps;
    }

    public function setReps(int $reps): static
    {
        $this->reps = $reps;

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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getWorkoutPlans(): Collection { return $this->workoutPlans; }

    public function addWorkoutPlan(WorkoutPlan $plan): self {
        if (!$this->workoutPlans->contains($plan)) {
            $this->workoutPlans->add($plan);
            $plan->addWorkout($this);
        }
        return $this;
    }

    public function removeWorkoutPlan(WorkoutPlan $plan): self {
        if ($this->workoutPlans->removeElement($plan)) {
        $plan->removeWorkout($this);
        }
        return $this;
    }
}
