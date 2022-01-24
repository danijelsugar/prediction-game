<?php

namespace App\Controller;

use App\Service\FootballDataInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PredictionController extends AbstractController
{
    /**
     * @Route("/competitions/{id}/predictions", name="app_prediction", requirements={"id"="\d{4}"})
     */
    public function predictions(int $id): Response 
    {
        return $this->render('prediction/index.html.twig', [
            'competitionId' => $id
        ]);
    }

    /**
     * @Route(
     * "competitions/{id}/predictions/round/{round}", 
     * name="app_prediction_round", 
     * requirements={"id"="\d{4}"})
     */
    public function predictionsRound(
        int $id, 
        $round
    ): Response {

        return $this->render('prediction/prediction_round.html.twig', [
            'competitionId' => $id,
            'round' => $round
        ]);
    }

    public function predictionsCache(
        $id,
        FootballDataInterface $footballData
    ): Response {

        $roundsMatches = $footballData->fetchData(
            'competitions/' . $id . '/matches'
        );

        $rounds = $footballData->getPredictionRoundsInfo($roundsMatches->matches);

        $response = $this->render('prediction/rounds_cache.html.twig', [
            'competitionId' => $id,
            'competitionName' => $roundsMatches->competition->area->name . ' - ' . $roundsMatches->competition->name,
            'rounds' => $rounds
        ]);

        $response->setPublic();
        $response->setMaxAge(120);

        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    public function predictionsRoundCache(
        $id,
        $round,
        FootballDataInterface $footballData
    ): Response {

        if (is_numeric($round)) {
            $roundMatches = $footballData->fetchData(
                'competitions/' . $id . '/matches',
                [
                    'matchday' => $round
                ]
            );
        } elseif (is_string($round)) {
            $roundMatches = $footballData->fetchData(
                'competitions/' . $id . '/matches',
                [
                    'stage' => $round
                ]
            );
        } else {
            throw $this->createNotFoundException('Page not found');
        }

        $roundsMatches = $footballData->fetchData(
            'competitions/' . $id . '/matches'
        );

        $rounds = $footballData->getPredictionRoundsInfo($roundsMatches->matches);
        
        $rounds = array_keys($rounds);

        $currentRoundKey = array_search($round, $rounds);

        if ($currentRoundKey === false) {
            throw $this->createNotFoundException('Page not found');
        }
        
        if (!array_key_exists($currentRoundKey, $rounds)) {
            throw $this->createNotFoundException('Page not found');
        }
        
        $prevRound = $currentRoundKey - 1;
        $prevRound = \key_exists($prevRound, $rounds) ? $rounds[$prevRound] : null;
        
        $nextRound = $currentRoundKey + 1;
        $nextRound = \key_exists($nextRound, $rounds) ? $rounds[$nextRound] : null;

        $rounds = ['prev' => $prevRound, 'next' => $nextRound];

        $response = $this->render('prediction/prediction_round_cache.html.twig', [
            'competitionId' => $id,
            'round' => $round,
            'roundMatches' => $roundMatches,
            'rounds' => $rounds
        ]);

        $response->setPublic();
        $response->setMaxAge(120);

        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
