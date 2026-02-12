<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // On filtre les avis pour n'afficher que les validés
        $avisValides = $user->getAvis()->filter(function($avi) {
            return $avi->getStatut() === 'validé';
        });

        return $this->render('profil/profil.html.twig', [
            'user' => $user,
            'mesVoitures' => $user->getVoitures(),
            'avisValides' => $avisValides,
            'hasVoiture' => count($user->getVoitures()) > 0,
        ]);
    }

    #[Route('/user/modifier-photo', name: 'app_user_photo')]
    public function updatePhoto(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $photoFile = $request->files->get('photo_profil');
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->getClientOriginalExtension();
                $photoFile->move($this->getParameter('kernel.project_dir').'/public/uploads/photos', $newFilename);
                
                /** @var User $user */
                $user->setPhoto($newFilename);
                $em->flush();
                $this->addFlash('success', 'Photo de profil mise à jour !');
                return $this->redirectToRoute('app_user');
            }
        }

        return $this->render('profil/update_photo.html.twig');
    }
}