<?php

namespace App\Form;

use App\Entity\Plan;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AssignPlanType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('plans', EntityType::class, [
                'class' => Plan::class,
                'choice_label' => 'planName',
                'multiple' => true,
                'label' => 'Izaberite planove',
                'expanded' => true,
                'by_reference' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Morate izabrati barem plan'
                    ])
                ]
        ]);
    }
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}

?>