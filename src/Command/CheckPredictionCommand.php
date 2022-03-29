<?php

namespace App\Command;

use App\Repository\PredictionRepository;
use App\Repository\RoundMatchRepository;
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

    private RoundMatchRepository $roundMatchRepository;

    protected static $defaultName = 'app:check:prediction';

    public function __construct(
        PredictionRepository $predictionRepository,
        FootballDataService $footballData,
        EntityManagerInterface $entityManager,
        PointService $pointService,
        LoggerInterface $logger,
        RoundMatchRepository $roundMatchRepository
    ) {
        $this->predictionRepository = $predictionRepository;
        $this->footballData = $footballData;
        $this->entityManager = $entityManager;
        $this->pointService = $pointService;
        $this->logger = $logger;
        $this->roundMatchRepository = $roundMatchRepository;

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
            if (!in_array($prediction->getCompetition()->getCompetition(), $competitions)) {
                $competitions[] = $prediction->getCompetition()->getCompetition();
            }
            $startDates[] = $prediction->getMatchStartTime();
        }

        $dateFrom = !empty($startDates) ? min($startDates) : new \DateTime();

        $dateTo = clone $dateFrom;
        $dateTo->modify('+10 days');

        try {
            $competitionsMatches = $this->footballData->fetchData(
                'matches',
                [
                    'competitions' => implode(',', $competitions),
                    'dateFrom' => $dateFrom->format('Y-m-d'),
                    'dateTo' => $dateTo->format('Y-m-d'),
                    'status' => 'FINISHED',
                ]
            );
        } catch (ClientException $e) {
            $io->info($e->getResponse()->getContent(false));
            $this->logger->info($this->getDefaultName().': '.$e->getResponse()->getContent(false));

            return Command::FAILURE;
        }

        foreach ($competitionsMatches->matches as $match) {
            $predictionMatch = $this->roundMatchRepository->findOneBy(
                [
                    'matchId' => $match->id,
                ]
            );

            if ($predictionMatch) {
                $predictionMatch
                    ->setStage($match->stage)
                    ->setGroupName($match->group)
                    ->setDate(new \DateTime($match->utcDate))
                    ->setHomeTeamName($match->homeTeam->name)
                    ->setAwayTeamName($match->awayTeam->name)
                    ->setFullTimeHomeTeamScore($match->score->fullTime->homeTeam)
                    ->setFullTimeAwayTeamScore($match->score->fullTime->awayTeam)
                    ->setExtraTimeHomeTeamScore($match->score->extraTime->homeTeam)
                    ->setExtraTimeAwayTeamScore($match->score->extraTime->awayTeam)
                    ->setWinner($match->score->winner)
                    ->setLastUpdated($match->lastUpdated);
            }

            $predictions = $this->predictionRepository->findPredictions($predictionMatch, $match->competition->id);

            if ($predictions) {
                foreach ($predictions as $prediction) {
                    $points = $this->pointService->calculatePoints($match, $prediction);

                    $userPoints = $prediction->getUser()->getPoints() + $points;
                    $prediction->getUser()->setPoints($userPoints);

                    $prediction
                        ->setHomeTeamScore($match->score->fullTime->homeTeam)
                        ->setAwayTeamScore($match->score->fullTime->awayTeam)
                        ->setFinished(true)
                        ->setPoints($points);
                }
            }
        }
        $this->entityManager->flush();

        $io->success('Finished checking user predictions.');

        return Command::SUCCESS;
    }
}
