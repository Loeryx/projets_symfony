<?php

namespace App\Repository;

use App\Entity\Media;
use Cassandra\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Media>
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function findPopularMedia(int $maxResult) : array
    {
        // Media OneToMany WatchHistory
        return $this->createQueryBuilder('m')
            -> leftJoin('m.watchHistories', 'wh')
            -> groupBy('m.id')
            -> orderBy('COUNT(wh)', 'DESC')
            -> setMaxResults($maxResult)
            -> getQuery()
            -> getResult();
    }
}
