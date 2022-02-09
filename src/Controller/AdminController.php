<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $currentUser = $this->getUser();

        return $this->render('admin/index.html.twig', [
            'page' => 'admin',
            'currentUser' => $currentUser,
        ]);
    }

    #[Route('/admin/user', name:'userList')]
    public function userList(ManagerRegistry $doctrine):Response
    {
        $currentUser = $this->getUser();

        $em = $doctrine->getManager();
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/list.html.twig', [
            'page' => 'adminUserList',
            'currentUser' => $currentUser,
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}', name: 'userEdit')]
    public function userEdit(ManagerRegistry $doctrine, int $id):Response
    {
        $currentUser = $this->getUser();

        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) {
            return $this->redirectToRoute('userList');
        }

        return $this->render('admin/edit.html.twig', [
            'page' => 'adminUserEdit',
            'currentUser' => $currentUser,
            'user' => $user,
        ]);
    }
}
