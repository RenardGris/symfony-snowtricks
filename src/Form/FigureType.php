<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Grabs' => 'Grabs',
                    'Rotation' => 'Rotation',
                    'Flips' => 'Flips',
                    'Slides' => 'Slides',
                    'One foot' => 'One foot',
                ],
                'required' => true
            ])
            ->add('description')
            ->add('images', FileType::class, [
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('videos', CollectionType::class, [
                'required' => false,
                'entry_type' => TextType::class,
                'entry_options' =>
                    [
                        'attr' => ['class' => 'col-12']
                    ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'mapped' => false,
                'label' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
