<?php

namespace App\Command;

use App\Entity\Competition;
use App\Repository\CompetitionRepository;
use App\Service\FootballDataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\Exception\ClientException;

class CompetitionCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private FootballDataInterface $footballData;

    private CompetitionRepository $competitionRepository;

    private LoggerInterface $logger;

    protected static $defaultName = 'app:get:competition';

    public function __construct(
        EntityManagerInterface $entityManager,
        FootballDataInterface $footballData,
        CompetitionRepository $competitionRepository,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->footballData = $footballData;
        $this->competitionRepository = $competitionRepository;
        $this->logger = $logger;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Get all competitions and inserts them if they dont exist');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $apiCompetitions = $this->footballData->fetchData(
                'competitions',
                [
                    'plan' => 'TIER_ONE',
                ]
            );
        } catch (ClientException $e) {
            $io->info($e->getMessage());
            $this->logger->info($this->getDefaultName().': '.$e->getMessage());

            return Command::FAILURE;
        }

        $inserted = 0;
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
                    ->setCode($apiCompetition->code)
                    ->setArea($apiCompetition->area->name)
                    ->setLastUpdated($apiCompetition->lastUpdated);
                if (null !== $apiCompetition->emblemUrl) {
                    $competition->setEmblemUrl($apiCompetition->emblemUrl);
                } else {
                    $competition->setEmblemUrl($apiCompetition->area->ensignUrl);
                }
                ++$inserted;
                $this->entityManager->persist($competition);
            } else {
                if ($apiCompetition->lastUpdated !== $competition->getLastUpdated()) {
                    $competition
                        ->setName($apiCompetition->name)
                        ->setCode($apiCompetition->code)
                        ->setArea($apiCompetition->area->name)
                        ->setLastUpdated($apiCompetition->lastUpdated);
                    if (null !== $apiCompetition->emblemUrl) {
                        $competition->setEmblemUrl($apiCompetition->emblemUrl);
                    } else {
                        $competition->setEmblemUrl($apiCompetition->area->ensignUrl);
                    }
                    ++$inserted;
                }
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('Finished inserting/updateing %s competitions.', $inserted));

        return Command::SUCCESS;
    }
}
