<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Запрос 5: самая дорогая книга (если таких несколько с одинаковой ценой — вернёт все).
     *
     * @return Book[]
     */
    public function findMostExpensive(): array
    {
        $max = $this->createQueryBuilder('b')
            ->select('MAX(b.price)')
            ->getQuery()
            ->getSingleScalarResult();

        if ($max === null) {
            return [];
        }

        return $this->findBy(['price' => $max]);
    }
}
