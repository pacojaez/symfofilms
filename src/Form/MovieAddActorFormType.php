<?php

namespace App\Form;

use App\Repository\ActorRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Actor;

use App\Form\Custom\DataListType;

class MovieAddActorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actor', DataListType::class, [
                'class' => Actor::class,
                'choice_label' => 'nombre',
                'label' => 'Añadir Actor',
                //consulta para recuperar los actores ordenados por nombre
                'query_builder' => function (ActorRepository $er ){
                    return $er->createQueryBuilder('a')
                    ->orderBy('a.nombre', 'ASC');
                }
            ])
            ->add('add', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary']
                ]
            )
            ->setAction( $options['action'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NULL
        ]);
    }
}
