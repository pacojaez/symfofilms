<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RoleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add( 'role', ChoiceType::class, [
            'expanded' => true,
            'multiple' => true,
            'choices' => [
                'ADMIN' => 'ROLE_ADMIN',
                'SUPERVISOR' => 'ROLE_SUPERVISOR',
                'EDITOR' =>'ROLE_EDITOR',
                'MODERADOR' => 'ROLE_MODERADOR'
            ],
            'choice_attr' => [
                'checked' => 'checked',
                // 'class' => 'form-control'
            ],
            'attr' => [ 'class' => 'form-control m-2 p-2' ]
        ])
        ->add( 'ADD_ROLE', SubmitType::class, [
            'attr' =>[ 'class' => 'btn btn-primary m-2 p-2' ]
        ])
        ->setAction( $options['action'])
        ->setMethod( 'POST')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
