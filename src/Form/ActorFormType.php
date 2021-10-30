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


class ActorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre',  TextType::class, [
                'attr' => ['class' => 'form-control']
                ])
            ->add('fecha_nacimiento',  DateType::class, [
                'attr' => ['class' => 'form-control']
                ])
            ->add('nacionalidad', TextType::class, [
                'attr' => ['class' => 'form-control']
                ])
            ->add('biografia', TextareaType::class, [
                'attr' => ['class' => 'form-control']
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
            'data_class' => Actor::class,
        ]);
    }
}

