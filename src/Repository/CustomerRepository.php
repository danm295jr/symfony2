<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * Запрос 1: все люди и сколько раз каждый покупал (0 — тоже показываем).
     *
     * @return array<int, array{id: int, name: string, email: string, ordersCount: int|string}>
     */
    public function findAllWithOrdersCount(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.name', 'c.email', 'COUNT(o.id) AS ordersCount')
            ->leftJoin('c.orders', 'o')
            ->groupBy('c.id')
            ->addGroupBy('c.name')
            ->addGroupBy('c.email')
            ->orderBy('ordersCount', 'DESC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Запрос 3: топ клиентов по сумме потраченных денег.
     *
     * @return array<int, array{id: int, name: string, email: string, totalSpent: string}>
     */
    public function findTopBySpending(int $limit = 3): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.name', 'c.email', 'SUM(i.quantity * i.unitPrice) AS totalSpent')
            ->join('c.orders', 'o')
            ->join('o.items', 'i')
            ->groupBy('c.id')
            ->addGroupBy('c.name')
            ->addGroupBy('c.email')
            ->orderBy('totalSpent', 'DESC')
            ->addOrderBy('c.name', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }
}
