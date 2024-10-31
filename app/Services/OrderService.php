<?php

namespace App\Services;

class OrderService
{
    public function processOrder(array $data): array
    {
        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'address' => $data['address'],
            'price' => $data['price'],
            'currency' => $data['currency'],
        ];
    }
}