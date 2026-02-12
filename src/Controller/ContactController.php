<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\NosqlStatsService;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig');
    }

    //contact
    #[Route('/contact/envoyer', name: 'app_contact_send', methods: ['POST'])]
    public function send(Request $request, NosqlStatsService $nosqlStats): Response
    {
    $nosqlStats->addContactMessage([
        'nom' => $request->request->get('nom'),
        'email' => $request->request->get('email'),
        'message' => $request->request->get('message')
    ]);

        $this->addFlash('success', 'Message envoyé !');
        return $this->redirectToRoute('app_contact');
    }
}
