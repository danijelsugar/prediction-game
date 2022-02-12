<?php

namespace App\Command;

use App\Repository\PredictionRepository;
use App\Service\FootballDataService;
use App\Service\PointService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckPredictionCommand extends Command
{
    private PredictionRepository $predictionRepository;

    private FootballDataService $footballData;

    private EntityManagerInterface $entityManager;

    private PointService $pointService;

    protected static $defaultName = 'app:check:prediction';

    public function __construct(
        PredictionRepository $predictionRepository,
        FootballDataService $footballData,
        EntityManagerInterface $entityManager,
        PointService $pointService
    ) {
        $this->predictionRepository = $predictionRepository;
        $this->footballData = $footballData;
        $this->entityManager = $entityManager;
        $this->pointService = $pointService;

        parent::__construct();
    }

    public function configure()
    {
        $this->setDescription('Checks the outcome of predictions and calculates the points earned.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $predictions = $this->predictionRepository->findBy([
            'finished' => false,
            'points' => null,
        ]);

        $competitions = [];
        $startDates = [];

        foreach ($predictions as $prediction) {
            if (!in_array($prediction->getCompetition(), $competitions)) {
                $competitions[] = $prediction->getCompetition();
            }
            $startDates[] = $prediction->getMatchStartTime();
        }

        //$minDate = !empty($startDates) ? min($startDates)->format('Y-m-d') : '';

        $matchess = $this->footballData->fetchData(
            'matches',
            [
                'competitions' => implode(',', $competitions),
            ]
        );

        $matches = [
            [
                'id' => 327126,
                'competition' => 2021,
                'homeTeamScore' => 2,
                'awayTeamScore' => 0,
            ],
            [
                'id' => 327128,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 2,
            ],
            [
                'id' => 327130,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 1,
            ],
            [
                'id' => 327117,
                'competition' => 2021,
                'homeTeamScore' => 2,
                'awayTeamScore' => 0,
            ],
            [
                'id' => 327113,
                'competition' => 2021,
                'homeTeamScore' => 3,
                'awayTeamScore' => 1,
            ],
            [
                'id' => 327119,
                'competition' => 2021,
                'homeTeamScore' => 0,
                'awayTeamScore' => 1,
            ],
            [
                'id' => 327120,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 2,
            ],
            [
                'id' => 327122,
                'competition' => 2021,
                'homeTeamScore' => 0,
                'awayTeamScore' => 0,
            ],
            [
                'id' => 327115,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 0,
            ],
            [
                'id' => 327114,
                'competition' => 2021,
                'homeTeamScore' => 2,
                'awayTeamScore' => 4,
            ],
            [
                'id' => 327116,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 0,
            ],
            [
                'id' => 327121,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 2,
            ],
            [
                'id' => 327118,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 1,
            ],
            [
                'id' => 327131,
                'competition' => 2021,
                'homeTeamScore' => 1,
                'awayTeamScore' => 1,
            ],
        ];

        foreach ($matches as $match) {
            $prediction = $this->predictionRepository->findOneBy(
                [
                    'matchId' => $match['id'],
                    'competition' => $match['competition'],
                ]
            );

            if ($prediction) {
                $points = $this->pointService->calculatePoints($match, $prediction);

                $prediction
                    ->setHomeTeamScore($match['homeTeamScore'])
                    ->setAwayTeamScore($match['awayTeamScore'])
                    ->setFinished(true)
                    ->setPoints($points);
            }
        }
        $this->entityManager->flush();

        $io->success('Finished checking user predictions.');

        return Command::SUCCESS;
    }
}
