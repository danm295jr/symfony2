<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookstoreController extends AbstractController
{
    #[Route('/bookstore', name: 'app_bookstore')]
    public function index(
        CustomerRepository $customers,
        OrderRepository $orders,
        BookRepository $books,
    ): Response {
        return $this->render('bookstore/index.html.twig', [
            'customerStats'  => $customers->findAllWithOrdersCount(),  // 1
            'orders'         => $orders->findAllWithTotals(),          // 2
            'topCustomers'   => $customers->findTopBySpending(3),      // 3
            'averageOrder'   => $orders->averageOrderTotal(),          // 4
            'expensiveBooks' => $books->findMostExpensive(),           // 5
        ]);
    }
}
