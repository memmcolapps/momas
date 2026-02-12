<?php

namespace App\Http\Requests\Meter;

use Illuminate\Foundation\Http\FormRequest;

class ValidateMeterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'meterNo' => 'required|string|max:255',
            'estateId' => 'required|integer|exists:estates,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'meterNo.required' => 'Meter number is required',
            'meterNo.string' => 'Meter number must be a string',
            'meterNo.max' => 'Meter number cannot exceed 255 characters',
            'estateId.required' => 'Estate ID is required',
            'estateId.integer' => 'Estate ID must be an integer',
            'estateId.exists' => 'The selected estate does not exist',
        ];
    }
}
