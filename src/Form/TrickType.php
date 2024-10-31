<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TrickType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => ['label' => 'URL de la vidéo'],
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'Vidéos',
                'mapped' => false,
                'required' => false,
                // 'prototype' => true,
                // 'prototype_name' => '__name__',  // Utilisé pour le clonage
                'data' => array_fill(0, 3, null), // Préparer 3 champs par défaut pour les vidéos
            ])

            ->add('images', FileType::class, options: [
                'multiple' => true,
                'required' => false,
                'mapped' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
