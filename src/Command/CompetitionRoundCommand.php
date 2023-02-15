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
    protected static $defaultName = 'app:get:competition:round';
    protected static $defaultDescription = 'Get all rounds for each competition';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CompetitionRepository $competitionRepository,
        private FootballInterface $footballData,
        private FootballDataService $footballDataService,
        private RoundRepository $roundRepository
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
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

                $rounds = $this->footballDataService->getRoundInfo($competitionMatches);

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
