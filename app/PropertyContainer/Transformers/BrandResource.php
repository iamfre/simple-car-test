<?php

namespace App\PropertyContainer\Transformers;

use App\Models\Brand;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return Arr::except(parent::toArray($request), [
            'created_at',
            'updated_at',
        ]);
    }
}
