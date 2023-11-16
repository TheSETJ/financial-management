<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListTransactionsRequest extends FormRequest
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
            'page' => ['integer'],
            'per_page' => ['integer'],
            'search' => ['string'],
            'min_amount' => ['integer'],
            'max_amount' => ['integer'],
            'start_of_performed_at' => ['date_format:Y-m-d H:i:s'],
            'end_of_performed_at' => ['date_format:Y-m-d H:i:s'],
        ];
    }
}
