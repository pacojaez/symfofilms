<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control m-2'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Introduce tu nuevo password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Tu password debe tener al menos {{ limit }} carácteres',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'Nuevo password',
                ],
                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control m-2'
                    ],
                    'label' => 'Repeat Password',
                ],
                'invalid_message' => 'Los dos campos deben coincidir',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
