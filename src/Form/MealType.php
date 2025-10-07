<?php

namespace App\Form;

use App\Entity\Meal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

class MealType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Molimo vas napisite ime obroka!"
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => "Ime obroka mora sadrzati ne vise od 50 karaktera"
                    ]),
                ]
            ])
            ->add('protein_g', NumberType::class, [
                'attr' => [
                    'step' => '0.01'
                ],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Tezina mora biti decimalna vrednost!'
                    ]),
                    new Positive([
                        'message' => 'Tezina mora biti pozitivna vrednost!'
                    ]),
                    new LessThanOrEqual([
                        'value' => 999.99,
                        'message' => 'Tezina je prekoracila dozvoljenju vrednost!'
                    ]),
                    new NotBlank([
                        'message' => 'Unseite tezinu!'
                    ])
                ]
            ])
            ->add('carbs_g', NumberType::class, [
                'attr' => [
                    'step' => '0.01'
                ],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Tezina mora biti decimalna vrednost!'
                    ]),
                    new Positive([
                        'message' => 'Tezina mora biti pozitivna vrednost!'
                    ]),
                    new LessThanOrEqual([
                        'value' => 999.99,
                        'message' => 'Tezina je prekoracila dozvoljenju vrednost!'
                    ]),
                    new NotBlank([
                        'message' => 'Unseite tezinu!'
                    ])
                ]
            ])
            ->add('calories_kcal', NumberType::class, [
                'attr' => [
                    'step' => '0.01'
                ],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Tezina mora biti decimalna vrednost!'
                    ]),
                    new Positive([
                        'message' => 'Tezina mora biti pozitivna vrednost!'
                    ]),
                    new LessThanOrEqual([
                        'value' => 999.99,
                        'message' => 'Tezina je prekoracila dozvoljenju vrednost!'
                    ]),
                    new NotBlank([
                        'message' => 'Unseite tezinu!'
                    ])
                ]
            ])
            ->add('fat_g', NumberType::class, [
                'attr' => [
                    'step' => '0.01'
                ],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Tezina mora biti decimalna vrednost!'
                    ]),
                    new Positive([
                        'message' => 'Tezina mora biti pozitivna vrednost!'
                    ]),
                    new LessThanOrEqual([
                        'value' => 999.99,
                        'message' => 'Tezina je prekoracila dozvoljenju vrednost!'
                    ]),
                    new NotBlank([
                        'message' => 'Unseite tezinu!'
                    ])
                ]
            ])
            ->add('recipe', TextareaType::class, [
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
            'data_class' => Meal::class,
        ]);
    }
}
