<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response('API OK', 200);
});
