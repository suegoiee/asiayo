<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|string',
            'name' => [
                'required',
                'string',
            ],
            'price' => [
                'required',
                'numeric',
                'max:2000'
            ],
            'currency' => [
                'required',
                Rule::in(['TWD', 'USD']),
            ],
            'address.city' => 'sometimes|string',
            'address.district' => 'sometimes|string',
            'address.street' => 'sometimes|string',
        ];
    }

    public function messages()
    {
        return [
            'price.max' => 'price is over 2000',
            'currency.in' => 'currency format is wrong',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $name = $this->input('name');

            if (!preg_match('/^[A-Za-z\s]+$/', $name)){
                $validator->errors()->add('name', 'Name contains non-English characters');
            }

            $capitalizedName = ucwords($name);
            if ($name !== $capitalizedName) {
                $validator->errors()->add('name', 'Name is not capitalized');
            }
        });
    }

    protected function prepareForValidation()
    {
        if ($this->input('currency') === 'USD') {
            $convertedPrice = $this->input('price') * 31;

            $this->merge([
                'price' => $convertedPrice,
                'currency' => 'TWD',
            ]);
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 400,
            'errors' => $validator->errors(),
        ], 400));
    }
}
