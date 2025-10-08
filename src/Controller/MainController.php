<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
//Abstrakt kontroler se koristi u svim controllerima jer implementira vazne metode poput createForm,
//Osim toga omogucava da naglasi symfoniju da radi sa kontrolerom
final class MainController extends AbstractController
{
    //Podesavamo rutu i vezujemo metodu koja se izvrsava kada neko ode na tu rutu
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        $user = $this->getUser();
        //render je metoda iz abstrakt kontrolera koja nam omogucava da povezemo templejt sa kontrolerom
        //i takodje posaljemo promenljive koje zelimo da se ucitaju na templejtu
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'user' => $user
        ]);
    }
}
