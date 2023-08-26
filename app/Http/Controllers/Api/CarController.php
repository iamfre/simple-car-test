<?php

namespace App\Http\Controllers\Api;

use App\Enum\CarAvailableSortingColums;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\PropertyContainer\Transformers\CarCollection;
use App\PropertyContainer\Transformers\CarResource;
use App\Services\CarService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CarController extends Controller
{
    private const LIMIT = 100;

    public function getList(?Request $request): JsonResponse
    {
        $perPage = $request->has('per_page') && $request->per_page < self::LIMIT ?
            (int) $request->per_page : self::LIMIT;

        $orderColumn = CarAvailableSortingColums::fromRequest($request->get('sort')) ?? 'brand_id';

        $orderDirection = mb_strtolower($request->has('sort_by') ? $request->sort_by : 'asc');

        try {
            $query = Car::query()->with(['brand']);

            if ($request->has('brand')) {
                $filterBrands = explode('-', $request->get('brand'));

                $query->whereHas('brand', function ($q) use ($filterBrands) {
                    $q->whereIn('name', $filterBrands);
                });
            }

            if ($request->has('price')) {
                $filterPrice = explode('-', $request->get('price'));

                if (count($filterPrice) == 1) {
                    $query->where('price', '<=', $filterPrice);
                } else {
                    $query->whereBetween('price', $filterPrice);
                }
            }

            if ($request->has('year')) {
                $filterYear = explode('-', $request->get('year'));

                if (count($filterYear) == 1) {
                    $query->where('year', '<=', $filterYear);
                } else {
                    $query->whereBetween('year', $filterYear);
                }
            }

            $cars = $query->orderBy($orderColumn, $orderDirection)->paginate($perPage);
        } catch (Exception $exception) {
            Log::channel('api')->error(
                sprintf(
                    'An error occurred while getting the list of cars, error code: %s',
                    $exception->getCode()
                ),
                [
                    'Exception class' => get_class($exception),
                    'Message' => $exception->getMessage(),
                    'File' => $exception->getFile(),
                    'Line' => $exception->getLine(),
                ]
            );
            return response()->json("Oops! Something went wrong", 400);
        }

        $responseData = [
            'total' => $cars->total(),
            'perPage' => $cars->perPage(),
            'page' => $cars->currentPage(),
            'items' => new CarCollection($cars->items()),
        ];

        return response()->json($responseData);
    }

    public function show($carId): JsonResponse
    {
        try {
            $errors = [];

            $carService = new CarService();
            $car = $carService->getCarById($carId);

            if (empty($car)) {
                $errors[] = __('errors.not_found', ['attribute' => $carId]);
                $responseStatus = 404;
            }

            $responseData = [
                'success' => empty($errors),
                'errors' => $errors,
                'car' => !empty($car) ? new CarResource($car) : null,
            ];
        } catch (Exception $exception) {
            Log::channel('api')->error(
                sprintf(
                    'An error occurred while obtaining a vehicle by ID:%s, error code: %s',
                    $carId,
                    $exception->getCode()
                ),
                [
                    'Exception class' => get_class($exception),
                    'Message' => $exception->getMessage(),
                    'File' => $exception->getFile(),
                    'Line' => $exception->getLine(),
                ]
            );

            return response()->json("Oops! Something went wrong", 400);
        }

        return response()->json($responseData, $responseStatus ?? 200);
    }

    /**
     * Создание или обновление автомобиля
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $errors = [];
        $data = $request->all();

        $action = Arr::get($data, 'action');
        $carId = Arr::get($data, 'id');

        $carService = new CarService();
        $currentCar = $carService->getCarById($carId, ['brand']);

        if (!empty($data['price']) && !is_numeric($data['price'])) {
            $errors[] = __('validation.numeric', ['attribute' => 'Price']);
        }

        if (!empty($data['brand'])) {
            $brand = Brand::query()
                ->where('name', $data['brand'])
                ->orWhere('id', $data['brand'])
                ->first();

            if (empty($brand)) {
                $errors[] = __('errors.not_found', ['attribute' => $data['brand']]);
            } else {
                $data['brand_id'] = $brand->id;
            }
        }

        if (!empty($data['brand_id']) && !empty($data['model'])) {
            $carIsExist = Car::query()
                ->where('brand_id', $data['brand_id'])
                ->where('model', $data['model'])
                ->exists();

            if ($carIsExist) {
                $errors[] = __('validation.exist', ['attribute' => "$brand->name"."-"."{$data['model']}"]);
            }
        }

        if (empty($errors)) {
            switch ($action) {
                case 'update':
                    if (!empty($currentCar)) {
                        unset($data['external_id']);
                        $car = $currentCar->update($data);
                    } else {
                        if (empty($data['brand'])) {
                            $errors[] = __('validation.required', ['attribute' => 'Brand']);
                        }

                        if (empty($data['model'])) {
                            $errors[] = __('validation.required', ['attribute' => 'Model']);
                        }

                        if (empty($errors)) {
                            $data['external_id'] = Str::uuid()->toString();

                            $car = Car::query()->create($data);

                            Log::channel('api')->info(
                                sprintf(
                                    'Creating car, id:%s',
                                    $car->id,
                                ),
                                [
                                    'Car' => $car->toArray(),
                                ]
                            );
                        }
                    }

                    break;
                case 'delete':
                    if (empty($currentCar)) {
                        $errors[] = __('errors.not_found', ['attribute' => $carId]);
                    }

                    if (empty($errors)) {
                        $car = $currentCar;

                        $currentCar->delete();

                        Log::channel('api')->info(
                            sprintf(
                                'Deleting car, id:%s',
                                $car->id,
                            ),
                            [
                                'Car' => $car->toArray(),
                            ]
                        );
                    }

                    break;
                default:
                    $errors[] = "Не верно задано действие";
            }
        }

        $responseData = [
            'success' => empty($errors),
            'errors' => $errors,
            'result' => !empty($car) ? new CarResource($car) : null,
        ];

        return response()->json($responseData, (empty($errors) ? 200 : 400));
    }
}
