<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


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

            ->add('phone', TelType::class, [
                'attr' => [
                    'placeholder' => 'Téléphone',
                    'class' => 'input100 form-control',
                ],
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
