<?php

namespace App\Controller;

use App\Helper\FootballDataNew;
use App\Helper\FootballInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    public function __construct(private FootballInterface $footballData)
    {
    }

    #[Route(path: '/match/{id}', name: 'app_match_statistics')]
    public function match(int $id): Response
    {
        try {
            $matchDto = $this->footballData->getMatch($id);
            if ($this->footballData instanceof FootballDataNew) {
                $headToHead = $this->footballData->getHeadToHead($id);
            }
        } catch (ClientException) {
            $matchDto = null;
        }

        $match = null;

        if (!is_null($matchDto)) {
            $match = [
                'date' => $matchDto->date,
                'homeTeamName' => $matchDto->homeTeamName,
                'awayTeamName' => $matchDto->awayTeamName,
                'fullTimeHomeTeamScore' => $matchDto->fullTimeHomeTeamScore,
                'fullTimeAwayTeamScore' => $matchDto->fullTimeAwayTeamScore,
            ];

            if (isset($headToHead)) {
                $match['headToHead'] = $headToHead;
            } else {
                $match['headToHead'] = $matchDto->headToHead;
            }
        }

        return $this->render('statistics/match.html.twig', [
            'match' => $match,
        ]);
    }
}
