<?php

namespace App\Form;

use App\Entity\User;
//use Composer\Pcre\Regex;
//use Doctrine\DBAL\Types\DecimalType;
//use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Molimo vas napisite vase ime!"
                    ]),
                    new Length([
                        'max' => 40,
                        'maxMessage' => "Ime sme sadrzati maksimalno 40 karaktera"
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{L}]+$/u',
                        'message' => "Ime moze sadrzati samo slova"
                    ])
                ]
            ])
            ->add('surname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Molimo vas napisite vase prezime!"
                    ]),
                    new Length([
                        'max' => 40,
                        'maxMessage' => "Prezime sme sadrzati maksimalno 40 karaktera"
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{L}]+$/u',
                        'message' => "Prezime moze sadrzati samo slova"
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Molimo vas unesite vasu e-mail adresu!"
                    ]),
                    new Email([
                        'message' => "E-mail adresu koju ste uneli nije validna!"
                    ]),
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Molimo vas unesite lozinku!',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Vasa sifra mora sadrzati barem {{ limit }} karaktera!',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('height', NumberType::class, [
                'attr' => [
                    'step' => '0.01'
                ],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Vrednost koju ste uneli nije broj'
                    ]),
                    new Positive([
                        'message' => 'Vrednost koju ste uneli nije pozitivna'
                    ]),
                    new LessThanOrEqual([
                        'value' => 999.99,
                        'message' => 'Visina ne sme biti veca od 999.99'
                    ])
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => User::class,
        ]);
    }
}
