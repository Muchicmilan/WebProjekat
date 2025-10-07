<?php

namespace App\Form;

use App\Entity\Enums\DayOfWeek;
use App\Entity\Meal;
use App\Entity\MealPlan;
use App\Entity\Plan;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MealPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('meals', EntityType::class, [
                'class' => Meal::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Izaberite obroke za ovaj dan',
                'expanded' => true,
                'by_reference' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Morate izabrati barem jedan obrok'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MealPlan::class,
        ]);
    }
}
