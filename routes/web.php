<?php

use App\Mail\BookOverdueNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/preview-overdue-email', function(){
    //Route to test out the email
    $bookData = \App\Models\BookRental::where('id', 27)->with(['user','book'])->first();
    return new BookOverdueNotificationMail($bookData);
});
