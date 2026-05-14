<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ImportController;

Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

Route::prefix('campaigns')->name('campaigns.')->group(function () {
    Route::get('/', [CampaignController::class, 'index'])->name('index');
    Route::get('/create', [CampaignController::class, 'create'])->name('create');
    Route::post('/', [CampaignController::class, 'store'])->name('store');
    Route::get('/{id}', [CampaignController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [CampaignController::class, 'edit'])->name('edit');
});

Route::prefix('queue-monitor')->name('queue-monitor.')->group(function () {
    Route::get('/', [\App\Http\Controllers\QueueMonitorController::class, 'index'])->name('index');
    Route::get('/metrics', [\App\Http\Controllers\QueueMonitorController::class, 'metrics'])->name('metrics');
});

Route::prefix('audience')->name('audience.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AudienceController::class, 'index'])->name('index');
    Route::get('/{id}', [\App\Http\Controllers\AudienceController::class, 'show'])->name('show');
});

Route::prefix('imports')->name('imports.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ImportController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\ImportController::class, 'store'])->name('store');
    Route::get('/history', [\App\Http\Controllers\ImportController::class, 'history'])->name('history');
    Route::get('/export', [\App\Http\Controllers\ImportController::class, 'export'])->name('export');
    Route::get('/{id}/status', [\App\Http\Controllers\ImportController::class, 'status'])->name('status');
});

Route::prefix('system-logs')->name('system-logs.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SystemLogController::class, 'index'])->name('index');
    Route::get('/export', [\App\Http\Controllers\SystemLogController::class, 'export'])->name('export');
    Route::get('/{id}/download', [\App\Http\Controllers\SystemLogController::class, 'download'])->name('download');
    Route::post('/{id}/retry', [\App\Http\Controllers\SystemLogController::class, 'retry'])->name('retry');
    Route::get('/{id}', [\App\Http\Controllers\SystemLogController::class, 'show'])->name('show');
});

Route::prefix('templates')->name('templates.')->group(function () {
    Route::get('/', [\App\Http\Controllers\TemplateController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\TemplateController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\TemplateController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\TemplateController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\TemplateController::class, 'edit'])->name('edit');
    Route::delete('/{id}', [\App\Http\Controllers\TemplateController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/duplicate', [\App\Http\Controllers\TemplateController::class, 'duplicate'])->name('duplicate');
    Route::patch('/{id}', [\App\Http\Controllers\TemplateController::class, 'update'])->name('update');
});

Route::post('/contact-lists', [\App\Http\Controllers\ContactListController::class, 'store'])->name('contact-lists.store');
Route::get('/contact-lists/{id}', [\App\Http\Controllers\ContactListController::class, 'show'])->name('contact-lists.show');
Route::delete('/contact-lists/{id}', [\App\Http\Controllers\ContactListController::class, 'destroy'])->name('contact-lists.destroy');
Route::post('/contact-lists/{id}/attach', [\App\Http\Controllers\ContactListController::class, 'attachContact'])->name('contact-lists.attach');
