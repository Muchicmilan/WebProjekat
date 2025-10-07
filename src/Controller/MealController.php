<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Form\MealType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MealController extends AbstractController {
    #[Route(path: '/manage-plans/create-meal', name: 'app_plans_meal')]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function createMeal(EntityManagerInterface $em, Request $req): Response {
        $meal = new Meal();
        $form = $this->createForm(MealType::class, $meal);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($meal);
            $em->flush();

            $retUrl = $req->query->get('return_url', $req->query->get('return_url'));
            if($retUrl)
                return $this->redirect($retUrl);

            return $this->redirect('app_plans_views');
        }

        return $this->render('plans/create-meal.html.twig', [
            'mealForm' => $form->createView(),
            'return_url' => $req->query->get('return_url')
        ]);

    }
}

?>