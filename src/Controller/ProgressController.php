<?php

namespace App\Controller;

use App\Entity\Enums\UserRole;
use App\Form\ProgressType;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\UserProgress;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/progress')]
final class ProgressController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('-first-register', 'app_weight_first_time')]
    public function weightRegister(Request $request, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser())
            $this->redirectToRoute('app_homepage');
        $registrationData = $request->getSession()->get('registration_data');
        $hashedPassword = $request->getSession()->get('hashed_password');

        if (!$registrationData) {
            $this->logger->warning('Korisnik je pristupio finalnom koraku registracije bez prethodnih podataka. Preusmeravanje.');
            $this->addFlash('warning', 'Molimo vas unesite prethodno vazne podatke');
            return $this->redirectToRoute('app_register');
        }

        $this->logger->info('Korisnik {email} je na finalnom koraku registracije (unos težine).', ['email' => $registrationData['email']]);

        $form = $this->createForm(ProgressType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $weightData = $form->getData()->getWeightKg();

            $user = new User();
            $user->setEmail($registrationData['email']);
            $user->setName($registrationData['name']);
            $user->setSurname($registrationData['surname']);
            $user->setHeight($registrationData['height']);
            $user->setRole(UserRole::USER);
            $user->setPassword($hashedPassword);

            $userStartProgress = new UserProgress();
            $userStartProgress->setUser($user);
            $userStartProgress->setWeightKg($weightData);
            $userStartProgress->setDate(new DateTime());

            $entityManager->persist($user);
            $entityManager->persist($userStartProgress);
            $entityManager->flush();

            $this->logger->notice('NOVI KORISNIK REGISTROVAN: {email}.', ['email' => $user->getEmail()]);

            $request->getSession()->remove('registration_data');
            $request->getSession()->remove('hashed_password');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('weight/index.html.twig', [
            'weightForm' => $form,
        ]);
    }

    #[Route('personal-view', name: 'app_pers_prog_view')]
    #[IsGranted('ROLE_USER')]
    public function viewPersonalProgress(EntityManagerInterface $em)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->logger->warning('Neautentifikovan korisnik je pokušao da pristupi stranici sa ličnim napretkom.');
            return $this->redirect('app_login');
        }

        $this->logger->info('Korisnik {user} pregleda svoj lični napredak.', ['user' => $user->getUserIdentifier()]);

        $usersProgress = $em
            ->getRepository(UserProgress::class)
            ->findBy(['user' => $user], ['date' => 'DESC']);

        return $this->render('users/user_personal_progress.html.twig', [
            'user' => $user,
            'progress' => $usersProgress
        ]);
    }

    #[Route('update-progress', name: 'app_pers_prog_upd')]
    #[IsGranted('ROLE_USER')]
    public function updateProgress(EntityManagerInterface $em, Request $req)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $this->logger->info('Korisnik {user} ažurira svoj napredak.', ['user' => $user->getUserIdentifier()]);

        $userProgress = new UserProgress();
        $form = $this->createForm(ProgressType::class, $userProgress);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $userProgress->setDate(new DateTime());
            $userProgress->setUser($user);
            $em->persist($userProgress);
            $em->flush();

            $this->logger->info('Korisnik {user} je uspešno ažurirao svoj napredak sa težinom od {weight} kg.', [
                'user' => $user->getUserIdentifier(),
                'weight' => $userProgress->getWeightKg()
            ]);

            return $this->redirectToRoute('app_pers_prog_view');
        }

        return $this->render('weight/index.html.twig', [
            'weightForm' => $form
        ]);
    }
}