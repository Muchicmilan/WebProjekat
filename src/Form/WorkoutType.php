<?php

namespace App\Form;

use App\Entity\Workout;
use App\Entity\WorkoutPlan;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

class WorkoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sets', NumberType::class, [
                'constraints' =>[
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Vrednost koju ste uneli nije broj'
                    ]),
                    new Positive([
                        'message' => 'Vrednost koju ste uneli nije pozitivna'
                    ]),
                ]
            ])
            ->add('reps', NumberType::class, [
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Vrednost koju ste uneli nije broj'
                    ]),
                    new Positive([
                        'message' => 'Vrednost koju ste uneli nije pozitivna'
                    ]),
                ]
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Molimo vas napisite ime vezbe!"
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => "Ime vezbe mora sadrzati ne vise od 50 karaktera"
                    ]),
                ]     
            ])
            ->add('notes', TextareaType::class, [
                'constraints' => [
                    new Length([
                        'max' => 1024,
                        'maxMessage' => "Dodatne informacije o vezbi ne smeju imati vise od 1024 karaktera"
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Workout::class,
        ]);
    }
}
