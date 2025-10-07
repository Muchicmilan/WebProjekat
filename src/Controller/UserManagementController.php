<?php

namespace App\Controller;

use App\Entity\Enums\UserRole;
use App\Entity\UserProgress;
use App\Form\AssignPlanType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserManagementController extends AbstractController {
    #[Route(path: 'admin/users/delete/{id}', name: 'app_delete_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(Request $req, User $user, EntityManagerInterface $entityManager) {
        if($this->isCsrfTokenValid('delete'.$user->getId(), $req->request->get('_token'))) {
            //Ovakav pristup ako u buducnosti planiramo da arhiviramo podatke
            $progressRepo = $entityManager->getRepository(UserProgress::class);
            $userProgressToDelete = $progressRepo->findBy(['user' => $user]);
            foreach($userProgressToDelete as $progress)
                $entityManager->remove($progress);

            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Korisnik uspesno obrisan!');
        } else {
            $this->addFlash('error', 'Doslo je do greske pri brisanu korisnika: nevalidan CSRF token');
        }
        return $this->redirectToRoute('app_all_users');

    }

    #[ROUTE(path: '/users/promote/{id}', name: 'app_promote_role', methods:['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function promote(
        Request $req,
        User $user,
        EntityManagerInterface $entityManager,) {
            if($this->isCsrfTokenValid('promote'.$user->getId(), $req->request->get('_token'))){
                if($user->getRole() === UserRole::USER) {
                    $user->setRole(UserRole::TRAINER);
                    $entityManager->flush();
                    $this->addFlash('success',"Uspesno izvrsena promocija!");
                } else {
                    $this->addFlash('warning', "Korisnik je vec trener");
                }
            } else {
                $this->addFlash('error', 'Nevalidan CSRF token');
            }
            return $this->redirectToRoute('app_all_users');
    }

    #[ROUTE(path: '/users/demote/{id}', name:'app_demote_role', methods:['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function demote(
        Request $req,
        User $user,
        EntityManagerInterface $entityManager
    ) {
        if($this->isCsrfTokenValid('demote'.$user->getId(), $req->request->get('_token'))) {
            if($user->getRole() === UserRole::TRAINER) {
                $user->SetRole(UserRole::USER);
                $entityManager->flush();
                $this->addFlash('success', 'Uspesno smanjena pozicija!');
            } else {
                $this->addFlash('warning', 'Korisnik nije u mogucnosti smanjenje pozicije!');
            }
        } else {
            $this->addFlash('error', "Nevalidan CSRF token");
        }
        return $this->redirectToRoute('app_all_users');
    }
    #[ROUTE(path: '/assign-plan/user/{id}', name:'app_assign_plan', methods:['POST', 'GET'])]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function assignPlanToUser(User $user, Request $req, EntityManagerInterface $em) {
        $form = $this->createForm(AssignPlanType::class, $user);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }
        return $this->render('plans/plan_assign.html.twig', [
            'plansForm' => $form,
            'user' => $user
        ]);
    }
}

?>