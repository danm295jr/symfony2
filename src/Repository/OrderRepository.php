<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Запрос 2: каждый заказ с суммой (сколько человек потратил в конкретный раз).
     *
     * @return array<int, array{id: int, createdAt: \DateTimeImmutable, customerName: string, orderTotal: string}>
     */
    public function findAllWithTotals(): array
    {
        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.createdAt', 'c.name AS customerName', 'SUM(i.quantity * i.unitPrice) AS orderTotal')
            ->join('o.customer', 'c')
            ->join('o.items', 'i')
            ->groupBy('o.id')
            ->addGroupBy('o.createdAt')
            ->addGroupBy('c.name')
            ->orderBy('o.createdAt', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Запрос 4: средняя сумма одного заказа.
     * Считается как «сумма всех позиций / количество заказов».
     */
    public function averageOrderTotal(): float
    {
        $row = $this->createQueryBuilder('o')
            ->select('SUM(i.quantity * i.unitPrice) AS grandTotal', 'COUNT(DISTINCT o.id) AS ordersCount')
            ->join('o.items', 'i')
            ->getQuery()
            ->getSingleResult();

        $count = (int) $row['ordersCount'];

        return $count > 0 ? round((float) $row['grandTotal'] / $count, 2) : 0.0;
    }
}
