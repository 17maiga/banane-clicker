<?php

namespace App\Controller;

use App\Entity\Data;
use App\Entity\Upgrade;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'game')]
    public function index(Request $request, ManagerRegistry $mr): JsonResponse|Response
    {
        $em = $mr->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $data = $user->getData();
        $upgrades = $em->getRepository(Upgrade::class)->findAll();

        if ($data == null) {
            $data = new Data();
            $data->setUser($user);
            $empty_upgrades = [];
            foreach ($upgrades as $upgrade) {
                $empty_upgrades[$upgrade->getName()] = 0;
            }
            $data->setUpgrades($empty_upgrades);
        }

        if ($request->isXmlHttpRequest()) {
            $received_data = json_decode($request->getContent(), true);
            exit(dump($received_data));
            /*
            $received_score = $received_data['score'];
            $user->setScore($received_score);
            $em->persist($user);
            $em->flush();
            return new JsonResponse(
                [
                    'status' => 'saved',
                ],
                Response::HTTP_OK
            );
            */
        } else {
            return $this->render('game/index.html.twig', [
                'page' => 'game',
                'currentUser' => $user,
                'data' => $data
            ]);
        }
    }
}
