<?php

namespace App\Form;

use App\Entity\Plan;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('notes', TextareaType::class, [
                'constraints' => [
                    new Length([
                        'max' => 1024,
                        'maxMessage' => "Dodatne informacije o planu ne smeju imati vise od 1024 karaktera"
                    ]),
                ]
            ])
            ->add('planName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Molimo vas napisite ime glavnog plana!"
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => "Ime plana mora sadrzati ne vise od 50 karaktera"
                    ]),
                ]     
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plan::class,
        ]);
    }
}
