<?php

namespace App\Services;

use App\Models\Car;

class CarService
{
    /** Получить автомобиль по id или external_id */
    public function getCarById($carId, array $with = [])
    {
        $car = null;

        if (!empty($carId)) {
            $query = Car::query()
                ->with($with)
                ->where('external_id', $carId);

            $query->when(is_numeric($carId), function ($q) use ($carId) {
                return $q->OrWhere('id', $carId);
            });

            $car = $query->first();
        }

        return $car;
    }
}
