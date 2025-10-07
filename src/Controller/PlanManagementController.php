<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Repository\PlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Enums\UserRole;

class PlanManagementController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/manage-plans', name: 'app_plans_view')]
    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_ADMIN")'))]
    public function listPlans(EntityManagerInterface $em)
    {
        $this->logger->info('Trener/Admin {user} lista sve dostupne planove.', ['user' => $this->getUser()->getUserIdentifier()]);
        $planRepo = $em->getRepository(Plan::class);
        $plans = $planRepo->findAll();
        return $this->render('plans/list.html.twig', [
            'allPlans' => $plans
        ]);
    }

    #[Route('/manage-plans/view-plan/{id}', name: 'app_plan_view_details')]
    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_ADMIN")'))]
    public function viewPlan(int $id, PlanRepository $planRepository)
    {
        $this->logger->info('Trener/Admin {user} pregleda detalje za plan sa ID-jem {plan_id}.', [
            'user' => $this->getUser()->getUserIdentifier(),
            'plan_id' => $id
        ]);
        $plan = $planRepository->findWithDetails($id);
        return $this->render('plans/plan-view.html.twig', [
            'plan' => $plan,
        ]);
    }

    #[Route('/my-plans', name: 'app_user_plans')]
    #[IsGranted('ROLE_USER')]
    public function userPlan(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $this->logger->info('Korisnik {user} pregleda svoje lične planove.', ['user' => $user->getUserIdentifier()]);

        if (!$user) {
            $this->logger->warning('Neautentifikovan korisnik je pokušao da pristupi stranici sa ličnim planovima.');
            return $this->redirect('app_login');
        }

        $plans = $user->getPlans();

        return $this->render('users/user_personal_plans.html.twig', [
            'plans' => $plans
        ]);
    }

    #[Route('/view-plan/{id}', name: 'app_plan_view')]
    #[IsGranted('ROLE_USER')]
    public function viewPersonalPlan(Plan $plan)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $this->logger->info('Korisnik {user} pokušava da pregleda lični plan sa ID-jem {plan_id}.', [
            'user' => $user->getUserIdentifier(),
            'plan_id' => $plan->getId()
        ]);

        if (!$user->getPlans()->contains($plan) && !$this->isGranted('ROLE_TRAINER')) {
            $this->logger->error('PRISTUP ODBIJEN: Korisnik {user} je pokušao da pristupi planu koji mu nije dodeljen (ID: {plan_id}).', [
                'user' => $user->getUserIdentifier(),
                'plan_id' => $plan->getId()
            ]);
            throw $this->createAccessDeniedException('Nemate pristup ovom planu.');
        }

        return $this->render('users/user_personal_plan_view.html.twig', [
            'plan' => $plan
        ]);
    }
}