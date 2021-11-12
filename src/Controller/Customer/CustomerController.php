<?php

namespace App\Controller\Customer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use PDO;

class CustomerController extends AbstractController
{
    public function index(): Response
    {
        $pdo = new PDO('mysql:host=db.3wa.io;dbname=cedricleclinche_classicmodels;charset=UTF8', 'cedricleclinche', 'eb094434df8b9e10f67b5c650f7bed6c', [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        $query = $pdo->prepare('
            SELECT customerNumber, customerName, city, country, postalCode, phone, firstName, lastName
            FROM customers
            INNER JOIN employees ON employees.employeeNumber = customers.salesRepEmployeeNumber
            ORDER BY customerNumber
        ');
        
        $query->execute();
        
        $customers = $query->fetchAll();
        
        return $this->render('customers/index.html.twig', [
            'customers' => $customers    
        ]);
    }
    
    public function show(int $id): Response
    {
        $pdo = new PDO('mysql:host=db.3wa.io;dbname=cedricleclinche_classicmodels;charset=UTF8', 'cedricleclinche', 'eb094434df8b9e10f67b5c650f7bed6c', [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        $query = $pdo->prepare('
            SELECT customers.customerNumber, customerName, addressLine1, city, country, postalCode, contactFirstName, contactLastName, firstName, lastName, orders.orderNumber, orderDate, status, SUM(quantityOrdered * priceEach) AS totalPrice
            FROM customers
            LEFT JOIN employees ON employees.employeeNumber = customers.salesRepEmployeeNumber
            LEFT JOIN orders ON orders.customerNumber = customers.customerNumber
            LEFT JOIN orderdetails ON orderdetails.orderNumber = orders.orderNumber
            WHERE customers.customerNumber = ?
            GROUP BY customerName, addressLine1, city, country, postalCode, contactFirstName, contactLastName, firstName, lastName, orders.orderNumber, orderDate
            ORDER BY orderDate DESC
        ');
        
        $query->execute([
            $id    
        ]);
        
        $customerDetails = $query->fetchAll();
        
        if (empty($customerDetails)) {
            throw $this->createNotFoundException('Le client n\'existe pas');
        }
        
        $customer = $customerDetails[0];
        
        return $this->render('customers/show.html.twig', [
            'customer' => $customer,
            'orders' => $customerDetails    
        ]);
    }
}