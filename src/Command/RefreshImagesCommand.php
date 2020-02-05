<?php

namespace App\Command;

use App\Entity\Holiday;
use App\Entity\NasaImage;
use App\Service\NasaApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RefreshImagesCommand extends Command
{
    use LockableTrait;

    /**
     * @var NasaApiService
     */
    private $nasaApiService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(NasaApiService $nasaApiService, EntityManagerInterface $entityManager)
    {
        $this->nasaApiService = $nasaApiService;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Refreshes dates of holidays in db for 2019')
            ->setName('app:refresh:images')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $holidayRepository = $this->entityManager->getRepository(Holiday::class);
        $imageRepository = $this->entityManager->getRepository(NasaImage::class);

        $holidays = $holidayRepository->findAll();

        if (count($holidays) === 0) {
            throw new NotFoundHttpException('No holidays in database, try reimport');
        }

        $images = $imageRepository->findAll();

        if (count($images) > 0) {
            foreach ($images as $image) {
                $this->entityManager->remove($image);
            }
            $this->entityManager->flush();
        }

        /** @var Holiday $holiday */
        foreach ($holidays as $holiday) {
            foreach (NasaApiService::ROVERS as $rover) {
                foreach (NasaApiService::CAMERAS_ABBREVIATION as $camera) {
                    $response = $this->nasaApiService->getDataFromApi($rover, $camera, $holiday->getDate());
                    $response = json_decode($response->getContent());

                    foreach ($response->photos as $photo) {
                        $image = new NasaImage(
                            $photo->id,
                            new \DateTime($photo->earth_date),
                            $photo->rover->name,
                            $photo->camera->name,
                            $photo->img_src
                        );
                        $this->entityManager->persist($image);
                    }
                }
            }

        }

        $this->entityManager->flush();

        return true;
    }
}