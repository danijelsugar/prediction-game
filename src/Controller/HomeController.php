<?php

namespace App\Controller;

use App\Service\FootballDataService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function home(FootballDataService $footballDataService): Response
    {
        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );

        return $this->render('home/index.html.twig', [
            'competitions' => $competitions,
        ]);
    }
}
