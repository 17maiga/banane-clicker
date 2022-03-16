<?php

namespace App\Controller;

use App\Entity\Upgrade;
use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// All account-related functions
class AccountController extends AbstractController
{
    // Display the current user's account
    #[Route('/u', name: 'account')]
    public function index(ManagerRegistry $registry): Response
    {
        // Read the user and all upgrades from the database
        $manager = $registry->getManager();
        $user = $manager->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $upgrades = $manager->getRepository(Upgrade::class)->findAll();

        // Render the user's info
        return $this->render('account/index.html.twig', [
            'page'        => 'account',
            'upgrades'    => $upgrades,
            'currentUser' => $user,
        ]);
    }

    // Update the current user through a form
    #[Route('/u/edit', name: 'account_edit')]
    public function accountEdit(Request $request, ManagerRegistry $registry): Response|RedirectResponse
    {
        // Read the current user from the database, create a form, and assign the user and the request to the form
        $manager = $registry->getManager();
        $user = $manager->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        // If the form has been submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Update the user's username from the form's data
            $user->setUsername($form->get('username')->getData());

            // Update the user in the database
            $manager->persist($user);
            $manager->flush();

            // Redirect to the user's account ('/u')
            return $this->redirectToRoute('account');
        }

        // Render the edit form
        return $this->render('account/edit.html.twig', [
            'page'        => 'Edit account',
            'currentUser' => $user,
            'form'        => $form->createView(),
        ]);
    }

    // Reset the user's info in the database
    #[Route('/u/reset', name: 'account_reset')]
    public function accountReset(ManagerRegistry $registry): RedirectResponse
    {
        // Read the user from the database
        $manager = $registry->getManager();
        $user = $manager->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        // Set the user's score to 0 and empty their data
        $user->setScore(0)
             ->setData([]);

        // Update the user in the database
        $manager->persist($user);
        $manager->flush();

        // Redirect to the user's account ('/u')
        return $this->redirectToRoute('account');
    }
}
