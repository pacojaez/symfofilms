<?php

namespace App\Form\Custom;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DataListType extends AbstractType {
    
    public function getParent(){

        return EntityType::class;

    }
    // public function buildForm(FormBuilderInterface $builder, array $options): void {
    //     $builder
    //         ->add('field_name')
    //     ;
    // }

    // public function configureOptions(OptionsResolver $resolver): void {
    //     $resolver->setDefaults([
    //         // Configure your form options here
    //     ]);
    // }
}
