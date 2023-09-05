<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pdv' => 'string',
                // 'productos' => 'json',
                // 'descuentos' => 'json',
                // 'detalles_anticipo' => 'json',
                // 'detalles_pago' => 'json'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $order = new Order();
            // $order->id_user = auth()->user()->id;
            $order->id_user = $request->id_user;
            $order->pdv = $request->pdv;
            $order->id_client = $request->id_client;
            $order->productos = json_encode($request->productos);
            $order->descuentos = json_encode($request->descuentos);
            $order->folio_status_id = $request->folio_status_id;
            $order->folio_cotizacion_id = $request->folio_cotizacion_id;
            $order->subtotal_productos = json_encode($request->subtotal_productos);
            $order->subtotal_promos = $request->subtotal_promos;
            $order->detalle_anticipo = json_encode($request->detalle_anticipo);
            $order->detalles_pago = json_encode($request->detalles_pago);
            $order->total = $request->total;
            $order->save();

            return response()->json($order, 201);
        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
