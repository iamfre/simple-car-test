<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'brand_id' => 'required',
            'model' => 'required',
            'price' => 'numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'brand_id.required' => __('validation.required', ['attribute' => 'Brand']),
            'model.required' =>  __('validation.required', ['attribute' => 'Model']),
            'price.numeric' =>  __('validation.numeric', ['attribute' => 'Model']),
        ];
    }
}
