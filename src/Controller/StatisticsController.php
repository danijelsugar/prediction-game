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
                $head2Head = $this->footballData->getHead2Head($id);
            }
        } catch (ClientException) {
            $matchDto = null;
        }

        $match = null;

        if (!is_null($matchDto)) {
            $match = [
                'date' => $matchDto->getDate(),
                'homeTeamName' => $matchDto->getHomeTeamName(),
                'awayTeamName' => $matchDto->getAwayTeamName(),
                'fullTimeHomeTeamScore' => $matchDto->getFullTimeHomeTeamScore(),
                'fullTimeAwayTeamScore' => $matchDto->getFullTimeAwayTeamScore(),
            ];

            if (isset($head2Head)) {
                $match['head2head'] = $head2Head;
            } else {
                $match['head2head'] = $matchDto->getHead2Head();
            }
        }

        return $this->render('statistics/match.html.twig', [
            'match' => $match,
        ]);
    }
}
