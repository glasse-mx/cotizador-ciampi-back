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
     * Dashboard
     */
    Route::get('sumary', [OrderController::class, 'getSalesSummary']);

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
    Route::get('client/{telefono}/orders', [OrderController::class, 'getOrdersByClient']);
    Route::get('clients/search={search}', [ClientController::class, 'searchClient']);

    //Rutas para las operaciones sobre las ordenes
    Route::post('orders', [OrderController::class, 'createOrder']); // Crea un Folio como cotizacion
    Route::get('orders/quotes', [orderController::class, 'getOrders']); // Obtiene todas las cotizaciones
    Route::get('orders/sales', [orderController::class, 'getSales']); // Obtiene todas las ventas
    Route::get('orders/cancelations', [orderController::class, 'getCancellations']); // Obtiene todas las Notas canceladas
    Route::get('orders/quotes/{id}', [orderController::class, 'getOrder']); // Obtiene un folio segun su id
    Route::put('orders/edit/{id}', [OrderController::class, 'editOrder']); // Modifica un Folio
    Route::put('orders/{id}', [OrderController::class, 'convertToNotaVenta']); // Convierte una cotizacion en una nota de venta
    Route::put('orders/{id}/cancel', [OrderController::class, 'convertToNotaCancelada']); // Cancela un folio
    Route::put('orders/{id}/approval', [OrderController::class, 'setOrderApproval']); // Aprueba un folio
});

// Ruta Para obtener las opciones de pago y bancos
Route::get('payment/options', [PaymentController::class, 'getPaymentOptions']);

// Ruta para los avatar de los usuarios
Route::get('avatar/{filename}', [AuthController::class, 'getAvatar']);

// Route::get('client/{telefono}', [ClientController::class, 'getClient']);
Route::get('usertypes', [AuthController::class, 'getUserTypes']);
