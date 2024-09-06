<?php

use App\Models\Collaborator;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});