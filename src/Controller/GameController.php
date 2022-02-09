<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'game')]
    public function index(): Response
    {
        $currentUser = $this->getUser();

        return $this->render('game/index.html.twig', [
            'page' => 'game',
            'currentUser' => $currentUser,
        ]);
    }
}