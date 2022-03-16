<?php

namespace App\Controller;

use App\Entity\Upgrade;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// All game-related functions
class GameController extends AbstractController
{
    // The main game page
    #[Route('/game', name: 'game')]
    public function index(Request $request, ManagerRegistry $mr): Response
    {
        // Read the current user from the database
        $em = $mr->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        // Render the game page with the current user's data
        return $this->render('game/index.html.twig', [
            'page'          => 'game',
            'currentUser'   => $user,
        ]);
    }

    // initializes the game
    #[Route('/game/initialize', name:'game_initialize')]
    public function game_initialize(Request $request, ManagerRegistry $registry): JsonResponse|RedirectResponse
    {
        // If the request isn't an AJAX request, redirect to the main game page
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('game');
        }

        // Read the user and all upgrades from the database
        $manager = $registry->getManager();
        $upgrades = $manager->getRepository(Upgrade::class)->findAll();
        $user = $manager->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $userData = $user->getData();

        // For each upgrade, save the upgrade's name, bananas/second, price, and amount owned by the user (or 0 if the user doesn't own any) into a single array
        $responseUpgrades = [];
        foreach ($upgrades as $upgrade) {
            $responseUpgrades[] = [
                'name'      => $upgrade->getName(),
                'bps'       => $upgrade->getBananasPerSecond(),
                'price'     => $upgrade->getPrice(),
                'amount'    => $userData[$upgrade->getName()] ?? 0,
            ];
        }

        // Return the user's score and the array containing all the upgrade data as a JSON object
        return new JsonResponse([
                'score' => $user->getScore(),
                'upgrades' => $responseUpgrades,
            ], Response::HTTP_OK
        );
    }

    // Is called through an AJAX request, save the game to database
    #[Route('/game/save', name:'game_save')]
    public function game_save(Request $request, ManagerRegistry $registry): JsonResponse|RedirectResponse
    {
        // If the request isn't an AJAX request, redirect to the main game page ('/game')
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('game');
        }

        // Read the user and all the upgrades from the database
        $manager = $registry->getManager();
        $user = $manager->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $upgrades = $manager->getRepository(Upgrade::class)->findAll();

        // Decode the received JSON data
        $content = json_decode($request->getContent(), true);
        $score = $content['score'];
        $data = [];
        // For each upgrade, add the amount owned by the user to an array
        foreach ($upgrades as $upgrade) {
            $data[$upgrade->getName()] = $content['upgrades'][$upgrade->getName()]['amount'];
        }

        // Set the user's score and data to the ones received by the AJAX request
        $user->setScore($score);
        $user->setData($data);
        $manager->persist($user);
        $manager->flush();

        // Return an HTTP_OK response
        return new JsonResponse([
                'status' => 'saved',
            ], Response::HTTP_OK
        );
    }
}
