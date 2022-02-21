<?php

namespace App\Form;

use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, [ 
                'label' => 'Intitulé de la session'
            ])
            ->add('startSession',DateType::class, [
                'widget' => 'single_text', 
                'label' => 'Début de la session'
            ])
            ->add('endSession',DateType::class, [
                'widget' => 'single_text', 
                'label' => 'Fin de la session'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary '],
                'label' => 'modifier une session'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
