<?php

namespace App\Repository;

use App\Entity\SharedFile;
use App\Utilities\Constants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @method SharedFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method SharedFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method SharedFile[]    findAll()
 * @method SharedFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SharedFileRepository extends ServiceEntityRepository
{
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct($registry, SharedFile::class);
    }

    public function getDownloadsOutOrOlderThanLimit()
    {
        $time_now = new \DateTime();
        $time_ago = $time_now->modify(-(Constants::FILE_LIFETIME_SECONDS)." seconds");

        $qb = $this->createQueryBuilder("f");
        $query = $qb->
            where("f.allowed_downloads >= f.number_of_downloads")->
            orWhere("f.time_created <= :time_ago")->
            setParameter("time_ago", $time_ago)->
            getQuery();
        return $query->getResult();
    }
}
