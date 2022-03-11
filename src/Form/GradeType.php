<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Grade;
use App\Entity\Rating;
use App\Entity\Session;
use App\Repository\UserRepository;
use App\Entity\ProgrammingLanguage;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
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
    private $security;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        $session = $this->entityManager->getRepository(Session::class)->findAll();
        $mySession = $user->getSession($session)->getId();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user, $mySession) {
           
            $form = $event->getForm();

            $formOptions = [
                'class' => User::class,
                'choice_label' => 'Fullname',
                'query_builder' => function (UserRepository $userRepository) use ($mySession) {
                    return $userRepository->createQueryBuilder('u')
                        ->where('u.session = :user')
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('role', "%ROLE_USER%")
                        ->setParameter('user', $mySession)
                        ->orderBy('u.lastname', 'ASC');
                },
                'label' => 'Elève'
            ];

            if ($user->getRole() == 'Administrateur') {

                $form->add('user', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'Fullname',
                    'label' => 'Elève'
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
            ->add('comment', TextareaType::class, ['label' => 'Commentaire'])

            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary btn-block'],
                'label' => 'Ajouter une note'
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Grade::class,
        ]);
    }
}

