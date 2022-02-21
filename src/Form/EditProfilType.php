<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'form-control',
                ]
            ])

            ->add('password', RepeatedType::class, [
                'constraints' => new Length([
                    'min' => 4,
                    'max' => 180,
                ]),
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'les deux mots de passe doivent être identiques',
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Entrez votre nouveau mot de passe',
                        'class' => 'input100 form-control'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer le nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmez votre Nouveau mot de passe',
                        'class' => 'input100 form-control'

                    ]
                ],
            ])

            ->add('phone', TelType::class, [
                'attr' => [
                    'placeholder' => 'Téléphone',
                    'class' => 'input100 form-control',
                ],
            ])

            ->add('submitPassword', SubmitType::class, [
                'label' => "Modifier",
                'attr' => [
                    'class' => 'login100-form-btn btn-primary',
                    'type' => 'submit',
                ]
            ])

            ->add('submit', SubmitType::class, [
                'label' => "Modifier",
                'attr' => [
                    'class' => 'login100-form-btn btn-primary',
                    'type' => 'submit',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
