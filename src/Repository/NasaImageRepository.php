<?php

namespace App\Repository;

use App\Entity\NasaImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method NasaImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method NasaImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method NasaImage[]    findAll()
 * @method NasaImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NasaImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NasaImage::class);
    }

    public function findByFields($rover = null, $camera = null, \DateTimeInterface $date = null)
    {
        $qb = $this->createQueryBuilder('i');
        if ($rover) {
            $qb->andWhere('i.rover = :rover')
                ->setParameter('rover', $rover);
        }

        if ($camera) {
            $qb->andWhere('i.cameraAbbreviation = :camera')
                ->setParameter('camera', $camera);
        }

        if ($date) {
            $qb->andWhere('i.earthDate = :date')
                ->setParameter('date', $date->format('Y-m-d'));
        }

            return $qb->getQuery()
            ->getResult()
        ;
    }
}
