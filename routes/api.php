<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Clients\ClientController;
use App\Http\Controllers\Api\Orders\FoliosController;
use App\Http\Controllers\Api\Orders\OrderController;
use App\Http\Controllers\Api\Payments\PaymentController;
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


// Rutas para la gestion de Usuarios de la app
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    /**
     * Rutas para la gestion de Usuarios de la app
     */
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('profile', [AuthController::class, 'me']);
    Route::get('allusers', [AuthController::class, 'getAllUsers']);
    Route::get('user/{id}', [AuthController::class, 'getUser']);
    Route::put('user/{id}', [AuthController::class, 'updateUser']);

    //Rutas para las operaciones de "Clientes"
    Route::post('clients', [ClientController::class, 'createClient']);
    Route::get('clients', [ClientController::class, 'getClients']);
    Route::get('client/{telefono}', [ClientController::class, 'getClient']);
    Route::put('client/{telefono}', [ClientController::class, 'editClient']);
    Route::delete('client/{telefono}', [ClientController::class, 'deleteClient']);

    //Rutas para las operaciones sobre las ordenes
    Route::post('orders', [OrderController::class, 'createOrder']);
    Route::put('orders/{id}', [OrderController::class, 'convertToNotaVenta']);
    Route::put('orders/{id}/cancel', [OrderController::class, 'convertToNotaCancelada']);
    Route::get('orders/all', [orderController::class, 'getOrders']);
    Route::get('orders/quotes', [orderController::class, 'getOrders']);
    Route::get('orders/sales', [orderController::class, 'getSales']);
});

// Ruta Para obtener las opciones de pago y bancos
Route::get('payment/options', [PaymentController::class, 'getPaymentOptions']);

// Ruta para los avatar de los usuarios
Route::get('avatar/{filename}', [AuthController::class, 'getAvatar']);

// Route::get('client/{telefono}', [ClientController::class, 'getClient']);
Route::get('usertypes', [AuthController::class, 'getUserTypes']);
