<?php

namespace App\Http\Requests\Book;

use App\Http\Requests\BaseFormRequest;
use Carbon\Carbon;
class BookRentalRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function attributes()
    {
        return [
            'book_id'   => 'Book',
            'user_id'   => 'User',
            'from_date' => 'From Date',
            'till_date' => 'Till Date'
        ];
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
            'from_date' => 'required|date|date_format:Y-m-d H:i:s',
//            'till_date' => [
//                'required',
//                'date',
//                'date_format:Y-m-d H:i:s',
//                'after:from_date',
//                'after:now',
//                function ($attribute, $value, $fail) {
//                    $fromDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->input('from_date'));
//                    $tillDate = Carbon::createFromFormat('Y-m-d H:i:s', $value);
//                    $maxTillDate = $fromDate->copy()->addWeeks(2);
//                    if ($tillDate->greaterThan($maxTillDate)) {
//                        $fail('The till date must not be more than two weeks.');
//                    }
//                },
//            ],
        ];
    }

    public function messages()
    {
        return [
            'book_id.required'  => 'Please mention book',
            'user_id.required'  => 'Please mention user',
//            'till_date.after' => 'The till date must be after the from date and a future date.',
//            'till_date.after:now' => 'The till date must be a future date.',
        ];
    }

}
