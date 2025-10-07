<?php
namespace App\Controller;

use App\Entity\Enums\UserRole;
use App\Entity\User;
use App\Entity\UserProgress;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
class UserListController extends AbstractController {
    #[Route(path: '/users', name:'app_all_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function listAllUsers(UserRepository $userRepository) {
        $allUsers = $userRepository->findEveryUser();

        return $this->render('users/user_list.html.twig', [
            'all_users' => $allUsers
        ]);
    }
    #[Route(path: '/assign-plan', name:'app_users_trainer_view')]
    #[IsGranted(
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function listUsersForTrainer(EntityManagerInterface $em) {
        $userRepo = $em->getRepository(User::class);
        $allUsers = $userRepo->findBy(['role' => UserRole::USER]);
        return $this->render('users/user_plan_list.html.twig', [
            'all_users' => $allUsers
        ]);
    }
    #[Route(path: '/user/profile/{id}', name:'app_user_profile')]
    public function listUser(EntityManagerInterface $em, User $user) {
        $porgressRepo = $em->getRepository(UserProgress::class);
        $usersProgres = $porgressRepo->findBy(['user' => $user]);
        return $this->render('users/profile.html.twig', [
            'user' => $user,
            'progress' => $usersProgres
        ]);
    }
}

?>