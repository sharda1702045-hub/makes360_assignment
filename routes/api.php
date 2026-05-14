<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Webhooks - Public endpoint for receiving Amazon SES / SendGrid events
Route::post('/webhooks/ses', [\App\Http\Controllers\Api\WebhookController::class, 'handleSes']);
