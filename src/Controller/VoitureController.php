<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VoitureController extends AbstractController
{
    #[Route('/user/voiture/ajouter', name: 'app_voiture_new')]
    #[Route('/user/voiture/modifier/{id}', name: 'app_voiture_edit')]
    public function form(Voiture $voiture = null, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$voiture) {
            $voiture = new Voiture();
            $voiture->setUser($user);
        }

        // Sécurité : on ne peut pas modifier la voiture d'un autre
        if ($voiture->getUser() !== $user) {
            return $this->redirectToRoute('app_user');
        }

        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($voiture);
            $em->flush();

            $this->addFlash('success', 'Véhicule enregistré avec succès !');
            return $this->redirectToRoute('app_user');
        }

        return $this->render('voiture/form.html.twig', [
            'form' => $form->createView(),
            'editMode' => $voiture->getId() !== null
        ]);
    }


    #[Route('/user/voiture/supprimer/{id}', name: 'app_voiture_delete', methods: ['POST'])]
    public function delete(Voiture $voiture, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Sécurité 1 : On vérifie que la voiture appartient bien à l'utilisateur connecté
        if ($voiture->getUser() !== $user) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de supprimer ce véhicule.");
        }

        // Sécurité 2 : Vérification du jeton CSRF (nommé 'delete' + l'id de la voiture)
        if ($this->isCsrfTokenValid('delete' . $voiture->getId(), $request->request->get('_token'))) {
            $em->remove($voiture);
            $em->flush();
            $this->addFlash('success', 'Véhicule supprimé avec succès.');
        } else {
            $this->addFlash('danger', 'Jeton de sécurité invalide.');
        }

        return $this->redirectToRoute('app_user');
    }
}