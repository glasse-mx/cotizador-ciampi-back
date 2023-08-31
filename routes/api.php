<?php

use App\Http\Controllers\Api\Clients\ClientController;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Rutas para las operaciones de "Clientes"
Route::post('clients', [ClientController::class, 'createClient']);
Route::get('clients', [ClientController::class, 'getClients']);
Route::get('client/{telefono}', [ClientController::class, 'getClient']);
Route::put('client/{telefono}', [ClientController::class, 'editClient']);
Route::delete('client/{telefono}', [ClientController::class, 'deleteClient']);
