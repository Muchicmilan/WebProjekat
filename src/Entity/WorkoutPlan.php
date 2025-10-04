<?php

namespace App\Entity;

use App\Repository\WorkoutPlanRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enums\DayOfWeek;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: WorkoutPlanRepository::class)]
class WorkoutPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type:"string", length: 25 ,enumType: DayOfWeek::class)]
        private ?DayOfWeek $day = null;

    #[ORM\ManyToMany(targetEntity: Workout::class, inversedBy: "workoutPlans")]
    private Collection $workouts; 
    
    #[ORM\ManyToOne(targetEntity: Plan::class)]
    #[ORM\JoinColumn(nullable:false)]
    private ?Plan $plan = null;

    public function __construct() {
        $this->workouts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay() : ?DayOfWeek {
        return $this->day;
    }
    public function setDay(DayOfWeek $day): static
    {
        $this->day = $day;

        return $this;
    }
    public function getWorkouts(): Collection { return $this->workouts; }

    public function addWorkout(Workout $workout): self {
        if (!$this->workouts->contains($workout)) {
            $this->workouts->add($workout);
            $workout->addWorkoutPlan($this);
        }
        return $this;
    }

    public function removeWorkout(Workout $workout): self {
        if ($this->workouts->removeElement($workout)) {
            $workout->removeWorkoutPlan($this);
        }
        return $this;
    }

    public function getPlan(): ?Plan {
        return $this->plan;
    }

    public function setPlan(Plan $plan):static {
        $this->plan = $plan;
        return $this;
    }
}
