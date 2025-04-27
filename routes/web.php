<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::prefix('dashboard')
    ->name('dashboard')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        // Dashboard Home
        Route::get('', [ReportController::class, 'dashboard']);

        // Clients
        Route::prefix('clients')
            ->name('-clients')
            ->controller(ClientController::class)
            ->group(function () {
                Route::get('', 'index');
                Route::get('create', 'create')->name('-create');
                Route::post('', 'store')->name('-store');
                Route::get('{client}', 'show')->name('-show');
                Route::get('{client}/edit', 'edit')->name('-edit');
                Route::put('{client}', 'update')->name('-update');
                Route::delete('{client}', 'destroy')->name('-destroy');
            });

        // Invoices
        Route::prefix('invoices')
            ->name('-invoices')
            ->controller(InvoiceController::class)
            ->group(function () {
                Route::get('', 'index');
                Route::get('create', 'create')->name('-create');
                Route::post('', 'store')->name('-store');
                Route::get('{invoice}', 'show')->name('-show');
                Route::get('{invoice}/edit', 'edit')->name('-edit');
                Route::put('{invoice}', 'update')->name('-update');
                Route::delete('{invoice}', 'destroy')->name('-destroy');
                Route::post('{invoice}/mark-as-sent', 'markAsSent')->name('-mark-as-sent');
                Route::post('{invoice}/mark-as-paid', 'markAsPaid')->name('-mark-as-paid');
            });

        // Transactions
        Route::prefix('transactions')
            ->name('-transactions')
            ->controller(TransactionController::class)
            ->group(function () {
                Route::get('', 'index');
                Route::get('{transaction}', 'show')->name('-show');
                Route::post('record-payment', 'recordPayment')->name('-record-payment');
            });

        // Reports
        Route::prefix('reports')
            ->name('-reports')
            ->group(function () {
                Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('-profit-loss');
                Route::get('client-statement/{client}', [ReportController::class, 'clientStatement'])->name('-client-statement');
            });
    });

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
