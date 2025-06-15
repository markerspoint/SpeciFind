<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\SpeciFindController;

Route::post('/find-scientific-name', [SpeciFindController::class, 'findScientificName']);


// auto complete 
use App\Http\Controllers\AutocompleteController;

Route::get('/autocomplete-suggestions', [AutocompleteController::class, 'suggest']);
