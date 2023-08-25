<?php

namespace App\Http\Controllers\Api;

use App\Enum\CarAvailableSortingColums;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\PropertyContainer\Transformers\CarCollection;
use App\PropertyContainer\Transformers\CarResource;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CarController extends Controller
{
    private const LIMIT = 100;

    public function getList(?Request $request): JsonResponse
    {
        $data = $request->all();

        // TODO: фильтр по году, бренду
        $perPage = $request->has('per_page') && $request->per_page < self::LIMIT ?
            (int) $request->per_page : self::LIMIT;

        $orderColumn = CarAvailableSortingColums::fromRequest($request->get('sort', 'brand_id'));

        $orderDirection = mb_strtolower($request->has('sort_by') ? $request->sort_by : 'asc');

        try {
            $cars = Car::query()->with(['brand'])->orderBy($orderColumn, $orderDirection)->paginate($perPage);
        } catch (\Exception $exception) {
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
        $responseStatus = 200;
        $errors = [];

        $car = $this->getCarById($carId);

        if (empty($car)) {
            $errors[] = __('errors.not_found', ['attribute' => $carId]);
        }

        if (!empty($errors)) {
            $responseStatus = 400;
        }

        $responseData = [
            'success' => empty($errors),
            'errors' => $errors,
            'car' => !empty($car) ? new CarResource($car) : null,
        ];

        return response()->json($responseData, $responseStatus);
    }

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
                $errors[] = __('validation.exist', ['attribute' => "{$brand->name}"."-"."{$data['model']}"]);
            }
        }

        if (empty($errors)) {
            switch ($action) {
                case 'update':
                    if (!empty($request->get('year'))) {
                        $data['year'] = Carbon::create($request->get('year'));
                    }

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
