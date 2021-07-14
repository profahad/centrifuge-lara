<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('jitsi/generate', [ApiController::class, 'generateToken']);

Route::get('pusher/broadcast', [ApiController::class, 'broadcastPusher']);

Route::get('centrifuge/token', [ApiController::class, 'genCentToken']);
Route::get('centrifuge/broadcast', [ApiController::class, 'broadcastCentrifuge']);
Route::get('centrifuge/presence', [ApiController::class, 'centrifugalPresence']);
