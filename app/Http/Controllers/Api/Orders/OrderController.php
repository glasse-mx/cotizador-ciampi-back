<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\Banks;
use App\Models\FolioCotizaciones;
use App\Models\FolioNotaCancelada;
use App\Models\FolioNotaVenta;
use App\Models\Order;
use App\Models\Client;
use App\Models\FolioType;
use App\Models\PaymentType;
use App\Models\User;
use Carbon\Carbon;
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
            $order->created_by = $request->created_by;
            $order->pdv = $request->pdv;
            $order->id_cliente = $request->id_cliente;
            $order->productos = json_encode($request->productos);
            $order->descuentos = json_encode($request->descuentos);
            $order->folio_status_id = 1;
            $order->folio_cotizacion_id = $folio->id;
            $order->subtotal_productos = json_encode($request->subtotal_productos);
            $order->subtotal_promos = $request->subtotal_promos;
            $order->detalle_anticipo = json_encode($request->detalles_anticipo);
            $order->detalles_pago = json_encode($request->detalles_pago);
            $order->observaciones = $request->observaciones;
            $order->salida = $request->salida;
            $order->llegada = $request->llegada;
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
    public function convertToNotaVenta($id, Request $request)
    {
        // Verificamos que la cotizacion exista
        if (!$id) {
            return response()->json([
                'message' => 'No se encontro la cotizacion'
            ], 404);
        }

        // Encontramos la cotizacion
        $order = Order::find($id);

        // Verificamos que la orden sea cotizacion o nota de venta, de lo contrario
        // se devuelve un error
        if ($order->folio_status_id > 2 && $order->folio_status_id != 4) {
            return response()->json([
                'message' => 'La cotizacion ya fue convertida a nota de venta'
            ], 404);
        } elseif ($order->folio_status_id == 4) {
            return response()->json([
                'message' => 'La cotizacion ya fue cancelada'
            ], 404);
        }

        // Si la cotizacion no tiene un folio de nota de venta, se crea uno
        if ($order->folio_nota_venta_id === null) {
            $nVenta = new FolioNotaVenta();
            $nVenta->save();
            $order->folio_nota_venta_id = $nVenta->id;
        }

        // Identificamos al usuario que convirtio la cotizacion en nota de venta
        if ($request->edited_by != null) {
            $order->edited_by = $request->edited_by;
        }

        // Cambiamos el estado de la cotizacion a nota de venta
        // Ajustamos los datos con los del request
        $order->folio_status_id = 2;
        $order->detalles_pago = json_encode($request->detalles_pago);
        $order->observaciones = $request->observaciones;
        $order->save();

        // Adaptamos el JSON de salida con datos legibles para el Front End
        $output = $order;

        $user = User::find($output->created_by);
        $client = Client::where('id', $output->id_cliente)
            ->orWhere('phone', $output->id_cliente)
            ->first();
        $productos = $order->productos ? json_decode($order->productos) : null;
        $promos = $order->descuentos ? json_decode($order->descuentos) : null;
        $anticipos = $order->detalle_anticipo ? json_decode($order->detalle_anticipo) : null;
        $pagos = $order->detalles_pago ? json_decode($order->detalles_pago) : null;

        // Guardamos los datos a la salida
        $order->created_by = $user;
        $order->id_cliente = $client;
        $order->productos = $productos;
        $order->descuentos = $promos;
        $order->detalle_anticipo = $anticipos;
        $order->detalles_pago = $pagos;

        if ($output->edited_by != null) {
            $edited_by = User::find($output->edited_by);
            $output->edited_by = $edited_by;
        }

        return response()->json($output, 200);
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
        $orders = Order::where('folio_status_id', 1)->get();

        foreach ($orders as $order) {

            $user = User::find($order->created_by);
            $client = Client::where('id', $order->id_cliente)
                ->orWhere('phone', $order->id_cliente)
                ->first();


            if ($order->folio_status_id == 1) {
                $cotizacion = FolioCotizaciones::find($order->folio_cotizacion_id);
                $fecha = Carbon::parse($cotizacion->created_at)->format('d/m/Y - H:i');
            } else {
                $notaVenta = FolioNotaVenta::find($order->folio_nota_venta_id);
                $fecha = Carbon::parse($notaVenta->created_at)->format('d/m/Y - H:i');
            }

            $productos = $order->productos ? json_decode($order->productos) : null;
            $promos = $order->descuentos ? json_decode($order->descuentos) : null;
            $anticipos = $order->detalle_anticipo ? json_decode($order->detalle_anticipo) : null;
            $pagos = $order->detalles_pago ? json_decode($order->detalles_pago) : null;

            $order->fecha = $fecha;
            $order->created_by = $user;
            $order->id_cliente = $client;
            $order->productos = $productos;
            $order->descuentos = $promos;
            $order->detalle_anticipo = $anticipos;
            $order->detalles_pago = $pagos;
        }

        return response()->json($orders, 200);
    }

    /**
     * Devuelve todas las Notas de Venta
     */
    public function getSales()
    {
        $orders = Order::where('folio_status_id', 2)->get();

        foreach ($orders as $order) {

            $user = User::find($order->created_by);
            $client = Client::where('id', $order->id_cliente)
                ->orWhere('phone', $order->id_cliente)
                ->first();

            $fecha = Carbon::parse($order->created_at)->format('d/m/Y - H:i');
            $productos = $order->productos ? json_decode($order->productos) : null;
            $promos = $order->descuentos ? json_decode($order->descuentos) : null;
            $anticipos = $order->detalle_anticipo ? json_decode($order->detalle_anticipo) : null;
            $pagos = $order->detalles_pago ? json_decode($order->detalles_pago) : null;

            $order->fecha = $fecha;
            $order->created_by = $user;
            $order->id_cliente = $client;
            $order->productos = $productos;
            $order->descuentos = $promos;
            $order->detalle_anticipo = $anticipos;
            $order->detalles_pago = $pagos;
        }

        return response()->json($orders, 200);
    }

    /**
     * Devuelve todas las Cotizaciones
     */
    public function getQuotes()
    {
        $orders = Order::all();
        return response()->json($orders, 200);
    }

    /**
     * Devuelve todas las Notas Canceladas
     */
    public function getCancellations()
    {
        $orders = Order::where('folio_status_id', 3)->get();

        foreach ($orders as $order) {

            $user = User::find($order->created_by);
            $client = Client::where('id', $order->id_cliente)
                ->orWhere('phone', $order->id_cliente)
                ->first();

            $fecha = Carbon::parse($order->created_at)->format('d/m/Y - H:i');
            $productos = $order->productos ? json_decode($order->productos) : null;
            $promos = $order->descuentos ? json_decode($order->descuentos) : null;
            $anticipos = $order->detalle_anticipo ? json_decode($order->detalle_anticipo) : null;
            $pagos = $order->detalles_pago ? json_decode($order->detalles_pago) : null;

            $order->fecha = $fecha;
            $order->created_by = $user;
            $order->id_cliente = $client;
            $order->productos = $productos;
            $order->descuentos = $promos;
            $order->detalle_anticipo = $anticipos;
            $order->detalles_pago = $pagos;
        }

        return response()->json($orders, 200);
    }

    /*
    * Devuelve una Cotizacion por su ID
    */
    public function getOrder($id)
    {
        // Encontramos la cotizacion
        $order = Order::find($id);

        // Si no se encuentra la cotizacion, se devuelve un error
        if ($order == null) {
            return response()->json([
                'message' => 'No se encontro la cotizacion'
            ], 404);
        }

        // Obtenemos los datos del cliente, usuario y tipo de folio
        $client = Client::where('id', $order->id_cliente)
            ->orWhere('phone', $order->id_cliente)
            ->first();


        $user = User::find($order->created_by);

        if ($order->edited_by != null) {
            $edited_by = User::find($order->edited_by);
            $order->edited_by = $edited_by;
        }

        $folioType = FolioType::find($order->folio_status_id);

        // Decodificamos los campos JSON para ser usados en Front End
        $productos = $order->productos ? json_decode($order->productos) : null;
        $promos = $order->descuentos ? json_decode($order->descuentos) : null;
        $anticipos = $order->detalle_anticipo ? json_decode($order->detalle_anticipo) : null;
        $pagos = $order->detalles_pago ? json_decode($order->detalles_pago) : null;

        // Agregamos los bancos y los tipos de pagos a los detalles de pago y anticipo
        if ($anticipos != null) {
            foreach ($anticipos as $anticipo) {
                $banco = $anticipo->bank > 0  ? Banks::find($anticipo->bank) : null;
                $anticipo->bank = $banco != null ? $banco->bank : null;
                $tipoDePago = PaymentType::find($anticipo->paymentType);
                $anticipo->paymentType = $tipoDePago->value;
            }
        }

        if ($pagos != null) {
            foreach ($pagos as $pago) {
                $banco = $pago->bank > 0  ? Banks::find($pago->bank) : null;
                $pago->bank = $banco != null ? $banco->bank : null;
                $tipoDePago = PaymentType::find($pago->paymentType);
                $pago->paymentType = $tipoDePago->value;
            }
        }

        // Convertimos la fecha en un formato mas legible
        $fecha = Carbon::parse($order->created_at)->format('d/m/Y - H:i');

        // Guarda los datos en la salida
        $order->fecha = $fecha;
        $order->created_by = $user;
        $order->id_cliente = $client;
        $order->folio_status_id = $folioType->name;
        $order->detalle_anticipo = $anticipos;
        $order->detalles_pago = $pagos;
        $order->productos = $productos;
        $order->descuentos = $promos;


        return response()->json($order, 200);
    }


    /**
     * obtiene las cotizaciones de un cliente
     */
    public function getOrdersByClient($id)
    {
        $orders = Order::where('id_client', $id)->get();

        foreach ($orders as $order) {

            $user = User::find($order->created_by);
            $client = Client::where('id', $order->id_cliente)
                ->orWhere('phone', $order->id_cliente)
                ->first();

            $fecha = Carbon::parse($order->created_at)->format('d/m/Y - H:i');
            $productos = $order->productos ? json_decode($order->productos) : null;
            $promos = $order->descuentos ? json_decode($order->descuentos) : null;
            $anticipos = $order->detalle_anticipo ? json_decode($order->detalle_anticipo) : null;
            $pagos = $order->detalles_pago ? json_decode($order->detalles_pago) : null;

            $order->fecha = $fecha;
            $order->created_by = $user;
            $order->id_cliente = $client;
            $order->productos = $productos;
            $order->descuentos = $promos;
            $order->detalle_anticipo = $anticipos;
            $order->detalles_pago = $pagos;
        }

        return response()->json($orders, 200);
    }

    /**
     * Obtiene las notas de venta de un cliente
     */
    public function getNotasVentaByClient($id)
    {
        $orders = Order::where('id_client', $id)->where('folio_status_id', 2)->get();

        foreach ($orders as $order) {

            $user = User::find($order->created_by);
            $client = Client::where('id', $order->id_cliente)
                ->orWhere('phone', $order->id_cliente)
                ->first();

            $fecha = Carbon::parse($order->created_at)->format('d/m/Y - H:i');
            $productos = $order->productos ? json_decode($order->productos) : null;
            $promos = $order->descuentos ? json_decode($order->descuentos) : null;
            $anticipos = $order->detalle_anticipo ? json_decode($order->detalle_anticipo) : null;
            $pagos = $order->detalles_pago ? json_decode($order->detalles_pago) : null;

            $order->fecha = $fecha;
            $order->created_by = $user;
            $order->id_cliente = $client;
            $order->productos = $productos;
            $order->descuentos = $promos;
            $order->detalle_anticipo = $anticipos;
            $order->detalles_pago = $pagos;
        }

        return response()->json($orders, 200);
    }
}
