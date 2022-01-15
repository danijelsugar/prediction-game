<?php

namespace App\Controller;

use App\Service\FootballDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PredictionController extends AbstractController
{
    /**
     * @Route("/prediction/{id}", name="app_prediction")
     */
    public function index($id, FootballDataService $footballDataService): Response
    {
        $competitions = $footballDataService->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE'
            ]
        );

        $competitionMatches = $footballDataService->fetchData(
            'competitions/' . $id . '/matches'
        );

        $matches = [];
        $round = [];
        $matchday = false;
        $count = 0;

        foreach ($competitionMatches->matches as $match) {
            if ($matchday !== $match->matchday) {
                $matchday = $match->matchday;

                if (!in_array($matchday, $round)) {
                    $round[] = $matchday;
                    $count++;
                    echo $matchday . '<br>';
                }
            }
        }
        $matches[] = [
            'matchday' => $round,
            'count' => $count
        ];
        $m[] = [
            'matchday' => 1,
            'count' => 10
        ];
        dd($matches);

        return $this->render('prediction/index.html.twig', [
            'competitionId' => $id,
            'competitions' => $competitions,
            'competitionMatches' => $competitionMatches
        ]);
    }
}
