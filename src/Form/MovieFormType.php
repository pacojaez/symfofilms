<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/* use for form */
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titulo', TextType::class, [
            'attr' => ['class' => 'form-control'],
            'label_format' => 'TÍTULO DE LA PELÍCULA',
            ])
        ->add('duracion', NumberType::class, [
            'attr' => ['class' => 'form-control'],
            'label_format' => 'DURACIÓN (EN MINUTOS)',
            'empty_data' => 0,
            'html5' => true,
            'required' => false,
        ])
        ->add('director', TextType::class, [
            'attr' => ['class' => 'form-control'],
            'label_format' => 'DIRECTOR',
            'required' => false,
            ])
        ->add('genero', TextType::class, [
            'attr' => ['class' => 'form-control'],
            'label_format' => 'GÉNERO',
            'required' => false,
            ])
        ->add('valoracion', NumberType::class, [
            'attr' => ['max' => 5],
            'attr' => ['class' => 'form-control'],            
            'label_format' => 'VALORACIÓN (Del 1 al 5)',
            'empty_data' => 0,
            'html5' => true,
            'required' => false,
            ])
        ->add('estreno', NumberType::class, [
            'attr' => ['class' => 'form-control'],
            'label_format' => 'AÑO DEL ESTRENO',
            'empty_data' => 0,
            'html5' => true,
            'required' => false,
            ])
        ->add('sinopsis', TextareaType::class, [
            'attr' => ['class' => 'form-control'],
            'required' => false,
            ])
        ->add('Guardar', SubmitType::class, [
            'attr' => ['class' => 'btn btn-primary']
            ]
        )
        ->getForm();
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}


