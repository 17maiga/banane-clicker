<?php

namespace App\Controller;

use App\Entity\Upgrade;
use App\Entity\User;
use App\Form\UpgradeFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// All admin-related functions
class AdminController extends AbstractController
{
    // Main administration page
    #[Route('/admin', name: 'admin')]
    public function index(Request $request, ManagerRegistry $registry): Response
    {
        return $this->render('admin/index.html.twig', [
            'currentUser' => $this->getUser(),
            'page'        => 'admin',
        ]);
    }

    // Lists all upgrades
    #[Route('/admin/upgrade', name: 'admin_upgrade')]
    public function upgradeList(ManagerRegistry $registry): Response
    {
        // Read all upgrades from database
        $manager = $registry->getManager();
        $upgrades = $manager->getRepository(Upgrade::class)->findAll();

        return $this->render('admin/upgrade.html.twig', [
            'currentUser' => $this->getUser(),
            'page'        => 'upgrades',
            'upgrades'    => $upgrades,
        ]);
    }

    // Create an upgrade through a form
    #[Route('/admin/upgrade/create', name: 'admin_upgrade_create')]
    public function upgradeCreate(Request $request, ManagerRegistry $registry): RedirectResponse|Response
    {
        // Instantiate an upgrade and a form, and bind the form to the upgrade and HTTP request
        $upgrade = new Upgrade();
        $form = $this->createForm(UpgradeFormType::class, $upgrade);
        $form->handleRequest($request);

        // Check whether the form has been submitted and is valid, and then write the upgrade to the database and return to the upgrade list ('/admin/upgrade')
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $registry->getManager();
            $manager->persist($upgrade);
            $manager->flush();
            return $this->redirectToRoute('admin_upgrade');
        }

        // render the form
        return $this->render('admin/upgrade_create.html.twig', [
            'currentUser' => $this->getUser(),
            'page'        => 'upgrade_edit',
            'form'        => $form->createView(),
        ]);
    }

    // Update an upgrade through a form, with its id in the url
    #[Route('/admin/upgrade/{id}/edit', name: 'admin_upgrade_edit')]
    public function upgradeEdit(int $id, Request $request, ManagerRegistry $registry): RedirectResponse|Response
    {
        // Read the upgrade from the database
        $manager = $registry->getManager();
        $upgrade = $manager->getRepository(Upgrade::class)->findOneBy(['id' => $id]);

        // If the upgrade doesn't exist, return to the upgrade list ('/admin/upgrade')
        if (!$upgrade) return $this->redirectToRoute('admin_upgrade');

        // If the upgrade does exist, create a form and bind it to the upgrade and HTTP request
        $form = $this->createForm(UpgradeFormType::class, $upgrade);
        $form->handleRequest($request);

        // If the form is submitted and valid, update the upgrade in the database and redirect to the upgrade list ('/admin/upgrade')
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($upgrade);
            $manager->flush();
            return $this->redirectToRoute('admin_upgrade');
        }

        // Render the form
        return $this->render('admin/upgrade_edit.html.twig', [
            'currentUser' => $this->getUser(),
            'page'        => 'upgrade_edit',
            'upgrade'     => $upgrade,
            'form'        => $form->createView(),
        ]);
    }

    // Remove an upgrade from the database with its id
    #[Route('/admin/upgrade/{id}/delete', name: 'admin_upgrade_delete')]
    public function upgradeDelete(int $id, ManagerRegistry $registry): RedirectResponse
    {
        // Read the upgrade from the database through its id
        $manager = $registry->getManager();
        $upgrade = $manager->getRepository(Upgrade::class)->findOneBy(['id' => $id]);

        // If the upgrade exists
        if ($upgrade) {
            // Read all users from the database
            $users = $manager->getRepository(User::class)->findAll();
            // Loop through the user list
            foreach ($users as $user) {
                // For each user, read its data from the database
                $data = $user->getData();
                // If the upgrade is referenced in the user's data, remove the reference
                if (array_key_exists($upgrade->getName(), $data)) {
                    unset($data[$upgrade->getName()]);
                }
                // Set the user's data to the updated array and save it to the database
                $user->setData($data);
                $manager->persist($user);
            }
            // Remove the upgrade from the database
            $manager->remove($upgrade);
            $manager->flush();
        }

        // Redirect to the upgrade list ('/admin/upgrade')
        return $this->redirectToRoute('admin_upgrade');
    }
}
