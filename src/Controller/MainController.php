<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
//Abstrakt kontroler se koristi u svim controllerima jer implementira vazne metode poput createForm,
//Osim toga omogucava da naglasy symfoniju da radi sa kontrolerom
final class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'user' => $user
        ]);
    }
}
