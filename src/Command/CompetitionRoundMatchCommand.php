<?php

namespace App\Command;

use App\Entity\RoundMatch;
use App\Helper\FootballInterface;
use App\Repository\RoundMatchRepository;
use App\Repository\RoundRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:get:competition:round:match', 'Get all matches for each round of each competition')]
class CompetitionRoundMatchCommand extends Command
{
    public function __construct(
        private RoundRepository $roundRepository,
        private FootballInterface $footballData,
        private EntityManagerInterface $entityManager,
        private RoundMatchRepository $roundMatchRepository
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
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
                    $roundMatches = $this->footballData->getCompetitionMatches($round->getCompetition()->getCompetition(), [
                        'matchday' => $round->getName(),
                        'stage' => $round->getStage(),
                    ]);
                } elseif (is_string($round->getName())) {
                    $roundMatches = $this->footballData->getCompetitionMatches($round->getCompetition()->getCompetition(), [
                        'stage' => $round->getStage(),
                    ]);
                } else {
                    throw new LogicException();
                }

                foreach ($roundMatches as $match) {
                    $roundMatch = $this->roundMatchRepository->findOneBy(
                        [
                            'round' => $round,
                            'matchId' => $match->matchId,
                        ]
                    );

                    if (!$roundMatch) {
                        $roundMatch = new RoundMatch();
                        $roundMatch
                            ->setRound($round)
                            ->setMatchId($match->matchId)
                            ->setDate($match->date)
                            ->setHomeTeamName($match->homeTeamName)
                            ->setAwayTeamName($match->awayTeamName)
                            ->setFullTimeHomeTeamScore(fullTimeHomeTeamScore: $match->fullTimeHomeTeamScore)
                            ->setFullTimeAwayTeamScore($match->fullTimeAwayTeamScore)
                            ->setExtraTimeHomeTeamScore($match->extraTimeHomeTeamScore)
                            ->setExtraTimeAwayTeamScore($match->extraTimeAwayTeamScore)
                            ->setWinner($match->winner)
                            ->setStage($match->stage)
                            ->setGroupName($match->groupName)
                            ->setLastUpdated($match->lastUpdated);
                        $this->entityManager->persist($roundMatch);
                    } else {
                        if ($match->lastUpdated->format('Y-m-d H:i:s') !== $roundMatch->getLastUpdated()->format('Y-m-d H:i:s')) {
                            $roundMatch
                                ->setDate($match->date)
                                ->setHomeTeamName($match->homeTeamName)
                                ->setAwayTeamName($match->awayTeamName)
                                ->setFullTimeHomeTeamScore($match->fullTimeHomeTeamScore)
                                ->setFullTimeAwayTeamScore($match->fullTimeAwayTeamScore)
                                ->setExtraTimeHomeTeamScore($match->extraTimeHomeTeamScore)
                                ->setExtraTimeAwayTeamScore($match->extraTimeAwayTeamScore)
                                ->setWinner($match->winner)
                                ->setStage($match->stage)
                                ->setGroupName($match->groupName)
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
