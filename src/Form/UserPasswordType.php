<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('firstname', TextType::class, [
                'disabled' => true,

            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,

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

                    'attr' => ['placeholder' => 'Entrez votre nouveau mot de passe']
                ],
                'second_options' => [
                    'attr' => ['placeholder' => 'Confirmez votre Nouveau mot de passe']
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label'=>'submit',
                'attr'=>['class'=>'btn btn-primary btn-block']
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
