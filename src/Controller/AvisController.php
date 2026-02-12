<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Entity\Covoiturage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AvisRepository;
use Symfony\Component\HttpFoundation\Request;




final class AvisController extends AbstractController
{
   #[Route('/avis/nouveau/{id}', name: 'app_avis_new')]
public function nouveau(Covoiturage $covoiturage, Request $request, EntityManagerInterface $em): Response
{
    // Sécurité supplémentaire : on vérifie le statut du trajet
    if (!in_array($covoiturage->getStatut(), ['Terminé', 'Clôturé'])) {
        $this->addFlash('danger', "Vous ne pouvez pas laisser d'avis avant la fin du trajet.");
        return $this->redirectToRoute('app_user');
    }

    $avis = new Avis();
    $form = $this->createForm(AvisType::class, $avis);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
    $chauffeur = $covoiturage->getVoiture()->getUser();
    $avis->addUser($chauffeur); 
    $avis->setCovoiturage($covoiturage); // lie l'avis au trajet précis
    $avis->setStatut('en attente');

        $em->persist($avis);
        $em->flush();

        $this->addFlash('success', 'Merci ! Votre avis est en cours de modération.');
        // Redirection vers le profil après l'avis
        return $this->redirectToRoute('app_user');
    }

    return $this->render('avis/nouveau.html.twig', [
        'form' => $form->createView(),
        'covoiturage' => $covoiturage
    ]);
}
}

