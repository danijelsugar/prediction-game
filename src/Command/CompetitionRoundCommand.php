<?php

namespace App\Command;

use App\Entity\Round;
use App\Repository\CompetitionRepository;
use App\Repository\RoundRepository;
use App\Service\FootballDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CompetitionRoundCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private CompetitionRepository $competitionRepository;

    private FootballDataService $footballData;

    private RoundRepository $roundRepository;

    protected static $defaultName = 'app:get:competition:round';

    public function __construct(
        EntityManagerInterface $entityManager,
        CompetitionRepository $competitionRepository,
        FootballDataService $footballData,
        RoundRepository $roundRepository
    ) {
        $this->entityManager = $entityManager;
        $this->competitionRepository = $competitionRepository;
        $this->footballData = $footballData;
        $this->roundRepository = $roundRepository;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Get all rounds for each competition');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $competitionsCount = $this->competitionRepository->competitionsCount();

        $io = new SymfonyStyle($input, $output);

        if ($competitionsCount <= 0) {
            $io->success('No competitions found.');

            return Command::SUCCESS;
        }

        $timesToRepeat = ceil($competitionsCount / 6);

        $offset = 0;
        for ($i = 0; $i < $timesToRepeat; ++$i) {
            $competitions = $this->competitionRepository->findBy(
                [],
                [],
                6,
                $offset
            );

            foreach ($competitions as $competition) {
                $roundsMatches = $this->footballData->fetchData(
                    'competitions/'.$competition->getCompetition().'/matches'
                );

                $rounds = $this->footballData->getPredictionRoundsInfo($roundsMatches->matches);

                $dates = $this->footballData->getMatchdayDates($rounds);

                $firstAndLastDate = $this->footballData->getFirstAndLastMatchdayDate($dates);

                $roundStatus = $this->footballData->getRoundStatus($rounds);

                foreach ($firstAndLastDate as $key => $value) {
                    $round = $this->roundRepository->findOneBy(
                        [
                            'competition' => $competition,
                            'name' => $key,
                        ]
                    );

                    if (!$round) {
                        $round = new Round();
                        $round
                            ->setCompetition($competition)
                            ->setName($key)
                            ->setDateFrom($value['dateFrom'])
                            ->setDateTo($value['dateTo'])
                            ->setStatus($roundStatus[$key]);
                        $this->entityManager->persist($round);
                    } else {
                        $round->setDateFrom($value['dateFrom'])
                            ->setDateTo($value['dateTo'])
                            ->setStatus($roundStatus[$key]);
                    }
                }

                $this->entityManager->flush();
            }

            $offset += 6;

            sleep(60);
        }

        $io->success('Finished inserting competition rounds.');

        return Command::SUCCESS;
    }
}
