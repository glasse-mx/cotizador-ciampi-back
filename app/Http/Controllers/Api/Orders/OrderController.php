<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\FolioCotizaciones;
use App\Models\FolioNotaCancelada;
use App\Models\FolioNotaVenta;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    /**
     * Crea una Nueva cotizacion
     */
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

            $folio = new FolioCotizaciones();
            $folio->save();

            $order = new Order();
            // $order->id_user = auth()->user()->id;
            $order->id_user = $request->id_user;
            $order->pdv = $request->pdv;
            $order->id_client = $request->id_client;
            $order->productos = json_encode($request->productos);
            $order->descuentos = json_encode($request->descuentos);
            $order->folio_status_id = 1;
            $order->folio_cotizacion_id = $folio->id;
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

    /**
     * Crea una Nueva Nota de Venta
     */
    public function createNotaVenta(Request $request)
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

            $folio = new FolioNotaVenta();
            $folio->save();

            $order = new Order();
            // $order->id_user = auth()->user()->id;
            $order->id_user = $request->id_user;
            $order->pdv = $request->pdv;
            $order->id_client = $request->id_client;
            $order->productos = json_encode($request->productos);
            $order->descuentos = json_encode($request->descuentos);
            $order->folio_status_id = 2;
            $order->folio_nota_venta_id = $folio->id;
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

    /**
     * Convierte una Cotizacion en una Nota Venta
     */
    public function convertToNotaVenta($id)
    {
        $order = Order::find($id);

        if ($order->folio_nota_venta_id != null) {
            $nVenta = new FolioNotaVenta();
            $nVenta->save();
        } else {
            $nVenta = FolioNotaVenta::find($order->folio_nota_venta_id);
        }

        $order->folio_status_id = 2;
        $order->folio_nota_venta_id = $nVenta->id;
        $order->save();
        return response()->json($order, 200);
    }

    /**
     * Convierte una Cotizacion en una Nota Cancelada
     */
    public function convertToNotaCancelada($id)
    {
        $nCancelada = new FolioNotaCancelada();
        $nCancelada->save();

        $order = Order::find($id);
        $order->folio_status_id = 3;
        $order->folio_nota_cancelada_id = $nCancelada->id;
        $order->save();
        return response()->json($order, 200);
    }

    /**
     * Devuelve todas las cotizaciones
     */
    public function getOrders()
    {
        $orders = Order::all();
        return response()->json($orders, 200);
    }

    /*
    * Devuelve una cotizacion
    */
    public function getOrder($id)
    {
        $order = Order::find($id);
        return response()->json($order, 200);
    }

    /**
     * obtiene las cotizaciones de un cliente
     */
    public function getOrdersByClient($id)
    {
        $orders = Order::where('id_client', $id)->get();
        return response()->json($orders, 200);
    }

    /**
     * Obtiene las notas de venta de un cliente
     */
    public function getNotasVentaByClient($id)
    {
        $orders = Order::where('id_client', $id)->where('folio_status_id', 2)->get();
        return response()->json($orders, 200);
    }
}
