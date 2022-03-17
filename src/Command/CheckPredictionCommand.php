<?php

namespace App\Command;

use App\Repository\PredictionRepository;
use App\Service\FootballDataService;
use App\Service\PointService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\Exception\ClientException;

class CheckPredictionCommand extends Command
{
    private PredictionRepository $predictionRepository;

    private FootballDataService $footballData;

    private EntityManagerInterface $entityManager;

    private PointService $pointService;

    private LoggerInterface $logger;

    protected static $defaultName = 'app:check:prediction';

    public function __construct(
        PredictionRepository $predictionRepository,
        FootballDataService $footballData,
        EntityManagerInterface $entityManager,
        PointService $pointService,
        LoggerInterface $logger
    ) {
        $this->predictionRepository = $predictionRepository;
        $this->footballData = $footballData;
        $this->entityManager = $entityManager;
        $this->pointService = $pointService;
        $this->logger = $logger;

        parent::__construct();
    }

    public function configure(): void
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

        $dateFrom = !empty($startDates) ? min($startDates)->format('Y-m-d') : '';

        $dateTo = (new \DateTime())->format('Y-m-d');

        try {
            $matches = $this->footballData->fetchData(
                'matches',
                [
                    'competitions' => implode(',', $competitions),
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'status' => 'FINISHED',
                ]
            );
        } catch (ClientException $e) {
            $io->info($e->getMessage());
            $this->logger->info($this->getDefaultName().': '.$e->getMessage());

            return Command::FAILURE;
        }

        foreach ($matches->matches as $match) {
            $prediction = $this->predictionRepository->findOneBy(
                [
                    'matchId' => $match->id,
                    'competition' => $match->competition->id,
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
