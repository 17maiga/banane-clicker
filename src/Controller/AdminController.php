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

    #[Route('/admin/u', name:'userList')]
    public function userList(ManagerRegistry $doctrine):Response
    {
        $currentUser = $this->getUser();

        $em = $doctrine->getManager();
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/users/list.html.twig', [
            'page' => 'adminUserList',
            'currentUser' => $currentUser,
            'users' => $users,
        ]);
    }

    #[Route('/admin/u/{id}', name: 'userEdit')]
    public function userEdit(ManagerRegistry $doctrine, int $id):Response
    {
        $currentUser = $this->getUser();

        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) return $this->redirectToRoute('userList');

        return $this->render('admin/users/edit.html.twig', [
            'page' => 'adminUserEdit',
            'currentUser' => $currentUser,
            'user' => $user,
        ]);
    }

    #[Route('/admin/u/{id}/delete', name: 'userDelete')]
    public function userDelete(ManagerRegistry $doctrine, int $id):Response
    {
        $currentUser = $this->getUser();

        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) return $this->redirectToRoute('userList');



        return $this->render('admin/users/delete.html.twig', [
            'page' => 'adminUserDelete',
            'currentUser' => $currentUser,
            'user' => $user,
        ]);
    }
}
