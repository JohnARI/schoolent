<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Calendar;
use App\Entity\ProgrammingLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('start', DateTimeType::class, [
            'label' => 'Départ',
            'date_widget' => 'single_text'
        ])
        ->add('end',DateTimeType::class, [
            'label' => 'Cloture',
            'date_widget' => 'single_text'
        ])
        ->add('title', TextType::class, [
            'label' => 'Intitulé',
        ])
        ->add('category', TextType::class, [
            'label' => 'Technologie',
            
        ])
        ->add('session')
    
        ->add('teacher_name', TextType::class, [
            'label' => 'Formateur',

        ])
        ->add('submit', SubmitType::class, [
            'attr' => ['class' => 'btn btn-primary btn-block'],
            'label' => 'Ajouter/modifier une date'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
