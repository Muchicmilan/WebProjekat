<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserProgress;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

class ProgressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('weight_kg', NumberType::class, [
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserProgress::class,
        ]);
    }
}
