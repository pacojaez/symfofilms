<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use App\Services\SimpleSearchService;

class SearchFormType extends AbstractType {

    public function buildForm ( FormBuilderInterface $builder, array $options ){

        $builder->add( 'campo', ChoiceType::class, [
            'choices' => $options['field_choices'],
            'attr' => [ 'class' => 'form-control' ]
        ])
        ->add( 'valor', TextType::class, [
            'attr' => ['class' => 'form-control']
        ])
        ->add( 'orden', ChoiceType::class, [
            'choices' => $options[ 'order_choices' ],
            'attr' => [ 'class' => 'form-control' ]
        ])
        ->add( 'sentido', ChoiceType::class, [
            'expanded' => true,
            'multiple' => false,
            'choices' => [
                'ASC' => 'ASC',
                'DESC' => 'DESC'
            ],
            'choice_attr' => [
                'checked' => 'checked',
                // 'class' => 'form-control'
            ],
            'attr' => [ 'class' => 'form-control' ]
        ])
        ->add( 'limite', NumberType::class, [
            'required' => true,
            'html5' => true,
            'attr' => [
                'min' => 1,
                'max' => 20,
                'class' => 'form-control'
            ],
            // 'attr' => [ 'class' => 'form-control' ]
        ])
        ->add( 'SEARCH', SubmitType::class, [
            'attr' =>[ 'class' => 'btn btn-primary' ]
        ])
        ;
    }


    public function configureOptions ( OptionsResolver $resolver ): void {

        // $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'data_class' => SimpleSearchService::class,
            'field_choices' => [ 'id' => 'id'],
            'order_choices' => [ 'id' => 'id'],
            'csrf_protection' => true,
            'csrf_field_name' => 'SymfoFilmsApp_token',
            'csrf_toke_id' => 'nombreparagenerarlasemilladeltoken',
        ]);
    }

    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Movie::class,
    //     ]);
    // }
}