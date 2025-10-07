<?php
namespace App\Controller;

use App\Entity\Enums\DayOfWeek;
use App\Entity\MealPlan;
use App\Entity\Plan;
use App\Form\MealPlanType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MealPlanController extends AbstractController {
    #[Route('/manage-plans/create-meal-plan/{id}/day/{day}', name:'app_create_meal_plan')]
        #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function createMealPlan(Request $req, EntityManagerInterface $em, string $day, Plan $plan):Response {
        $dayEnum = DayOfWeek::tryFrom($day);
        if(!$dayEnum) {
            throw $this->createNotFoundException('Nepostojeći dan u nedelji.');
        }
        $mealPlan = new MealPlan();
        $mealPlan->setDay($dayEnum);
        $mealPlan->setPlan($plan);
        $form = $this->createForm(MealPlanType::class, $mealPlan);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($mealPlan);
            $em->flush();

            return $this->redirectToRoute('app_plan_view', ['id' => $mealPlan->getPlan()->getId()]);
        }
        return $this->render('plans/create-meal-plan.html.twig', [
            'mealPlanForm' => $form,
            'plan' => $plan,
            'day' => $day
        ]);
    }
    #[Route('/manage-plans/delete_meal_plan/{id}', name:'app_del_mp')]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function deleteMp(Request $req, EntityManagerInterface $em, MealPlan $mp):Response {
        $planId = $mp->getPlan()->getId();
        if($this->isCsrfTokenValid('delete'.$mp->getId(), $req->request->get('_token'))){
            $em->remove($mp);
            $em->flush();

            $this->addFlash('success', 'Uspesno obrisan obrok');
        } else {
            $this->addFlash('error', 'Nevalidan csrf token');
        }
        return $this->redirectToRoute('app_plan_view', ['id' => $planId]);
    }
    
}
?>