<?php

namespace App\Form;

use App\Entity\Enums\DayOfWeek;
use App\Entity\Plan;
use App\Entity\Workout;
use App\Entity\WorkoutPlan;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class WorkoutPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('workouts', EntityType::class, [
                'class' => Workout::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Izaberite vezbe za ovaj dan',
                'expanded' => true,
                'by_reference' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Morate izabrati barem jednu vezbu'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkoutPlan::class,
        ]);
    }
}
