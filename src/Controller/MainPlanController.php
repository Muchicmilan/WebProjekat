<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Form\PlanType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
//Klasa bazirana na write funkcije vezano za planove
class MainPlanController extends AbstractController{

    //Logger iz simfonijevog logger bundle-a omogucava instanciranje klase koja ispisuje u fajlu nase
    //custom logove
    private $logger;

    public function __construct(LoggerInterface $li) {
        $this->logger = $li;
    }

    #[Route('/manage-plans/create-main-plan', name:'app_plan_create')]
    //Isgranted atribut nam omogucava da obezbedimo rute, tako da samo odredjeni korisnici mogu pristupiti datu rutu
    #[IsGranted(
        //Expression language nam omogucava u ovoj situaciji da proverimo dal neko poseduje jedan od 2 rola
        //Jer samo sa isGranted mozemo and operator za rolove da koristimo
        new Expression(
            'is_granted("ROLE_TRAINER") or ' .
            'is_granted("ROLE_ADMIN")'
        )
    )]
    public function createMainPlan(EntityManagerInterface $em, Request $req) {
        /**
         * @var \App\Entity\User $user
         */

        //Deklarisemo $user promenljivu kao EntityUser
        //To nam omogucava da kad fetchujemo trenutnog usera preko kontrolera dobijemo odgovarajuci entitet
        $user = $this->getUser();
        $this->logger->info($user->getRole()->value . ' ' . $user->getName() .' '. $user->getSurname() .
         " Pokusava da kreira glavni plan");
        $plan = new Plan();
        //Kreiranje forme metodom iz abstraktnog controllera omogucava nam da naglasimo kako treba forma izgledati
        //Koristeci AbstractType objekta kao i da dodelimo objekat ili promenljivu u kojoj se podaci upisuju
        $form = $this->createForm(PlanType::class, $plan);
        //Ispituje prosledjen Request i pokrece metodu submit()
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($plan); //Ovde koristimo EntityManagerInterface kako bi mogli lakse
            //Komunicirati sa bazom, persist() salje objekte u redu, flush() vrsi upite koji upisuje objekte
            $em->flush();
        $this->logger->info($plan->getPlanName() . " sa id-em: " . $plan->getId() . " Je uspesno kreiran");

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
        /**
         * @var \App\Entity\User $user
         */
        $user = $this->getUser();
        $this->logger->info($user->getRole()->value . ' ' . $user->getName() .' '. $user->getSurname() .
         " Pokusava da obrise plan sa id-em: ". $plan->getId());
        if($this->isCsrfTokenValid('delete'.$plan->getId(), $req->request->get('_token'))) {
            $em->remove($plan);
            $em->flush();
            $this->logger->info('Plan uspesno obrisan');
            $this->addFlash('success', "Uspesno obrisan plan sa id-em " . $plan->getId());
        } else {
            $this->logger->error('Neuspelo brisanje plana sa id-em: '. $plan->getId() . " razlog: nevalidan csrf token");

            $this->addFlash('error', 'Nevalidan csrf token');
        }
        return $this->redirectToRoute('app_plans_view');
    }
    
}

?>