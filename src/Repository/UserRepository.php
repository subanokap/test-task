<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    private const ALLOWED_SORT_FIELDS = [
        'id',
        'firstName',
        'lastName',
        'email',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllSorted($sortField): array
    {
        if (empty($sortField) || !in_array($sortField, self::ALLOWED_SORT_FIELDS, true)) {
            $sortField = 'id';
        }

        return $this->createQueryBuilder('user')
            ->orderBy('user.' . $sortField, 'ASC')
            ->getQuery()
            ->getResult();
    }
}