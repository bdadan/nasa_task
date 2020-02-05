<?php

namespace App\Command;

use App\Entity\Holiday;
use App\Service\DateCalculator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Doctrine\ORM\EntityManagerInterface;

class RefreshHolidaysCommand extends Command
{
    use LockableTrait;

    const YEAR = 2019;

    /**
     * @var DateCalculator
     */
    private $dateCalculator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(DateCalculator $dateCalculator, EntityManagerInterface $entityManager)
    {
        $this->dateCalculator = $dateCalculator;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Refreshes dates of holidays in db for 2019')
            ->setName('app:refresh:holidays')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $dates = $this->dateCalculator->getHolidaysDates();
        $repository = $this->entityManager->getRepository(Holiday::class);

        $results = $repository->findAll();

        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->entityManager->remove($result);
            }
            $this->entityManager->flush();
        }

        foreach ($dates as $name => $date) {
            $fullDate = \DateTime::createFromFormat('Y-m-d', self::YEAR . '-' . $date);
            $holiday = new Holiday($fullDate, $name);
            $this->entityManager->persist($holiday);
        }

        $this->entityManager->flush();

        $this->release();

        return true;
    }
}