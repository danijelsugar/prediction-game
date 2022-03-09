<?php

namespace App\Command;

use App\Entity\Competition;
use App\Repository\CompetitionRepository;
use App\Service\FootballDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CompetitionCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private FootballDataService $footballData;

    private CompetitionRepository $competitionRepository;

    protected static $defaultName = 'app:get:competition';

    public function __construct(
        EntityManagerInterface $entityManager,
        FootballDataService $footballData,
        CompetitionRepository $competitionRepository
    ) {
        $this->entityManager = $entityManager;
        $this->footballData = $footballData;
        $this->competitionRepository = $competitionRepository;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Get all competitions and inserts them if they dont exist');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $apiCompetitions = $this->footballData->fetchData(
            'competitions',
            [
                'plan' => 'TIER_ONE',
            ]
        );

        foreach ($apiCompetitions->competitions as $apiCompetition) {
            $competition = $this->competitionRepository->findOneBy(
                [
                    'competition' => $apiCompetition->id,
                ]
            );

            if (!$competition) {
                $competition = new Competition();
                $competition
                    ->setCompetition($apiCompetition->id)
                    ->setName($apiCompetition->name)
                    ->setArea($apiCompetition->area->name)
                    ->setLastUpdated($apiCompetition->lastUpdated);
                $this->entityManager->persist($competition);
            } else {
                if ($apiCompetition->lastUpdated !== $competition->getLastUpdated()) {
                    $competition
                        ->setName($apiCompetition->name)
                        ->setArea($apiCompetition->area->name)
                        ->setLastUpdated($apiCompetition->lastUpdated);
                }
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('Finished inserting competitions.'));

        return Command::SUCCESS;
    }
}
