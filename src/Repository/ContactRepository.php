<?php
// src/Repository/ContactRepository.php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ContactRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function save(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search(string $query, int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.nom LIKE :query')
            ->orWhere('c.prenom LIKE :query')
            ->orWhere('c.email LIKE :query')
            ->orWhere('c.telephone LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC');

        return $this->createPaginator($qb, $page);
    }

    public function findAllPaginated(int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC');

        return $this->createPaginator($qb, $page);
    }

    private function createPaginator($queryBuilder, int $page): Paginator
    {
        $currentPage = $page < 1 ? 1 : $page;
        $firstResult = ($currentPage - 1) * self::PAGINATOR_PER_PAGE;

        $query = $queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->getQuery();

        return new Paginator($query);
    }

    public function getStats(): array
    {
        $total = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $recent = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.dateCreation >= :date')
            ->setParameter('date', new \DateTime('-7 days'))
            ->getQuery()
            ->getSingleScalarResult();

        $withImage = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.image IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => (int)$total,
            'recent' => (int)$recent,
            'with_image' => (int)$withImage,
            'without_image' => (int)$total - (int)$withImage,
        ];
    }

    public function getRecentContacts(int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.dateCreation', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}