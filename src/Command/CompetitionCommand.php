<?php

namespace App\Command;

use App\Entity\Competition;
use App\Helper\FootballInterface;
use App\Repository\CompetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:get:competition', 'Get all competitions and inserts them if they dont exist')]
class CompetitionCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FootballInterface $footballData,
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

        $competitionDtos = $this->footballData->getCompetitions([
            'plan' => 'TIER_ONE',
        ]);

        $inserted = 0;
        $updated = 0;

        foreach ($competitionDtos as $competitionDto) {
            $competition = $this->competitionRepository->findOneBy(
                [
                    'competition' => $competitionDto->competition,
                ]
            );
            if (!$competition) {
                $competition = new Competition();
                $competition
                    ->setCompetition($competitionDto->competition)
                    ->setName($competitionDto->name)
                    ->setCode($competitionDto->code)
                    ->setArea($competitionDto->area)
                    ->setLastUpdated($competitionDto->lastUpdated)
                    ->setEmblemUrl($competitionDto->emblemUrl);
                ++$inserted;
                $this->entityManager->persist($competition);
            } else {
                if ($competitionDto->lastUpdated->format('Y-m-d H:i:s') !== $competition->getLastUpdated()->format('Y-m-d H:i:s')) {
                    $competition
                        ->setName($competitionDto->name)
                        ->setCode($competitionDto->code)
                        ->setArea($competitionDto->area)
                        ->setLastUpdated($competitionDto->lastUpdated)
                        ->setEmblemUrl($competitionDto->emblemUrl);
                    ++$updated;
                }
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('Inserted %s, updated %s', $inserted, $updated));

        return Command::SUCCESS;
    }
}
