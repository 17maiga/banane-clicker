<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModerationController extends AbstractController
{
    #[Route('/mod', name: 'moderation')]
    public function index(): Response
    {
        return $this->render('moderation/index.html.twig', [
            'page' => 'moderation',
            'currentUser' => $this->getUser(),
        ]);
    }



    // Users
    #[Route('/mod/u', name:'moderation_user_list')]
    public function userList(ManagerRegistry $doctrine):Response
    {
        $em = $doctrine->getManager();
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('moderation/users.html.twig', [
            'page' => 'adminUserList',
            'currentUser' => $this->getUser(),
            'users' => $users,
        ]);
    }

    #[Route('/mod/u/{id}/delete', name: 'moderation_user_delete')]
    public function userDelete(ManagerRegistry $doctrine, int $id):Response
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) return $this->redirectToRoute('moderation_user_list');

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('moderation_user_list');
    }

    #[Route('/mod/u/{id}/promote', name: 'moderation_user_promote')]
    public function userPromote(ManagerRegistry $doctrine, int $id):Response
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) return $this->redirectToRoute('moderation_user_list');

        $moderator = false;
        foreach ($user->getRoles() as $role) {
            if ($role == 'ROLE_MODERATOR') $moderator = true;
        }

        if ($moderator) {
            $user->setRoles(['ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_USER', 'ROLE_MODERATOR']);
        }

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('moderation_user_list');
    }
}
