<?php

namespace App\Controller;

use App\Service\FootballDataInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    /**
     * @Route("/match/{id}", name="app_match_statistics")
     */
    public function match(
        int $id,
        FootballDataInterface $footballData
    ): Response {
        try {
            $match = $footballData->fetchData(
                'matches/'.$id
            );
        } catch (ClientException $e) {
            $match = null;
        }

        return $this->render('statistics/match.html.twig', [
            'match' => $match,
        ]);
    }
}
