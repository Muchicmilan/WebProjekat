<?php

namespace App\Controller;

use App\Entity\Enums\UserRole;
use App\Entity\UserProgress;
use App\Form\AssignPlanType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserManagementController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route(path: 'admin/users/delete/{id}', name: 'app_delete_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(Request $req, User $user, EntityManagerInterface $entityManager)
    {
        $admin = $this->getUser();
        $this->logger->info('Admin {admin} pokušava da obriše korisnika {user_to_delete}.', [
            'admin' => $admin->getUserIdentifier(),
            'user_to_delete' => $user->getUserIdentifier()
        ]);

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $req->request->get('_token'))) {
            $progressRepo = $entityManager->getRepository(UserProgress::class);
            $userProgressToDelete = $progressRepo->findBy(['user' => $user]);
            foreach ($userProgressToDelete as $progress) {
                $entityManager->remove($progress);
            }

            $entityManager->remove($user);
            $entityManager->flush();

            $this->logger->warning('KORISNIK OBRISAN: Korisnika {deleted_user} je obrisao admin {admin}.', [
                'deleted_user' => $user->getUserIdentifier(),
                'admin' => $admin->getUserIdentifier()
            ]);
            $this->addFlash('success', 'Korisnik uspesno obrisan!');
        } else {
            $this->logger->error('Nevalidan CSRF token za pokušaj brisanja korisnika {user_to_delete} od strane admina {admin}.', [
                'admin' => $admin->getUserIdentifier(),
                'user_to_delete' => $user->getUserIdentifier()
            ]);
            $this->addFlash('error', 'Doslo je do greske pri brisanu korisnika: nevalidan CSRF token');
        }
        return $this->redirectToRoute('app_all_users');
    }

    #[Route(path: '/users/promote/{id}', name: 'app_promote_role', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function promote(Request $req, User $user, EntityManagerInterface $entityManager)
    {
        $admin = $this->getUser();
        if ($this->isCsrfTokenValid('promote' . $user->getId(), $req->request->get('_token'))) {
            if ($user->getRole() === UserRole::USER) {
                $user->setRole(UserRole::TRAINER);
                $entityManager->flush();
                $this->logger->notice('KORISNIK UNAPREĐEN: Korisnik {promoted_user} je unapređen u TRENERA od strane admina {admin}.', [
                    'promoted_user' => $user->getUserIdentifier(),
                    'admin' => $admin->getUserIdentifier()
                ]);
                $this->addFlash('success', "Uspesno izvrsena promocija!");
            } else {
                $this->logger->warning('Admin {admin} je pokušao da unapredi korisnika {user} koji je već trener ili admin.', [
                    'admin' => $admin->getUserIdentifier(),
                    'user' => $user->getUserIdentifier()
                ]);
                $this->addFlash('warning', "Korisnik je vec trener");
            }
        } else {
            $this->logger->error('Nevalidan CSRF token pri pokušaju unapređenja korisnika {user} od strane admina {admin}.', [
                'admin' => $admin->getUserIdentifier(),
                'user' => $user->getUserIdentifier()
            ]);
            $this->addFlash('error', 'Nevalidan CSRF token');
        }
        return $this->redirectToRoute('app_all_users');
    }

    #[Route(path: '/users/demote/{id}', name: 'app_demote_role', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function demote(Request $req, User $user, EntityManagerInterface $entityManager)
    {
        $admin = $this->getUser();
        if ($this->isCsrfTokenValid('demote' . $user->getId(), $req->request->get('_token'))) {
            if ($user->getRole() === UserRole::TRAINER) {
                $user->SetRole(UserRole::USER);
                $entityManager->flush();
                $this->logger->notice('KORISNIK UNAZAĐEN: Korisnik {demoted_user} je unazađen u KORISNIKA od strane admina {admin}.', [
                    'demoted_user' => $user->getUserIdentifier(),
                    'admin' => $admin->getUserIdentifier()
                ]);
                $this->addFlash('success', 'Uspesno smanjena pozicija!');
            } else {
                $this->logger->warning('Admin {admin} je pokušao da unazadi korisnika {user} koji nije trener.', [
                    'admin' => $admin->getUserIdentifier(),
                    'user' => $user->getUserIdentifier()
                ]);
                $this->addFlash('warning', 'Korisnik nije u mogucnosti smanjenje pozicije!');
            }
        } else {
            $this->logger->error('Nevalidan CSRF token pri pokušaju unazađenja korisnika {user} od strane admina {admin}.', [
                'admin' => $admin->getUserIdentifier(),
                'user' => $user->getUserIdentifier()
            ]);
            $this->addFlash('error', "Nevalidan CSRF token");
        }
        return $this->redirectToRoute('app_all_users');
    }

    #[Route(path: '/assign-plan/user/{id}', name: 'app_assign_plan', methods: ['POST', 'GET'])]
    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_ADMIN")'))]
    public function assignPlanToUser(User $user, Request $req, EntityManagerInterface $em)
    {
        $trainer = $this->getUser();
        $this->logger->info('Trener/Admin {trainer} dodeljuje plan korisniku {user}.', [
            'trainer' => $trainer->getUserIdentifier(),
            'user' => $user->getUserIdentifier()
        ]);

        $form = $this->createForm(AssignPlanType::class, $user);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->logger->info('Plan uspešno dodeljen korisniku {user} od strane trenera {trainer}.', [
                'user' => $user->getUserIdentifier(),
                'trainer' => $trainer->getUserIdentifier()
            ]);
            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }

        return $this->render('plans/plan_assign.html.twig', [
            'plansForm' => $form,
            'user' => $user
        ]);
    }
}