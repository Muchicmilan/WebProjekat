<?php

    namespace App\Entity;

    use App\Repository\MealRepository;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\DBAL\Types\Types;
    use Doctrine\ORM\Mapping as ORM;

    #[ORM\Entity(repositoryClass: MealRepository::class)]
    class Meal
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null;

        #[ORM\Column(length: 45)]
        private ?string $name = null;

        #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
        private ?string $protein_g = null;

        #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
        private ?string $carbs_g = null;

        #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
        private ?string $calories_kcal = null;

        #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
        private ?string $fat_g = null;

        #[ORM\ManyToMany(targetEntity: MealPlan::class, mappedBy:"meals")]
        private Collection $mealPlans;

        #[ORM\Column(type: Types::TEXT)]
        private ?string $recipe = null;

        public function __construct() {
            $this->mealPlans = new ArrayCollection();
        }

        public function getId(): ?int
        {
            return $this->id;
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

        public function getProteinG(): ?string
        {
            return $this->protein_g;
        }

        public function setProteinG(string $protein_g): static
        {
            $this->protein_g = $protein_g;

            return $this;
        }

        public function getCarbsG(): ?string
        {
            return $this->carbs_g;
        }

        public function setCarbsG(string $carbs_g): static
        {
            $this->carbs_g = $carbs_g;

            return $this;
        }

        public function getCaloriesKcal(): ?string
        {
            return $this->calories_kcal;
        }

        public function setCaloriesKcal(string $calories_kcal): static
        {
            $this->calories_kcal = $calories_kcal;

            return $this;
        }

        public function getFatG(): ?string
        {
            return $this->fat_g;
        }

        public function setFatG(string $fat_g): static
        {
            $this->fat_g = $fat_g;

            return $this;
        }

        public function getMealPlans(): Collection { return $this->mealPlans; }

            public function getRecipe(): ?string
        {
            return $this->recipe;
        }

        public function setRecipe(string $recipe): static
        {
            $this->recipe = $recipe;

            return $this;
        }

        public function addMealPlan(MealPlan $mealPlan): self {
    if (!$this->mealPlans->contains($mealPlan)) {
        $this->mealPlans->add($mealPlan);
        $mealPlan->addMeal($this);
    }
    return $this;
}

    public function removeMealPlan(MealPlan $mealPlan): self {
    if ($this->mealPlans->removeElement($mealPlan)) {
        $mealPlan->removeMeal($this);
    }
    return $this;
}
    }
