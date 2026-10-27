<?php

namespace App\Command;

use App\Entity\Book;
use App\Entity\Customer;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-bookstore',
    description: 'Заполняет базу тестовыми данными книжного магазина',
)]
class SeedBookstoreCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->em->getRepository(Customer::class)->count([]) > 0) {
            $io->warning('В базе уже есть покупатели, тестовые данные не добавлены.');

            return Command::SUCCESS;
        }

        $customers = [];
        foreach ([
            ['Анна Смирнова', 'anna@example.com'],
            ['Иван Петров',   'ivan@example.com'],
            ['Мария Козлова', 'maria@example.com'],
            ['Олег Новиков',  'oleg@example.com'],
            ['Елена Волкова', 'elena@example.com'], // ещё ничего не покупала
        ] as [$name, $email]) {
            $customer = new Customer($name, $email);
            $this->em->persist($customer);
            $customers[] = $customer;
        }

        $books = [];
        foreach ([
            ['Мастер и Маргарита',       '450.00'],
            ['Преступление и наказание', '380.00'],
            ['Война и мир',              '990.00'],
            ['1984',                     '320.00'],
            ['Тихий Дон',                '870.00'],
            ['Атлас мира (подарочный)', '4500.00'],
        ] as [$title, $price]) {
            $book = new Book($title, $price);
            $this->em->persist($book);
            $books[] = $book;
        }

        // [индекс покупателя, дата, [[индекс книги, количество], ...]]
        $orders = [
            [0, '2026-09-01 10:15:00', [[0, 1], [3, 2]]],
            [0, '2026-09-20 18:40:00', [[2, 1]]],
            [0, '2026-10-05 12:00:00', [[1, 1], [4, 1]]],
            [1, '2026-09-10 09:30:00', [[5, 1]]],
            [1, '2026-10-12 20:10:00', [[0, 1], [1, 1], [3, 1]]],
            [2, '2026-10-15 14:25:00', [[3, 1]]],
            [3, '2026-10-20 11:05:00', [[2, 2], [4, 1]]],
        ];

        foreach ($orders as [$customerIndex, $date, $lines]) {
            $order = new Order($customers[$customerIndex], new \DateTimeImmutable($date));
            foreach ($lines as [$bookIndex, $quantity]) {
                $order->addItem($books[$bookIndex], $quantity);
            }
            $this->em->persist($order); // позиции сохранятся каскадом
        }

        $this->em->flush();

        $io->success('Добавлено: 5 покупателей, 6 книг, 7 заказов.');

        return Command::SUCCESS;
    }
}
