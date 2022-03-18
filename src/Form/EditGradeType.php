<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\ProgrammingLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditGradeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('category', EntityType::class, [

                'class' => ProgrammingLanguage::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('name', TextType::class, ['attr' => [
                'attr' => 'input100 form-control',
            ]])
            ->add('grade', NumberType::class, ['attr' => [
                'class' => 'input100 form-control',
            ]])
            ->add('comment', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                ]
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'login100-form-btn btn-primary'
                ],
                'label' => 'Ajouter une note'
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grade::class,
        ]);
    }
}
