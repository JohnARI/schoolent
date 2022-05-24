<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\ProgrammingLanguage;
use App\Entity\Session;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('start', DateType::class, [
            'label' => 'Depart',
            'widget' => 'single_text'
        ])
        ->add('end', DateType::class, [
            'label' => 'Cloture',
            'widget' => 'single_text'
        ])
        ->add('name', TextType::class, [
            'label' => 'Intitulé',
        ])
        ->add('category', EntityType::class, [
            'label' => 'Technologie',
            'class' => ProgrammingLanguage::class,
            'choice_label' => 'name',
        ])
        ->add('session', EntityType::class, [
            'label' => 'Session',
            'class' => Session::class,
            'choice_label' => 'name',
        ])
        ->add('teacher_name', EntityType::class, [
            'label' => 'Professeur',
            'class' => User::class,
            'multiple' => false,
            // 'query_builder' => function (UserRepository $teacher) {
 
            //     return $teacher->findByRole('ROLE_TEACHER');
            // },
        ])
        ->add('submit', SubmitType::class, [
            'attr' => ['class' => 'btn btn-primary btn-block'],
            'label' => 'Ajouter une date'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}