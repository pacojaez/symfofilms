<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add( 'campo', ChoiceType::class, [
                'choices' => $options['field_choices'],
                'attr' => [ 'class' => 'form-control' ]
                ])
            ->add( 'SEARCH', SubmitType::class, [
                'attr' =>[ 'class' => 'btn btn-primary' ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
