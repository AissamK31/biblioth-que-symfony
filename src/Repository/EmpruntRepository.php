<?php

namespace App\Repository;

use App\Entity\Emprunt;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

/**
 * @extends ServiceEntityRepository<Emprunt>
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    /**
     * Récupère tous les emprunts actifs d'un utilisateur
     */
    public function findEmpruntsActifs(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.utilisateur = :user')
            ->andWhere('e.dateRetourEffective IS NULL')
            ->setParameter('user', $user)
            ->orderBy('e.dateRetourPrevue', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Récupère tous les emprunts en retard d'un utilisateur
     */
    public function findEmpruntsEnRetard(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.utilisateur = :user')
            ->andWhere('e.dateRetourEffective IS NULL')
            ->andWhere('e.dateRetourPrevue < :now')
            ->setParameter('user', $user)
            ->setParameter('now', new DateTime())
            ->orderBy('e.dateRetourPrevue', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Récupère l'historique des emprunts d'un utilisateur (déjà retournés)
     */
    public function findHistoriqueEmprunts(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.utilisateur = :user')
            ->andWhere('e.dateRetourEffective IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('e.dateRetourEffective', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Vérifie si un livre est déjà emprunté
     */
    public function isLivreEmprunte(int $livreId): bool
    {
        $count = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.livre = :livreId')
            ->andWhere('e.dateRetourEffective IS NULL')
            ->setParameter('livreId', $livreId)
            ->getQuery()
            ->getSingleScalarResult();
            
        return $count > 0;
    }
} 