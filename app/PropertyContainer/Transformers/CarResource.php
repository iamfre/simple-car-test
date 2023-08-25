<?php

namespace App\PropertyContainer\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        $brand = $this->whenLoaded('brand', function () {
            return $this->brand;
        }, null);

        $array = Arr::except(parent::toArray($request), [
            'created_at',
            'updated_at',
        ]);

        if (isset($brand)) {
            $array['brand'] = new BrandResource($brand);
        }

        return $array;
    }
}
