<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// All homepage-related functions
class HomeController extends AbstractController
{
    // Render the homepage
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'page'          => 'home',
            'currentUser'   => $this->getUser(),
        ]);
    }
}
