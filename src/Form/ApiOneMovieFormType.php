<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ApiOneMovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('formato', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'JSON' => 'json',
                    'CSV' => 'csv',
                    'XML' => 'xml'
                ],
                'choice_attr' => [
                    'checked' => 'checked',
                    'class' => 'form-control m-5 d-flex flex-row justify-content-center'
                ],
                'attr' => [ 'class' => 'form-control m-5 d-flex flex-row justify-content-center' ]
            ])
            ->add( 'id', NumberType::class, [
                'required' => true,
                'html5' => true,
                'attr' => [ 'class' => 'form-control' ]
            ])
            ->add( 'SEARCH', SubmitType::class, [
                'attr' =>[ 'class' => 'btn btn-primary m-5' ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
