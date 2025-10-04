<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', [ContactController::class, 'showForm'])->name('contact.form');
Route::post('/', [ContactController::class, 'submitForm'])->name('contact.submit');
