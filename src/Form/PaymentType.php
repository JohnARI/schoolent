<?php

namespace App\Form;

use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('tarification', NumberType::class, [
            'attr' => ['placeholder' => 'Rémuneration'],
            'label' => 'Rémunération journalière'
        ])

        ->add('submit', SubmitType::class, [
            'attr' => ['class' => 'login100-form-btn btn-primary'],
            'label' => 'Ajouter un tarif'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}
