<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom et Prénom',
                ]
            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'placeholder' => 'Adresse mail',
                ]
            ])
            ->add('phone', TextType::class, [
                'attr' => [
                    'placeholder' => 'Numéro de téléphone',
                ]
            ])
            ->add('objet', ChoiceType::class, [
                'choices' => [
                    'Inscription' => 'Inscription',
                    'Autres' => 'Autres',
                ],
            ])
            ->add('message')
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer la demande",
                'attr' => [
                    'class' => 'login100-form-btn btn-primary col-4',
                    'type' => 'submit',
                ]
            ]);
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
