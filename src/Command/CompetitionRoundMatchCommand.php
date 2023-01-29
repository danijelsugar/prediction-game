<?php

namespace App\Command;

use App\Dto\MatchDto;
use App\Entity\RoundMatch;
use App\Repository\RoundMatchRepository;
use App\Repository\RoundRepository;
use App\Helper\FootballInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CompetitionRoundMatchCommand extends Command
{
    private RoundRepository $roundRepository;

    private FootballInterface $footballData;

    private EntityManagerInterface $entityManager;

    private RoundMatchRepository $roundMatchRepository;

    protected static $defaultName = 'app:get:competition:round:match';

    public function __construct(
        RoundRepository $roundRepository,
        FootballInterface $footballData,
        EntityManagerInterface $entityManager,
        RoundMatchRepository $roundMatchRepository
    ) {
        $this->roundRepository = $roundRepository;
        $this->footballData = $footballData;
        $this->entityManager = $entityManager;
        $this->roundMatchRepository = $roundMatchRepository;

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

                /** @var MatchDto[] $roundMatches */
                foreach ($roundMatches as $match) {
                    $roundMatch = $this->roundMatchRepository->findOneBy(
                        [
                            'round' => $round,
                            'matchId' => $match->getMatchId(),
                        ]
                    );

                    if (!$roundMatch) {
                        $roundMatch = new RoundMatch();
                        $roundMatch
                            ->setRound($round)
                            ->setMatchId($match->getMatchId())
                            ->setDate($match->getDate())
                            ->setHomeTeamName($match->getHomeTeamName())
                            ->setAwayTeamName($match->getAwayTeamName())
                            ->setFullTimeHomeTeamScore($match->getFullTimeHomeTeamScore())
                            ->setFullTimeAwayTeamScore($match->getFullTimeAwayTeamScore())
                            ->setExtraTimeHomeTeamScore($match->getExtraTimeHomeTeamScore())
                            ->setExtraTimeAwayTeamScore($match->getExtraTimeAwayTeamScore())
                            ->setWinner($match->getWinner())
                            ->setStage($match->getStage())
                            ->setGroupName($match->getGroupName())
                            ->setLastUpdated($match->getLastUpdated());
                        $this->entityManager->persist($roundMatch);
                    } else {
                        if ($match->getLastUpdated()->format('Y-m-d H:i:s') !== $roundMatch->getLastUpdated()->format('Y-m-d H:i:s')) {
                            $roundMatch
                                ->setDate($match->getDate())
                                ->setHomeTeamName($match->getHomeTeamName())
                                ->setAwayTeamName($match->getAwayTeamName())
                                ->setFullTimeHomeTeamScore($match->getFullTimeHomeTeamScore())
                                ->setFullTimeAwayTeamScore($match->getFullTimeAwayTeamScore())
                                ->setExtraTimeHomeTeamScore($match->getExtraTimeHomeTeamScore())
                                ->setExtraTimeAwayTeamScore($match->getExtraTimeAwayTeamScore())
                                ->setWinner($match->getWinner())
                                ->setStage($match->getStage())
                                ->setGroupName($match->getGroupName())
                                ->setLastUpdated($match->getLastUpdated());
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
