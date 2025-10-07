<?php
namespace App\Controller;

use App\Entity\Plan;
use App\Repository\MealPlanRepository;
use App\Repository\PlanRepository;
use App\Repository\WorkoutPlanRepository;
use App\Repository\WorkoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Enums\UserRole;

class PlanManagementController extends AbstractController {
    #[Route('/manage-plans',name: 'app_plans_view')]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function listPlans(EntityManagerInterface $em) {
        $planRepo = $em->getRepository(Plan::class);
        $plans = $planRepo->findAll();
        return $this->render('plans/list.html.twig', [
            'allPlans' => $plans
        ]);
    }

    #[Route('/manage-plans/view-plan/{id}',name: 'app_plan_view')]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function viewPlan(int $id, PlanRepository $planRepository) {
        $plan = $planRepository->findWithDetails($id);  
        return $this->render('plans/plan-view.html.twig', [
            'plan' => $plan,
        ]);
    }

}

?>