<?php

namespace App\Controller\Customer;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/customer", name: "api_customer_")]
class CustomerController
{
    public function addUser(): void {}

    public function editUser(): void {}

    public function deleteUser(): void {}

    public function getUserDetail(): void {}

    public function getUserLists(): void {}
}