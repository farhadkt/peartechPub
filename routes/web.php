<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'DashboardController@index')->name('home');

/*
 * Authentication routes
 */
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
Route::get ('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

/*
 * All routes need authenticated user
 */
Route::middleware('auth')->group(function () {

    Route::get('/', 'DashboardController@index')->name('home');

    /*
     * Dashboard
     */
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', 'DashboardController@index')->name('index');
        Route::post('/', 'DashboardController@indexAjax');
        Route::post('/watchlist', 'DashboardController@watchlist');
        Route::post('/positions', 'DashboardController@positions');
        Route::post('/unmatch', 'DashboardController@unmatch');
        Route::post('/history', 'DashboardController@history');
    });

    /*
     * Orders
     */
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::post('create', 'OrderController@store')->name('create');
        Route::patch('{order}/edit', 'OrderController@update')->name('update');
        Route::get('destroy-unmatch/{id}', 'OrderController@destroyUnmatch')->name('destroyUnmatch');
    });

    /*
    * Admin.users routes
    */
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', 'UserController@index')->name('index');
        Route::get('{user}/edit', 'UserController@edit')->name('edit');
        Route::patch('{user}/edit', 'UserController@update')->name('update');
        Route::get('create', 'UserController@create')->name('create');
        Route::post('create', 'UserController@store')->name('store');
        /* Maybe Later
        Route::delete('{user}/edit', 'UserController@destroy')->name('destroy');
        Route::get('{user}', 'UserController@show')->name('show');
        */
    });

    /*
    * Product routes
    */
    Route::prefix('products')->name('products.')->group(function () {
        Route::post('chart/details', 'ProductController@chartDetails');
        Route::get('group-{group}', 'ProductController@findByGroup');

        /* Imports */
        Route::get('import', 'ProductController@importForm')->name('import.form');
        Route::post('import/parse', 'ProductController@parseImport')->name('import.parse');
        Route::post('import', 'ProductController@import')->name('import');
    });

    /*
    * Transactions routes
    */
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('index', 'TransactionController@index')->name('index');
        Route::post('index', 'TransactionController@indexPost');
        Route::post('detail', 'TransactionController@detail');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('edit', 'SettingController@edit')->name('edit');
        Route::patch('edit', 'SettingController@update')->name('update');
    });

    Route::get('/clear-cache', function() {
        Artisan::call('config:cache');
        Artisan::call('cache:clear');
    });

});
