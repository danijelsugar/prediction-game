<?php

namespace App\Command;

use App\Helper\FootballInterface;
use App\Repository\CompetitionRepository;
use App\Repository\PredictionRepository;
use App\Repository\RoundMatchRepository;
use App\Service\PointService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\Exception\ClientException;

#[AsCommand('app:check:prediction', 'Checks the outcome of predictions and calculates the points earned.')]
class CheckPredictionCommand extends Command
{
    public function __construct(
        private PredictionRepository $predictionRepository,
        private FootballInterface $footballData,
        private EntityManagerInterface $entityManager,
        private PointService $pointService,
        private LoggerInterface $logger,
        private RoundMatchRepository $roundMatchRepository,
        private CompetitionRepository $competitionRepository
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
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

        /** @var \DateTime */
        $dateTo = clone $dateFrom;
        $dateTo->modify('+10 days');

        try {
            $matches = $this->footballData->getMatches([
                'competitions' => implode(',', $competitions),
                'dateFrom' => $dateFrom->format('Y-m-d'),
                'dateTo' => $dateTo->format('Y-m-d'),
                'status' => 'FINISHED',
            ]);
        } catch (ClientException $e) {
            $io->info($e->getResponse()->getContent(false));
            $this->logger->info(static::getDefaultName().': '.$e->getResponse()->getContent(false));

            return Command::FAILURE;
        }

        foreach ($matches as $match) {
            $predictionMatch = $this->roundMatchRepository->findOneBy(
                [
                    'matchId' => $match->matchId,
                ]
            );

            $competition = $this->competitionRepository->findOneBy(
                [
                    'competition' => $match->competitionId,
                ]
            );

            if ($predictionMatch) {
                $predictionMatch
                    ->setStage($match->stage)
                    ->setGroupName($match->groupName)
                    ->setDate($match->date)
                    ->setHomeTeamName($match->homeTeamName)
                    ->setAwayTeamName($match->awayTeamName)
                    ->setFullTimeHomeTeamScore($match->fullTimeHomeTeamScore)
                    ->setFullTimeAwayTeamScore($match->fullTimeAwayTeamScore)
                    ->setExtraTimeHomeTeamScore($match->extraTimeHomeTeamScore)
                    ->setExtraTimeAwayTeamScore($match->extraTimeAwayTeamScore)
                    ->setWinner($match->winner)
                    ->setLastUpdated($match->lastUpdated);

                $predictions = $this->predictionRepository->findPredictions($predictionMatch, $competition);
            }

            if ($predictions) {
                foreach ($predictions as $prediction) {
                    $points = $this->pointService->calculatePoints($match, $prediction);

                    $userPoints = $prediction->getUser()->getPoints() + $points;
                    $prediction->getUser()->setPoints($userPoints);

                    $prediction
                        ->setHomeTeamScore($match->fullTimeHomeTeamScore)
                        ->setAwayTeamScore($match->fullTimeAwayTeamScore)
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
