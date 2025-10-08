<?php

namespace App\Controller;

use App\Entity\Enums\UserRole;
use App\Entity\User;
use App\Entity\UserProgress;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
//Klasa vezana samo za read funkcije user entiteta
class UserListController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route(path: '/users', name: 'app_all_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function listAllUsers(UserRepository $userRepository)
    {
        $this->logger->info('Admin {admin} pregleda listu svih korisnika.', ['admin' => $this->getUser()->getUserIdentifier()]);
        $allUsers = $userRepository->findEveryUser();

        return $this->render('users/user_list.html.twig', [
            'all_users' => $allUsers
        ]);
    }

    #[Route(path: '/assign-plan', name: 'app_users_trainer_view')]
    #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_ADMIN")'))]
    public function listUsersForTrainer(Request $req, UserRepository $userRepo)
    {
        $this->logger->info('Trener/Admin {user} pregleda listu korisnika za dodelu plana.', ['user' => $this->getUser()->getUserIdentifier()]);
        $searchTerm = $req->query->get('q');
        $allUsers = $userRepo->findUserSearch($searchTerm);
        return $this->render('users/user_plan_list.html.twig', [
            'all_users' => $allUsers
        ]);
    }

    #[Route(path: '/user/profile/{id}', name: 'app_user_profile')]
    public function listUser(EntityManagerInterface $em, User $user)
    {
        $this->logger->info('Korisnik {viewer} pregleda profil korisnika {profile_user}.', [
            'viewer' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'Anoniman',
            'profile_user' => $user->getUserIdentifier()
        ]);
        $porgressRepo = $em->getRepository(UserProgress::class);
        $usersProgres = $porgressRepo->findBy(['user' => $user]);
        return $this->render('users/profile.html.twig', [
            'user' => $user,
            'progress' => $usersProgres
        ]);
    }
}