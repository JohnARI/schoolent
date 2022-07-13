<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Regex;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom et Prénom',
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Adresse mail',
                ]
            ])
            ->add('phone', TelType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Téléphone',
                ],
                // 'constraints' => [
                //     new Regex([
                //         'pattern' => '/^\(0\)[0-9]*$',
                //         'message' => 'Votre numéro de téléphone n\'est pas valide',
                //     ]),
                // ]
            ])
            ->add('objet', ChoiceType::class, [
                    'required' => true,
                'choices' => [
                    'Inscription' => 'Inscription',
                    'Autres' => 'Autres',
                ],
            ])
            ->add('message', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Message',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer la demande",
                'attr' => [
                    'class' => 'login100-form-btn btn-primary col-4',
                    'type' => 'submit',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}