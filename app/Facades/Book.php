<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
class Book extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'book';
    }

}
?>
