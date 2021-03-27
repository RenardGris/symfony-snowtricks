<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class StoreMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('images', FileType::class, [
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('video', CollectionType::class, [
                'required' => false,
                'entry_type' => TextType::class,
                'entry_options' =>
                    [
                        'attr' => ['class' => 'col-12']
                    ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ]);
    }

}
