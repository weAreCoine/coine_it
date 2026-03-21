<?php

use App\Http\Controllers\CalendlyWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('webhooks/calendly', CalendlyWebhookController::class);
