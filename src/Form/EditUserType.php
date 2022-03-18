<?php

namespace App\Form;

use App\Entity\Payment;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
               // Choix du prénom
               ->add('firstname', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'form-control',
                ]
            ])
                    // Choix du nom de famille
            ->add('lastname', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'form-control',
                ]
            ])
                    // Choix de l'email
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'form-control',
                ]
            ])

            ->add('session', EntityType::class, [
                'attr' => [
                    'class' => 'input100 form-control',
                ],
                'placeholder' => 'Session',
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'class' => Session::class,
            ])

            ->add('payment', EntityType::class, [
                'attr' => [
                    'class' => 'input100 form-control',  
                ],
                'placeholder' => 'Rémuneration',
                'required' => false,
                'class' => Payment::class,
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
                    'Professeur' => "ROLE_TEACHER",
                    'Eleve' => "ROLE_USER",
                ]
            ])

            ->add('picture', FileType::class, [    
                'required' => false,
                'data_class' => null,
                // 'constraints'=> [

                //     new Image([
                //         'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp' ,'image/jpg'],
                //         'mimeTypesMessage' => 'Les types de fichiers autorisés sont : .jpeg / .png / .webp / .jpg'
                //     ])
                // ]



            ])

            ->add('submit', SubmitType::class, [
                'label' => "Modifier",
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
            // Configure your form options here
        ]);
    }
}
