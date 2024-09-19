<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\BookController;

Route::prefix('v1/book')->namespace('App\Http\Controllers\Api\v1')->group(function() {
    Route::get('/search', [BookController::class, 'searchBookOrGenre']);
    Route::post('/rent-book', [BookController::class, 'rentBook']);
    Route::post('/return-book', [BookController::class, 'returnBook']);
    Route::get('/view-rental-history', [BookController::class, 'viewRentalHistory']);
    Route::get('/book-stats', [BookController::class, 'getBookRentalStats']);
    //    Route::get('/mark-overdue-rentals', [BookController::class, 'markOverdueRentals']);
//    Route::get('/send-overdue-email', [BookController::class, 'sendEmailNotificationsToUsersForOverdue']);
});


