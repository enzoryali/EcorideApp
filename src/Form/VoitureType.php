<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\User;
use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; 
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modele')
            ->add('immatriculation')
            ->add('energie', ChoiceType::class, [
                    'choices' => [
                    'Électrique' => 'Electrique',
                    'Essence' => 'Essence',
                    'Diesel' => 'Diesel',
            ],
            ])
            ->add('couleur')
            ->add('nbPlaces', IntegerType::class, [ 
                'label' => 'Nombre de places du véhicule',
                'attr' => [
                    'min' => 1, 
                    'max' => 9, 
                    'class' => 'form-control'
                ]
            ])
            ->add('date_premiere_immatriculation')
            ->add('marque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => 'libelle',
            ])
            
            ->add('fumeur', ChoiceType::class, [
                'required' => false,
               'choices' => [
                    
                    'Fumeur accepté' => true,
                    'Non-fumeur uniquement' => false,
               ],
               'expanded' => true,
               'multiple' => false,
               'label' => 'Préférence fumeur',
            ])
            ->add('animaux', ChoiceType::class, [
                'required' => false,
               'choices' => [
                    
                    'Animaux Acceptés' => true,
                    'Pas d\'animaux' => false,
               ],
               'expanded' => true,
            ])
            ->add('preferences_libres', TextareaType::class, [
                'required' => false,
                'label' => 'Autres préférences',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
