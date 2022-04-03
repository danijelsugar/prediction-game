<?php

namespace App\Controller;

use App\Service\FootballDataService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;

class StatisticsController extends AbstractController
{

    /**
     * @Route("/match/{id}", name="app_match_statistics")
     */
    public function match(
        int $id,
        FootballDataService $footballData
    ): Response {
        try {
            $match = $footballData->fetchData(
                'matches/'.$id
            );
        } catch (ClientException $e) {
        }
        
        return $this->render('statistics/match.html.twig', [
            'match' => $match,
        ]);
    }
}
