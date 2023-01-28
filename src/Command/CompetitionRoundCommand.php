<?php

namespace App\Command;

use App\Entity\Round;
use App\Helper\FootballInterface;
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

    private FootballInterface $footballData;

    private FootballDataService $footballDataService;

    private RoundRepository $roundRepository;

    protected static $defaultName = 'app:get:competition:round';

    public function __construct(
        EntityManagerInterface $entityManager,
        CompetitionRepository $competitionRepository,
        FootballInterface $footballDataNew,
        FootballDataService $footballDataService,
        RoundRepository $roundRepository
    ) {
        $this->entityManager = $entityManager;
        $this->competitionRepository = $competitionRepository;
        $this->footballData = $footballDataNew;
        $this->footballDataService = $footballDataService;
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
                $competitionMatches = $this->footballData->getCompetitionMatches($competition->getCompetition());

                $matches = $this->footballDataService->getMatchesInfo($competitionMatches);

                $rounds = $this->footballDataService->getRoundInfo($matches);

                foreach ($rounds as $key => $value) {
                    $round = $this->roundRepository->findOneBy(
                        [
                            'competition' => $competition,
                            'name' => $key,
                        ]
                    );

                    $firstAndLastDate = $this->footballDataService->getFirstAndLastMatchdayDate($value);
                    $status = $this->footballDataService->getRoundStatus($value);
                    $stage = $this->footballDataService->getRoundStage($value);

                    if (!$round) {
                        $round = new Round();
                        $round
                            ->setCompetition($competition)
                            ->setName($key)
                            ->setStage($stage)
                            ->setDateFrom($firstAndLastDate['dateFrom'])
                            ->setDateTo($firstAndLastDate['dateTo'])
                            ->setStatus($status);
                        $this->entityManager->persist($round);
                    } else {
                        $round
                            ->setStage($stage)
                            ->setDateFrom($firstAndLastDate['dateFrom'])
                            ->setDateTo($firstAndLastDate['dateTo'])
                            ->setStatus($status);
                    }
                }

                $this->entityManager->flush();
            }

            $offset += 6;

            sleep(57);
        }

        $io->success('Finished inserting/updateing competition rounds.');

        return Command::SUCCESS;
    }
}
