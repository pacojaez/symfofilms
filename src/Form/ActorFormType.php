<?php

namespace App\Form;

use App\Entity\Actor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/* use for form */
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;


class ActorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre',  TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label_format' => 'NOMBRE DEL ACTOR/ACTRIZ',
                ])
            ->add('fechanacimiento',  DateType::class, [
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text',
                'label_format' => 'FECHA DE NACIMIENTO',
                ])
            ->add('nacionalidad', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'label_format' => 'NACIONALIDAD',
                ])
            ->add('biografia', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'label_format' => 'BIOGRAFÍA',
                ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Actor' => 'M',
                    'Actriz' => 'F'
                ],
                'label' => 'GÉNERO',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            // ->add('gender', RadioType::class, [
            //     'label' => 'Actor',
            //     'data' => 'M'
            //     ])
            // ->add('gender', RadioType::class, [
            //     'attr' => ['class' => 'form-control'],
            //     'required' => true,
            //     'label_format' => 'GÉNERO',
            //     ])
            ->add('portrait', FileType::class, [
                'required' => false,
                'data_class' => NULL,
                'constraints' => [
                    new File([
                        'maxSize' => '10240k',
                        'mimeTypes' => [ 'image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'La image debe de ser jpg, gif o png'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('Guardar', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary']
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Actor::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'SymfoFilmsApp_token',
            'csrf_toke_id' => 'nombreparagenerarlasemilladeltoken',
        ]);
    }
}

