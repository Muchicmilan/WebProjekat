<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use APP\Entity\Enums\UserRole;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    EntityManagerInterface $entityManager,
    UserRepository $userRepository
    ): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registrationData = $form->getData();
            $plainPassword = $form->get('plainPassword')->getData(); //temp resenje

            //Proveravamo da li korisnik sa email adresom vec postoji
            $email = $registrationData['email'];
            $existingUser = $userRepository->findOneBy(['email' => $email]);

            if($existingUser){
                $error = new FormError('Korisnik sa ovom e-mail adresom vec postoji!');

                $form->get('email')->addError($error);
            }
            //Validacija pada u slucaju da postoj
            if($form->isValid()){
                $tempUser = new User();
                $hashedPassword = $userPasswordHasher->hashPassword(
                    $tempUser,
                    $plainPassword
                );
                $request->getSession()->set('registration_data', $registrationData);
                $request->getSession()->set('hashed_password', $hashedPassword);

            
                return $this->redirectToRoute('app_weight_first_time');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
