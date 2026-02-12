<?php

namespace App\Form;

use App\Entity\Covoiturage;
use App\Entity\Voiture; // Ne pas oublier de décommenter
use App\Entity\Marque;  // Utile si tu veux un label précis
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class CovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On récupère l'utilisateur passé dans les options
        $user = $options['user'];

        $builder
            ->add('date_depart', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Jour de départ'
            ])
            ->add('heure_depart', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de Départ'
            ])
            ->add('lieu_depart', TextType::class, [
                'label' => 'Adresse de départ',
                'attr' => [
                    'placeholder' => 'Saisissez une adresse précise...',
                    'class' => 'adresse-autocomplete'
                ]
            ])
            ->add('date_arrivee', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Jour d\'arrivée'
            ])
            ->add('heure_arrivee', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure d\'arrivée'
            ])
            ->add('lieu_arrivee', TextType::class, [
                'label' => 'Adresse de destination',
                'attr' => [
                    'placeholder' => 'Saisissez une adresse de destination...',
                    'class' => 'adresse-autocomplete'
                ]
            ])
            ->add('prix_personne', MoneyType::class, [
                'label' => 'Prix (Crédits)',
                'currency' => 'EUR'
            ])
            // --- AJOUT DU CHOIX DU VÉHICULE ---
            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'label' => 'Véhicule utilisé',
                'placeholder' => 'Choisissez votre véhicule',
                // On affiche la marque et le modèle dans la liste
                'choice_label' => function (Voiture $voiture) {
                    return $voiture->getMarque()->getLibelle() . ' ' . $voiture->getModele() . ' (' . $voiture->getImmatriculation() . ')';
                },
                // REQUÊTE POUR FILTRER : Uniquement les voitures de l'utilisateur connecté
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('v')
                        ->where('v.user = :u')
                        ->setParameter('u', $user);
                },
            ]);

            // Note : On retire nb_place du formulaire car il sera 
            // rempli automatiquement par la capacité du véhicule choisi dans le contrôleur.
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
            'user' => null, // On déclare l'option "user" pour pouvoir l'utiliser
        ]);
    }
}