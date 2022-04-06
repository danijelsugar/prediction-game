<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StandingsController extends AbstractController
{
    /**
     * @Route("/standings", name="app_standings")
     */
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy(
            [],
            ['points' => 'DESC']
        );

        return $this->render('standings/overall.html.twig', [
            'users' => $users,
        ]);
    }
}
