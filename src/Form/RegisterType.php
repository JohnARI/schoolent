<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                // Choix du prénom
            ->add('firstname', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'input100 form-control',
                ]
            ])
                    // Choix du nom de famille
            ->add('lastname', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'input100 form-control',
                ]
            ])
                    // Choix de l'email
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'input100 form-control',
                ]
            ])
                    // Choix du numéro
            ->add('phone', TelType::class, [
                'attr' => [
                    'placeholder' => 'Téléphone',
                    'class' => 'input100 form-control',
                ],
            ])
      
                    // Choix du sexe
            ->add('sexe', ChoiceType::class, [
                'attr' => [
                    'class' => 'input100 form-control',
                ],
                'label' => 'Sexe',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Homme' => 0,
                    'Femme' => 1,

                ]
            ])
                    // Choix du rôle
            ->add('roles', ChoiceType::class, [
                'attr' => [
                    'class' => 'input100 form-control',
                ],
                'label' => 'Roles',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Administrateur' => "ROLE_ADMIN",
                    'Formateur' => "ROLE_TEACHER",
                    'Eleve' => "ROLE_USER",
                ]
            ])

            ->add('picture', FileType::class, [    
                'required' => true,
                'constraints'=> [

                    new Image([
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp' ,'image/jpg'],
                        'mimeTypesMessage' => 'Les types de fichiers autorisés sont : .jpeg / .png / .webp / .jpg'
                    ])
                ]



            ])

            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire",
                'attr' => [
                    'class' => 'login100-form-btn btn-primary',
                    'type' => 'submit',
                ]
            ]);

            $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
