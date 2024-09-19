<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRental extends Model
{
    use HasFactory;

    protected $casts = [
        'from_date' => 'datetime'
    ];

    protected $fillable = [
        'book_id',
        'user_id',
        'from_date',
        'till_date',
        'return_date',
        'status',
        'is_overdue',
    ];

    public function book() {
        return $this->belongsTo(Book::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
