<?php

namespace App\Http\Requests;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStrainRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:strain,name',
            'link' => 'required|string|max:255',
            'place_of_allocation' => 'required|string|max:255',
            'year_of_allocation' => 'required|digits:4',
            'type_of_bacteria' => 'required|string|max:255',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::info('Validation failed: ', $validator->errors()->toArray());

        $response = response()->json([
            'message' => 'Validation Error',
            'errors' => $validator->errors(),
        ], 422);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }
}
