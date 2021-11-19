<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control m-4'],
            ])
            
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'attr' => array('checked'   => 'checked'),
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Debes acepatr los términos.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Introduce un password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Tu password debe tener al menos {{ limit }} carácteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'attr' => ['class' => 'form-control m-4'],
            ])
            ->add('displayname', TextType::class, [
                'attr' => ['class' => 'form-control m-4'],
                'required' => true,
                'label_format' => 'NICK',
            ])
            ->add('avatar', FileType::class, [
                'required' => false,
                'data_class' => NULL,
                'constraints' => [
                    new File([
                        'maxSize' => '10240k',
                        'mimeTypes' => [ 'image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'La image debe de ser jpg, gif o png'
                    ])
                ],
                'attr' => ['class' => 'form-control m-4']
            ])
            // ->add('registrar', SubmitType::class, [
            //     'attr' => ['class' => 'btn btn-primary m-4 text-center']
            //     ]
            // )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
