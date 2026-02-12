<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SearchTrajetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('depart', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ville de départ',
                    'class' => 'form-control'
                ]
            ])
            ->add('arrivee', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ville d\'arrivée',
                    'class' => 'form-control'
                ]
            ])
            ->add('date', DateType::class, [
                'label' => false,
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
          ->add('prixMax', NumberType::class, [
        'required' => false,
        'label' => 'Prix max',
        'attr' => ['placeholder' => 'Prix max (€)', 'class' => 'form-control']
    ])
    ->add('dureeMax', IntegerType::class, [
        'required' => false,
        'label' => 'Durée max',
        'attr' => ['placeholder' => 'Heures max', 'class' => 'form-control']
    ])
    ->add('noteMin', IntegerType::class, [
        'required' => false,
        'label' => 'Note min',
        'attr' => ['placeholder' => 'Note chauffeur (1-5)', 'class' => 'form-control', 'min' => 1, 'max' => 5]
    ])

    ->add('estEcologique', CheckboxType::class, [
    'label' => 'Voyage écologique uniquement',
    'required' => false,
    'attr' => ['class' => 'form-check-input']
]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
         
        'method' => 'GET',
        'attr' => ['id' => null],
        'block_prefix' => '', 
    ]);
    }
}