<?php

namespace App\Controller;

use App\Entity\Enums\UserRole;
use App\Form\WeightType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\UserProgress;
use App\Entity\User;
#[Route('/weight')]
final class WeightController extends AbstractController
{   
    #[Route('-first-register', 'app_weight_first_time')]
    public function weightRegister(Request $request,UserPasswordHasherInterface $userPasswordHasher ,EntityManagerInterface $entityManager): Response {
        $registrationData = $request->getSession()->get('registration_data');

        if(!$registrationData) {
            $this->addFlash('warning', 'Molimo vas unesite prethodno vazne podatke');
            return $this->redirectToRoute('app_register');
        }

        $form = $this->createForm(WeightType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $weightData = $form->getData();

            $user = new User();
            $user->setEmail($registrationData['email']);
            $user->setName($registrationData['name']);
            $user->setSurname($registrationData['surname']);
            $user->setHeight($registrationData['height']);
            $user->setRole(UserRole::GUEST);
            $user->setPasswordHash($userPasswordHasher->hashPassword(
                $user,
                $registrationData['plainPassword']
            ));

            $userStartProgress = new UserProgress();
            $userStartProgress->setUser($user);
            $userStartProgress->setWeightKg($weightData['weight_kg']);
            $userStartProgress->setDate(new DateTime());

            $entityManager->persist($user);
            $entityManager->persist($userStartProgress);
            $entityManager->flush();

            $request->getSession()->remove('registration_data');

            return $this->redirectToRoute('app_homepage');
        }
        
        return $this->render('weight/index.html.twig', [
            'weightForm' => $form,
        ]);
    }
}
