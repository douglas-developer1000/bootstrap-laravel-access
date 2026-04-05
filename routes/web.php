<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/', 'pages.home')->name('home');
    Route::view('/signin', 'pages.signin')->name('login');
    Route::view('/forgot-password', 'pages.f-password')->name('password.request');
    
});
