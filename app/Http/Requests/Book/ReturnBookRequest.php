<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class ReturnBookRequest extends FormRequest
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
            'book_id'   => 'required|exists:books,id',
            'user_id'   => 'required|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'book_id.required'  => 'Please mention book',
            'user_id.required'  => 'Please mention user',
        ];
    }

}
