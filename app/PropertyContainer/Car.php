<?php

declare(strict_types=1);

namespace App\PropertyContainer;

class Car extends PropertyContainer
{
    private $model;
    private $brand;
    private $price;

    public function __construct(string $model, $brand, $price)
    {
        $this->brand = $brand;
        $this->model = $model;
        $this->price = $price;

    }

    public function getModel($carId)
    {

    }
}
