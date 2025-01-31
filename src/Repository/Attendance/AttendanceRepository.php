<?php

declare(strict_types=1);

namespace App\Repository\Attendance;

use App\Entity\Attendance\Attendance;
use App\Entity\Attendance\AttendanceInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|AttendanceInterface find($id, $lockMode = null, $lockVersion = null)
 * @method null|AttendanceInterface findOneBy(array $criteria, array $orderBy = null)
 * @method AttendanceInterface[]    findAll()
 * @method AttendanceInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttendanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attendance::class);
    }
}
