<?php
namespace App\Controller;

use App\Entity\Plan;
use App\Entity\Workout;
use App\Form\WorkoutType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

    class WorkoutController extends AbstractController {
        #[Route(path: '/manage-plans/create-workout',name: 'app_plans_workout')]
        #[IsGranted(new Expression('is_granted("ROLE_TRAINER") or is_granted("ROLE_ADMIN")'))]
        public function createWorkout(Request $req,
        EntityManagerInterface $em,): Response {
            $workout = new Workout();
            $form = $this->createForm(WorkoutType::class, $workout);
            $form->handleRequest($req);
            if($form->isSubmitted() && $form->isValid()) {
                $em->persist($workout);
                $em->flush();

                $retUrl = $req->query->get('return_url', $req->query->get('return_url'));
                if($retUrl) {
                    return $this->redirect($retUrl);
                }
                return $this->redirectToRoute('app_plans_view');

            }
            return $this->render('plans/create-workout.html.twig', [
                'workoutForm' => $form->createView(),
                'return_url' => $req->query->get('return_url')
            ]);
        }
    }

?>