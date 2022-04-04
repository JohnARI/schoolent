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

class GradeProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('category', EntityType::class, [

                'class' => ProgrammingLanguage::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control input-sm',
                ]
            ])
            ->add('name', TextType::class, ['attr' => [
                'attr' => 'form-control input-sm',
            ]])
            ->add('grade', NumberType::class, ['attr' => [
                'class' => 'form-control input-sm',
            ]])
            ->add('comment', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control input-sm',
                    'rows' => '1',
                ]
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',                   
                ],
                'label' => 'Ajouter',

            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grade::class,
        ]);
    }
}
