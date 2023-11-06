<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:paid,received,transferred'],
            'amount' => ['required', 'integer', 'gt:0'],
            'description' => ['string'],
            'performed_at' => ['date_format:Y-m-d H:i:s']
        ];
    }
}
