<?php

namespace App\Controller;

use App\Entity\Enums\DayOfWeek;
use App\Entity\Plan;
use App\Entity\WorkoutPlan;
use App\Form\WorkoutPlanType;
use App\Form\WorkoutPlanWithParamsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class WorkoutPlanController extends AbstractController{

    #[Route('/manage-plans/create-workout-plan/{id}/day/{day}', name:"app_create_workout_plan_params")]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function createWorkoutPlanWithSetDay(Request $req, EntityManagerInterface $em, string $day, Plan $plan) {
        $dayEnum = DayOfWeek::tryFrom($day);
        if($dayEnum === null)
            throw $this->createNotFoundException('Nepostojeći dan u nedelji.');
        $workoutPlan = new WorkoutPlan();

        $workoutPlan->setDay($dayEnum);
        $workoutPlan->setPlan($plan);

        $form = $this->createForm(WorkoutPlanType::class, $workoutPlan);
        $form->handleRequest($req);


        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($workoutPlan);
            $em->flush();

            return $this->redirectToRoute('app_plan_view', ['id' => $plan->getId()]);
        }

        return $this->render('plans/create-workout-plan.html.twig', [
            'workoutPlanForm' => $form,
            'plan' => $plan,
            'day' => $day
        ]);
    }

    #[Route('/manage-plans/delete_workout_plan/{id}', name:"app_del_wp")]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function deleteWp(WorkoutPlan $wp, Request $req, EntityManagerInterface $em,) {
        $planId = $wp->getPlan()->getId();
        if($this->isCsrfTokenValid('delete'.$wp->getId(), $req->request->get('_token'))) {
            $em->remove($wp);
            $em->flush();
            $this->addFlash('success', 'Uspesno obrisan plan vezbi');
        } else {
            $this->addFlash('error', 'Nevalidan CSRF token');
        }
        return $this->redirectToRoute('app_plan_view', ['id' => $planId]);

    }

}
?>