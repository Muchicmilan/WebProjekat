<?php

namespace App\Entity;

use App\Entity\Enums\DayOfWeek;
use App\Repository\MealPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MealPlanRepository::class)]
class MealPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type:"string", length: 25, enumType: DayOfWeek::class)]
            private ?DayOfWeek $day = null;

    #[ORM\ManyToMany(targetEntity: Meal::class, inversedBy: "mealPlans")]
    private Collection $meals;  

    #[ORM\ManyToOne(targetEntity: Plan::class)]
    #[ORM\JoinColumn(nullable:false)]
    private ?Plan $plan = null;

    public function __construct() {
        $this->meals = new ArrayCollection();
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

        public function getMeals(): Collection { return $this->meals; }

    public function addMeal(Meal $meal): self {
    if (!$this->meals->contains($meal)) {
        $this->meals->add($meal);
        $meal->addMealPlan($this);
    }
    return $this;
    }

    public function removeMeal(Meal $meal): self {
        if ($this->meals->removeElement($meal)) {
            $meal->removeMealPlan($this);
        }
        return $this;
    }

    public function getPlan(): ?Plan {
        return $this->plan;
    }

    public function setPlan(?Plan $plan):static {
        $this->plan = $plan;
        return $this;
    }
}
