<?php

namespace App\Repository;

use App\Entity\Commentary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 *
 * @extends ServiceEntityRepository<Commentary>
 */
class CommentaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentary::class);
    }

    /**
     * Récupère les commentaires avec les informations de l'utilisateur
     */
    public function findByThemeWithUser(int $themeId): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.user', 'u')
            ->addSelect('u')
            ->where('c.theme = :themeId')
            ->setParameter('themeId', $themeId)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
