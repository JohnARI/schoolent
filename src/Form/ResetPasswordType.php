<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email',EmailType::class,[
            'label' => 'Email du compte',
            'attr' => ['placeholder'=>'Email','class'=>'form-control form-control-lg form-control-solid']])
        ->add('submit', SubmitType::class, [
            'attr' => ['class' => 'btn btn-primary btn-block'],
            'label' => 'Réinitialiser mon mot de passe'])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}