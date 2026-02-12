<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Form\CovoiturageType;
use App\Form\SearchTrajetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CovoiturageRepository;
use App\Service\NosqlStatsService;

final class CovoiturageController extends AbstractController
{
  #[Route('/covoiturage', name: 'app_covoiturage')]
public function new(Request $request, EntityManagerInterface $em): Response
{
    // 1. On récupère le chauffeur connecté
    /** @var \App\Entity\User $chauffeur */
    $chauffeur = $this->getUser();

    // 2. Vérification : le chauffeur a-t-il les 2 crédits requis pour publier ?
    if (!$chauffeur || $chauffeur->getCredit() < 2) {
        $this->addFlash('danger', 'Crédits insuffisants : il vous faut 2 crédits pour publier un voyage.');
        return $this->redirectToRoute('app_user');
    }

    // Sécurité véhicule (US 9 : Un véhicule doit être sélectionné)
    if ($chauffeur->getVoitures()->isEmpty()) {
        $this->addFlash('danger', 'Vous devez d\'abord enregistrer une voiture.');
        return $this->redirectToRoute('app_user');
    }

    $covoiturage = new Covoiturage();
    $form = $this->createForm(CovoiturageType::class, $covoiturage, [
        'user' => $chauffeur
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $voiture = $covoiturage->getVoiture();
        
        // On récupère la capacité du véhicule choisi
        if ($voiture) {
            $covoiturage->setNbPlace($voiture->getNbPlaces());
        }

        // --- LOGIQUE US 9 : Déduction des 2 crédits du compte CHAUFFEUR ---
        $nouveauSolde = $chauffeur->getCredit() - 2;
        $chauffeur->setCredit($nouveauSolde);

        // Paramètres par défaut
        $covoiturage->setStatut('Ouvert');

        // Calcul durée (Heure Arrivée - Heure Départ)
        $hDepart = $covoiturage->getHeureDepart();
        $hArrivee = $covoiturage->getHeureArrivee();
        if ($hDepart && $hArrivee) {
            $interval = $hDepart->diff($hArrivee);
            $covoiturage->setDuree(($interval->h * 60) + $interval->i); 
        }

        $em->persist($covoiturage);
        // On persiste aussi les modifications sur le chauffeur (le crédit)
        $em->persist($chauffeur); 
        
        $em->flush();

        $this->addFlash('success', 'Voyage publié ! 2 euros ont été prélevés de votre compte.');
        return $this->redirectToRoute('app_user');
    }

    return $this->render('covoiturage/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

    
    
   #[Route('/covoiturages', name: 'app_covoiturage_index')]
public function index(CovoiturageRepository $repository, Request $request): Response
{
    $form = $this->createForm(SearchTrajetType::class);
    
    
    $form->handleRequest($request);
    $searchArray = $request->query->all('search_trajet');

    // On nettoie les données pour éviter les chaînes vides ""
    $depart = !empty($searchArray['depart']) ? $searchArray['depart'] : null;
    $arrivee = !empty($searchArray['arrivee']) ? $searchArray['arrivee'] : null;
    $date = !empty($searchArray['date']) ? $searchArray['date'] : null;
    $prixMax = (isset($searchArray['prixMax']) && $searchArray['prixMax'] !== '') ? (float)$searchArray['prixMax'] : null;
    $dureeMax = (isset($searchArray['dureeMax']) && $searchArray['dureeMax'] !== '') ? (int)$searchArray['dureeMax'] : null;
    $noteMin = (isset($searchArray['noteMin']) && $searchArray['noteMin'] !== '') ? (int)$searchArray['noteMin'] : null;
    $estEcologique = isset($searchArray['estEcologique']);

    // Si on a au moins UN filtre, on utilise findByFilters
    if ($depart || $arrivee || $date || $prixMax || $dureeMax || $noteMin || $estEcologique) {
        $trajets = $repository->findByFilters($depart, $arrivee, $date, $prixMax, $dureeMax, $noteMin, $estEcologique);
    } else {
        $trajets = $repository->findBy(['statut' => 'Ouvert']);
    }

    return $this->render('covoiturage/index.html.twig', [
        'trajets' => $trajets,
        'searchForm' =>$form->createView(),
       
        
    ]);
    
    }

    #[Route('/covoiturage/{id}', name: 'app_covoiturage_show', methods: ['GET'])]
    public function show(Covoiturage $covoiturage): Response
    {
    
    return $this->render('covoiturage/show.html.twig', [
        'covoiturage' => $covoiturage,
    ]);
    
    }


    #[Route('/covoiturage/reserver/{id}', name: 'app_covoiturage_reserve')]
    public function reserve(Covoiturage $covoiturage, EntityManagerInterface $em, NosqlStatsService $nosqlStats): Response
    {
        $user = $this->getUser(); // Le passager connecté

        // 1. Vérifier si l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour réserver.');
            return $this->redirectToRoute('app_login');
        }

        // 2. Sécurité : Empêcher le chauffeur de réserver son propre trajet
        $chauffeur = $covoiturage->getVoiture()->getUser();
        if ($user === $chauffeur) {
            $this->addFlash('warning', 'Vous ne pouvez pas réserver votre propre trajet.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // 3. Vérifier s'il reste des places
        if ($covoiturage->getNbPlace() <= 0) {
            $this->addFlash('danger', 'Désolé, ce trajet est complet.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // 4. Vérifier si l'utilisateur n'est pas déjà inscrit
        if ($covoiturage->getUsers()->contains($user)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à ce trajet.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // 5. LOGIQUE DE TRANSACTION (Crédits)
        // Vérifier si le passager a assez de crédits
        if ($user->getCredit() < $covoiturage->getPrixPersonne()) {
            $this->addFlash('danger', 'Crédits insuffisants pour cette réservation.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // Débiter le passager et créditer le chauffeur
        $user->setCredit($user->getCredit() - $covoiturage->getPrixPersonne());
        $chauffeur->setCredit($chauffeur->getCredit() + $covoiturage->getPrixPersonne());

        // 6. Procéder à la réservation technique
        $covoiturage->addUser($user);
        $covoiturage->setNbPlace($covoiturage->getNbPlace() - 1); // Décrémenter les places

        // 7. Si le nombre de place tombe à 0, on change le statut
        if ($covoiturage->getNbPlace()===0){
            $covoiturage->setStatut('Complet');
        }

        $em->flush();
        $nosqlStats->addStat($covoiturage->getPrixPersonne());//stats JSON

        $this->addFlash('success', 'Votre place a été réservée ! Vos crédits ont été mis à jour.');
        return $this->redirectToRoute('app_user');
    }

    #[Route('/covoiturage/annuler/{id}', name: 'app_covoiturage_annuler', methods: ['POST'])]
public function annuler(Covoiturage $trajet, EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    $chauffeur = $trajet->getVoiture()->getUser();

    if ($user === $chauffeur) {
        // Le chauffeur annule tout le trajet
        $trajet->setStatut('Annulé');
        foreach ($trajet->getUsers() as $passager) {
            // Remboursement passagers
            $passager->setCredit($passager->getCredit() + $trajet->getPrixPersonne());
        }
        $this->addFlash('danger', 'Trajet annulé. Passagers remboursés.');
    } else {
        // Un passager se désiste
        $trajet->removeUser($user);
        $trajet->setNbPlace($trajet->getNbPlace() + 1); // Libère la place
        $user->setCredit($user->getCredit() + $trajet->getPrixPersonne()); // Rend l'argent
        $this->addFlash('success', 'Annulation confirmée, vos crédits ont été rendus.');
    }

    $em->flush();
    return $this->redirectToRoute('app_user');
}

#[Route('/covoiturage/statut/{id}/{action}', name: 'app_covoiturage_statut')]
public function changeStatut(Covoiturage $trajet, string $action, EntityManagerInterface $em): Response
{
    /** @var User $user */
    $user = $this->getUser();
    
    // Sécurité : Seul le propriétaire de la voiture peut démarrer/arrêter
    if ($trajet->getVoiture()->getUser() !== $user) {
        throw $this->createAccessDeniedException();
    }

    if ($action === 'demarrer' && $trajet->getStatut() === 'Ouvert') {
        $trajet->setStatut('En cours');
        $this->addFlash('info', 'Bonne route ! Le trajet est maintenant en cours.');
    } 
    
    elseif ($action === 'terminer' && $trajet->getStatut() === 'En cours') {
        $trajet->setStatut('Terminé');
        
        // US 11 : Envoyer un mail aux passagers ici
        foreach ($trajet->getUsers() as $passager) {
            // Logique Mailer : "Merci de valider le trajet sur votre espace"
        }
        
        $this->addFlash('success', 'Trajet terminé ! Les passagers ont été invités à valider le voyage.');
    }

    $em->flush();
    return $this->redirectToRoute('app_user');
}

#[Route('/trajet/valider/{id}/{reussite}', name: 'app_trajet_valider')]
public function validerTrajet(Covoiturage $trajet, string $reussite, EntityManagerInterface $em): Response
{
    $chauffeur = $trajet->getVoiture()->getUser();
    
    if ($reussite === 'oui') {
        // US 11 & 12 : Mise à jour des crédits du chauffeur
        // On ajoute le prix du trajet au crédit du chauffeur
        $chauffeur->setCredit($chauffeur->getCredit() + $trajet->getPrixPersonne());
        
        $trajet->setStatut('Clôturé');
        $this->addFlash('success', 'Merci ! Le chauffeur a bien reçu ses crédits.');
    } else {
        // Si le passager indique un problème
        $this->addFlash('danger', 'Un employé va examiner la situation avant de débloquer les crédits du chauffeur.');
        // Ici, on pourrait envoyer un mail à l'admin ou changer le statut en 'Litige'
        $trajet->setStatut('Litige');
    }

    $em->flush();
    return $this->redirectToRoute('app_user');
}

}
