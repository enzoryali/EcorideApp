<?php

namespace App\Controller;

use App\Form\SearchTrajetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // On initialise le formulaire
        $form = $this->createForm(SearchTrajetType::class);

        return $this->render('home/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }
}