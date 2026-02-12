<?php

namespace App\Controller;

use App\Entity\Avis; 
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\AvisRepository; 
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CovoiturageRepository;
use App\Repository\UserRepository;
use App\Service\NosqlStatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
public function index(
    UserRepository $userRepo, 
    CovoiturageRepository $trajetRepo, 
    NosqlStatsService $nosqlStats
): Response {
    // 1. Total des crédits gagnés par la plateforme (Ex: commission de 2€ par trajet)
    // Ici, on fait une estimation simple, à adapter selon votre logique métier
    $totalTrajetsTermines = $trajetRepo->count(['statut' => 'Terminé']);
    $totalCreditsPlateforme = $totalTrajetsTermines * 2; 

    // 2. Données pour les graphiques (Exemple sur les 7 derniers jours)
    $statsGraph = [
        'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
        'trajets' => [5, 8, 12, 7, 15, 20, 10], // À remplacer par une requête GROUP BY date
        'credits' => [10, 16, 24, 14, 30, 40, 20], // Nombre trajets * commission
    ];

    return $this->render('admin/admin.html.twig', [
        'countUsers' => $userRepo->count([]),
        'allUsers' => $userRepo->findAll(), // Pour la suspension de compte
        'totalCredits' => $totalCreditsPlateforme,
        'stats' => $nosqlStats->getStats(),
        'statsGraph' => $statsGraph,
    ]);
}
    //valid avis
    #[Route('/admin/avis/valider/{id}', name: 'app_admin_avis_valider')]
    public function validerAvis(Avis $avis, EntityManagerInterface $em): Response
    {
    // On change le statut en SQL
    $avis->setStatut('validé');
    $em->flush();

    $this->addFlash('success', 'Avis validé ! Il est désormais visible sur le site.');
    return $this->redirectToRoute('app_admin_avis');
    }

    //refus avis
    #[Route('/admin/avis/refuser/{id}', name: 'app_admin_avis_refuser')]
    public function refuserAvis(Avis $avis, EntityManagerInterface $em): Response
    {
    
    $em->remove($avis);
    $em->flush();

    $this->addFlash('success', 'Avis supprimé.');
    return $this->redirectToRoute('app_admin_avis');
    }


    #[Route('/admin/user/toggle/{id}', name: 'app_admin_toggle_user', methods: ['POST'])]
public function toggleUser(User $user, EntityManagerInterface $em): Response
{
    // Il faudra ajouter le champ boolean 'isSuspended' dans votre entité User
    // php bin/console make:entity User (puis ajouter isSuspended)
    
    $user->setIsSuspended(!$user->isSuspended());
    $em->flush();

    $status = $user->isSuspended() ? 'suspendu' : 'activé';
    $this->addFlash('success', "Le compte de {$user->getPseudo()} a été $status.");

    return $this->redirectToRoute('app_admin');
}

#[Route('/admin/register-employe', name: 'app_register_employe')]
public function registerEmploye(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
{
    $user = new User();
    // On peut réutiliser votre formulaire d'inscription existant (ex: RegistrationFormType)
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // On encode le mot de passe
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        // IMPORTANT : On définit le rôle employé
        $user->setRoles(['ROLE_EMPLOYE']);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Compte employé créé avec succès.');
        return $this->redirectToRoute('app_admin');
    }

    return $this->render('admin/register_employe.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}
}