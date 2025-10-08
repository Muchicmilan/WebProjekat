<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository
    ): Response {
        if($this->getUser())
            return $this->redirectToRoute('app_homepage');
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registrationData = $form->getData();
            $plainPassword = $form->get('plainPassword')->getData();

            $email = $registrationData['email'];
            $existingUser = $userRepository->findOneBy(['email' => $email]);

            if ($existingUser) {
                $this->logger->warning('Pokušaj registracije sa postojećom email adresom: {email}.', ['email' => $email]);
                $error = new FormError('Korisnik sa ovom e-mail adresom vec postoji!');
                $form->get('email')->addError($error);
            }

            if ($form->isValid()) {
                $this->logger->info('Početni korak registracije uspešan za {email}. Nastavlja se na sledeći korak.', ['email' => $email]);
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