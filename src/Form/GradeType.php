<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Grade;
use App\Repository\UserRepository;
use App\Entity\ProgrammingLanguage;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class GradeType extends AbstractType
{

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            throw new \LogicException();
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user) {
            if (null !== $event->getData()->getUser()) {

                return;
            }

            $form = $event->getForm();


            $formOptions = [
                'class' => User::class,
                'choice_label' => 'fullName',
                'query_builder' => function (UserRepository $userRepository) use ($user) {
                    return $userRepository->createQueryBuilder('u')
                        ->where('u.session = :user')
                        ->setParameter('user', $user->getSession())
                        ->orderBy('u.session', 'ASC');
                },
                'label' => 'Etudiant'
            ];
            // create the field, this is similar the $builder->add()
            // field name, field type, field options

            if ($user->getRole() == 'Administrateur') {

                $form->add('user', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'fullName',
                    'label' => 'Etudiant'
                ]);
            } else {
                $form->add('user', EntityType::class, $formOptions);
            }
        });
        $builder

            ->add('category', EntityType::class, [
                'label' => 'Language',
                'class' => ProgrammingLanguage::class,
                'choice_label' => 'name',
            ])
            ->add('name', TextType::class, ['label' => 'Intitulé'])
            ->add('grade', NumberType::class, ['label' => 'Note'])
            ->add('comment', TextareaType::class, ['label' => 'Remarques'])

            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary btn-block'],
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
