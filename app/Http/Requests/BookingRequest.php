<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
            'guest_name'      => ['required', 'string', 'max:255'],
            'hotel_name'      => ['required', 'string', 'max:255'],
            'check_in_date'   => ['required', 'date'],
            'check_out_date'  => ['required', 'date', 'after:check_in_date'],
            'guests_count'    => ['required', 'integer', 'min:1', 'max:255'],
            'total_price'     => ['required', 'numeric', 'min:0'],
            'currency'        => ['required', 'string', 'size:3'],
            'contact_email'   => ['nullable', 'email', 'max:255'],
            'contact_phone'   => ['nullable', 'string', 'max:50'],
            'promo_code'      => ['nullable', 'integer'],
        ];
    }
}
