<?php

namespace App\Command;

use App\Entity\RoundMatch;
use App\Repository\RoundMatchRepository;
use App\Repository\RoundRepository;
use App\Service\FootballDataService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\Exception\ClientException;

class CompetitionRoundMatchCommand extends Command
{
    private RoundRepository $roundRepository;

    private FootballDataService $footballData;

    private EntityManagerInterface $entityManager;

    private RoundMatchRepository $roundMatchRepository;

    private LoggerInterface $logger;

    protected static $defaultName = 'app:get:competition:round:match';

    public function __construct(
        RoundRepository $roundRepository,
        FootballDataService $footballData,
        EntityManagerInterface $entityManager,
        RoundMatchRepository $roundMatchRepository,
        LoggerInterface $logger
    ) {
        $this->roundRepository = $roundRepository;
        $this->footballData = $footballData;
        $this->entityManager = $entityManager;
        $this->roundMatchRepository = $roundMatchRepository;
        $this->logger = $logger;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Get all matches for each round of each competition');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $roundsCount = $this->roundRepository->roundCount();

        $io = new SymfonyStyle($input, $output);

        if ($roundsCount <= 0) {
            $io->success('No rounds found.');

            return Command::SUCCESS;
        }

        $timesToRepeat = ceil($roundsCount / 6);

        $offset = 0;
        for ($i = 0; $i < $timesToRepeat; ++$i) {
            $rounds = $this->roundRepository->findBy(
                [],
                [],
                6,
                $offset
            );

            foreach ($rounds as $round) {
                if (is_numeric($round->getName())) {
                    try {
                        $roundMatches = $this->footballData->fetchData(
                            'competitions/'.$round->getCompetition()->getCompetition().'/matches',
                            [
                                'matchday' => $round->getName(),
                            ]
                        );
                    } catch (ClientException $e) {
                        $this->logger->info(sprintf('Cannot get matches for round %s of %s', $round->getName(), $round->getCompetition()->getName()));
                        continue;
                    }
                } elseif (is_string($round->getName())) {
                    try {
                        $roundMatches = $this->footballData->fetchData(
                            'competitions/'.$round->getCompetition()->getCompetition().'/matches',
                            [
                                'stage' => $round->getName(),
                            ]
                        );
                    } catch (ClientException $e) {
                        $this->logger->info(sprintf('Cannot get matches for round %s of %s', $round->getName(), $round->getCompetition()->getName()));
                        continue;
                    }
                } else {
                    throw new LogicException();
                }

                foreach ($roundMatches->matches as $match) {
                    $roundMatch = $this->roundMatchRepository->findOneBy(
                        [
                            'round' => $round,
                            'matchId' => $match->id,
                        ]
                    );

                    if (!$roundMatch) {
                        $roundMatch = new RoundMatch();
                        $roundMatch
                            ->setRound($round)
                            ->setMatchId($match->id)
                            ->setDate(new \DateTime($match->utcDate))
                            ->setHomeTeamName($match->homeTeam->name)
                            ->setAwayTeamName($match->awayTeam->name)
                            ->setFullTimeHomeTeamScore($match->score->fullTime->homeTeam)
                            ->setFullTimeAwayTeamScore($match->score->fullTime->awayTeam)
                            ->setExtraTimeHomeTeamScore($match->score->extraTime->homeTeam)
                            ->setExtraTimeAwayTeamScore($match->score->extraTime->awayTeam)
                            ->setWinner($match->score->winner)
                            ->setStage($match->stage)
                            ->setGroupName($match->group)
                            ->setLastUpdated($match->lastUpdated);
                        $this->entityManager->persist($roundMatch);
                    } else {
                        if ($match->lastUpdated !== $roundMatch->getLastUpdated()) {
                            $roundMatch->setDate(new \DateTime($match->utcDate))
                            ->setHomeTeamName($match->homeTeam->name)
                            ->setAwayTeamName($match->awayTeam->name)
                            ->setFullTimeHomeTeamScore($match->score->fullTime->homeTeam)
                            ->setFullTimeAwayTeamScore($match->score->fullTime->awayTeam)
                            ->setExtraTimeHomeTeamScore($match->score->extraTime->homeTeam)
                            ->setExtraTimeAwayTeamScore($match->score->extraTime->awayTeam)
                            ->setWinner($match->score->winner)
                            ->setStage($match->stage)
                            ->setGroupName($match->group)
                            ->setLastUpdated($match->lastUpdated);
                        }
                    }
                }

                $this->entityManager->flush();
            }

            $offset += 6;

            sleep(60);
        }

        $io->success('Finished inserting rounds matches.');

        return Command::SUCCESS;
    }
}
