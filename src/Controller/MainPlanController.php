<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Form\PlanType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainPlanController extends AbstractController{
    #[Route('/manage-plans/create-main-plan', name:'app_plan_create')]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function createMainPlan(EntityManagerInterface $em, Request $req) {
        $plan = new Plan();
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($plan);
            $em->flush();

            return $this->redirectToRoute('app_plans_view');
        }
        return $this->render('plans/create-main-plan.html.twig', [
            'planForm' => $form
        ]);
    }
    #[Route('/manage-plans/delete-main-plan/{id}', name:'app_plan_delete')]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function deletePlan(Plan $plan, Request $req, EntityManagerInterface $em) {
        if($this->isCsrfTokenValid('delete'.$plan->getId(), $req->request->get('_token'))) {
            $em->remove($plan);
            $em->flush();

            $this->addFlash('success', "Uspesno obrisan plan");
        } else {
            $this->addFlash('error', 'Nevalidan csrf token');
        }
        return $this->redirectToRoute('app_plans_view');
    }
    
}

?>