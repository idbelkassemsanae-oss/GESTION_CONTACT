<?php
// src/Service/PaginatorService.php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\ORM\Query;

class PaginatorService
{
    private int $itemsPerPage;

    public function __construct(int $itemsPerPage = 10)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    public function paginate(QueryBuilder $queryBuilder, int $page = 1): DoctrinePaginator
    {
        $currentPage = $page < 1 ? 1 : $page;
        $firstResult = ($currentPage - 1) * $this->itemsPerPage;

        $query = $queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($this->itemsPerPage)
            ->getQuery();

        return new DoctrinePaginator($query);
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function calculateTotalPages(int $totalItems): int
    {
        return (int) ceil($totalItems / $this->itemsPerPage);
    }
}