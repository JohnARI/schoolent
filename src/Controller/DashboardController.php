<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DashboardController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("admin/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $this->render('dashboard/admins-dashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'users' => $users
        ]);
    }

    /**
     * @Route("admin/teachers", name="view-teachers")
     */
    public function showTeacher($role = 'ROLE_TEACHER'): Response {

        $users = $this->entityManager->getRepository(User::class)->findByRole($role);

        
        return $this->render("administration/admin/view_teacher.html.twig",[
            'users' => $users,
        ]);
    } 

    /**
     * @Route("admin/admins", name="view-admins")
     */
    public function showAdmin($role = 'ROLE_ADMIN'): Response {

        $users = $this->entityManager->getRepository(User::class)->findByRole($role);

        
        return $this->render("administration/admin/view_admin.html.twig",[
            'users' => $users,
        ]);
    } 

    /**
     * @Route("admin/contact", name="view-contact")
     */
    public function showContact(): Response {



        $contacts = $this->entityManager->getRepository(Contact::class)->findAll();
        
        return $this->render("administration/admin/view_contact.html.twig", [
            'contacts' => $contacts,
        ]);
    } 
    




}
