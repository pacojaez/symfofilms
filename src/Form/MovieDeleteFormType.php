<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MovieDeleteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('Confirmar', SubmitType::class, [
            'attr' => ['class' => 'btn btn-danger']
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'SymfoFilmsApp_token',
            'csrf_toke_id' => 'nombreparagenerarlasemilladeltoken',
        ]);
    }
}
