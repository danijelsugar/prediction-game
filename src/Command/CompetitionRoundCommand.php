<?php

namespace App\Command;

use App\Entity\Round;
use App\Repository\CompetitionRepository;
use App\Repository\RoundRepository;
use App\Service\FootballDataService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\Exception\ClientException;

class CompetitionRoundCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private CompetitionRepository $competitionRepository;

    private FootballDataService $footballData;

    private RoundRepository $roundRepository;

    private LoggerInterface $logger;

    protected static $defaultName = 'app:get:competition:round';

    public function __construct(
        EntityManagerInterface $entityManager,
        CompetitionRepository $competitionRepository,
        FootballDataService $footballData,
        RoundRepository $roundRepository,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->competitionRepository = $competitionRepository;
        $this->footballData = $footballData;
        $this->roundRepository = $roundRepository;
        $this->logger = $logger;

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
                try {
                    $roundsMatches = $this->footballData->fetchData(
                        'competitions/'.$competition->getCompetition().'/matches'
                    );
                } catch (ClientException $e) {
                    $this->logger->info(sprintf('Cannot get rounds for competition: %s', $competition->getName()));
                    continue;
                }

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
                        if ($value['dateFrom']->format('Y-m-d H:i:s') !== $round->getDateFrom()->format('Y-m-d H:i:s') || $value['dateTo']->format('Y-m-d H:i:s') !== $round->getDateTo()->format('Y-m-d H:i:s') || $roundStatus[$key] !== $round->getStatus()) {
                            $round->setDateFrom($value['dateFrom'])
                                ->setDateTo($value['dateTo'])
                                ->setStatus($roundStatus[$key]);
                        }
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
