<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\ProgrammingLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Intitulé',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('link', FileType::class, [
                'label' => 'Cours',
                'mapped' => true,
                'attr' => [
                    'required' => false,
                    'data-allowed-file-extensions' => 'jpg jpeg png gif html css rar doc docs',
                    'class' => 'dropify',
                ],
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '50024k',
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Language',
                'class' => ProgrammingLanguage::class,
                'choice_label' => 'name',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary btn-block'],
                'label' => 'Ajouter un cours'
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
