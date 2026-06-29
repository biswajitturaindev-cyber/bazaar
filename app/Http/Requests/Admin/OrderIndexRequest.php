<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderIndexRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'      => ['nullable', 'string', 'max:100'],
            'business_id' => ['nullable', 'string'],
            'month'       => ['nullable', 'integer', 'between:1,12'],
            'year'        => ['nullable', 'integer', 'min:2023', 'max:' . date('Y')],
            'from_date'   => ['nullable', 'date'],
            'to_date'     => ['nullable', 'date', 'after_or_equal:from_date'],
            'per_page'    => ['nullable', 'integer', 'in:10,25,50,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_date.after_or_equal' => 'The "to" date must be on or after the "from" date.',
            'month.between'          => 'Month must be between 1 and 12.',
            'year.min'               => 'Year must be 2023 or later.',
        ];
    }
}
