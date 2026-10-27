<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Состав заказа: связь «много книг в заказе» и «одна книга в разных заказах».
#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
#[ORM\UniqueConstraint(name: 'uq_order_book', columns: ['order_id', 'book_id'])]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Book::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Book $book;

    #[ORM\Column]
    private int $quantity;

    /** Цена одной книги на момент покупки. */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $unitPrice;

    public function __construct(Order $order, Book $book, int $quantity, string $unitPrice)
    {
        $this->order = $order;
        $this->book = $book;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getBook(): Book
    {
        return $this->book;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }
}
