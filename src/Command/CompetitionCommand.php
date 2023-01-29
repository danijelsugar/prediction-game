<?php

namespace App\Command;

use App\Dto\CompetitionDto;
use App\Entity\Competition;
use App\Helper\FootballInterface;
use App\Repository\CompetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CompetitionCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private FootballInterface $footballData;

    private CompetitionRepository $competitionRepository;

    protected static $defaultName = 'app:get:competition';

    public function __construct(
        EntityManagerInterface $entityManager,
        FootballInterface $footballDataNew,
        CompetitionRepository $competitionRepository
    ) {
        $this->entityManager = $entityManager;
        $this->footballData = $footballDataNew;
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

        /** @var CompetitionDto[] $competitionDtos */
        $competitionDtos = $this->footballData->getCompetitions([
            'plan' => 'TIER_ONE',
        ]);

        $inserted = 0;
        $updated = 0;

        foreach ($competitionDtos as $competitionDto) {
            $competition = $this->competitionRepository->findOneBy(
                [
                    'competition' => $competitionDto->getCompetition(),
                ]
            );
            if (!$competition) {
                $competition = new Competition();
                $competition
                    ->setCompetition($competitionDto->getCompetition())
                    ->setName($competitionDto->getName())
                    ->setCode($competitionDto->getCode())
                    ->setArea($competitionDto->getArea())
                    ->setLastUpdated($competitionDto->getLastUpdated())
                    ->setEmblemUrl($competitionDto->getEmblemUrl());
                ++$inserted;
                $this->entityManager->persist($competition);
            } else {
                if ($competitionDto->getLastUpdated()->format('Y-m-d H:i:s') !== $competition->getLastUpdated()->format('Y-m-d H:i:s')) {
                    $competition
                        ->setName($competitionDto->getName())
                        ->setCode($competitionDto->getCode())
                        ->setArea($competitionDto->getArea())
                        ->setLastUpdated($competitionDto->getLastUpdated())
                        ->setEmblemUrl($competitionDto->getEmblemUrl());
                    ++$updated;
                }
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('Inserted %s, updated %s', $inserted, $updated));

        return Command::SUCCESS;
    }
}
