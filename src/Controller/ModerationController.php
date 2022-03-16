<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// All moderation-related functions
class ModerationController extends AbstractController
{
    // Render the main moderation page
    #[Route('/mod', name: 'mod')]
    public function index(): Response
    {
        return $this->render('moderation/index.html.twig', [
            'page'          => 'moderation',
            'currentUser'   => $this->getUser(),
        ]);
    }

    // Lists all users from the database, with links to promote/demote moderators if the current user is an admin, and to delete users if:
    //      - the current user is a moderator and the user to delete is a simple user
    //      - the current user is an admin
    #[Route('/mod/u', name:'mod_user_list')]
    public function userList(ManagerRegistry $doctrine):Response
    {
        $em = $doctrine->getManager();
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('moderation/users.html.twig', [
            'page'          => 'adminUserList',
            'currentUser'   => $this->getUser(),
            'users'         => $users,
        ]);
    }

    // Delete a user through its id if :
    //      - the current user is a moderator and the user to delete is a simple user
    //      - the current user is an admin
    // and redirect to the moderation's user list ('/mod/u')
    #[Route('/mod/u/{id}/delete', name: 'mod_user_delete')]
    public function userDelete(ManagerRegistry $registry, int $id):Response
    {
        $manager = $registry->getManager();
        $user = $manager->getRepository(User::class)->findOneBy(['id' => $id]);
        if ($user && !(in_array("ROLE_ADMIN", $user->getRoles()) || (
                in_array("ROLE_MODERATOR", $user->getRoles()) &&
                !in_array("ROLE_ADMIN", $this->getUser()->getRoles())
            ))) {
            $manager->remove($user);
            $manager->flush();
        }
        return $this->redirectToRoute('mod_user_list');
    }

    // Promote a user to moderator/demote a moderator to user if the current user is an admin
    #[Route('/mod/u/{id}/promote', name: 'mod_user_promote')]
    public function userPromote(ManagerRegistry $doctrine, int $id):Response
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
        if ($user && in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            if (in_array("ROLE_MODERATOR", $user->getRoles())) {
                $user->setRoles(['ROLE_USER']);
            } else {
                $user->setRoles(['ROLE_USER', 'ROLE_MODERATOR']);
            }
            $em->persist($user);
            $em->flush();
        }
        return $this->redirectToRoute('mod_user_list');
    }
}
