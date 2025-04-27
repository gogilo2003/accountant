<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::prefix('dashboard')
    ->name('dashboard')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        Route::get('', function () {
            return Inertia::render('Dashboard');
        });

        Route::get('clients', function () {
            return Inertia::render('clients/Index');
        })->name('-clients');

        Route::get('invoices', function () {
            return Inertia::render('invoices/Index');
        })->name('-invoices');

        Route::get('transactions', function () {
            return Inertia::render('transactions/Index');
        })->name('-transactions');

        Route::get('reports', function () {
            return Inertia::render('reports/Index');
        })->name('-reports');
    });

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
