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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    /**
     * Crea un Folio Nuevo como Cotizacion
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
            $order->detalles_envio = json_encode($request->detalles_envio);
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
     * Edita un Folio existente como Cotizacion
     */
    public function editOrder($id, Request $request)
    {
        $order = Order::find($id);

        $order->edited_by = $request->edited_by;
        $order->id_cliente = $request->id_cliente;
        $order->productos = json_encode($request->productos);
        $order->descuentos = json_encode($request->descuentos);
        $order->folio_status_id = 1;
        $order->subtotal_productos = json_encode($request->subtotal_productos);
        $order->subtotal_promos = $request->subtotal_promos;
        $order->detalle_anticipo = json_encode($request->detalles_anticipo);
        $order->detalles_pago = json_encode($request->detalles_pago);
        $order->observaciones = $request->observaciones;
        $order->detalles_envio = json_encode($request->detalles_envio);
        $order->salida = $request->salida;
        $order->llegada = $request->llegada;
        $order->total = $request->total;
        $order->save();

        return response()->json($order, 201);
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
     * Convierte una Cotizacion/N. Venta en una Nota Cancelada
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
    public function getOrders(Request $request)
    {

        $user = User::find($request->user_id);

        // return response()->json($user, 200);

        $orders = $user->user_type === 1
            ? Order::where('folio_status_id', 1)->where('created_by', $user->id)->get()
            : Order::where('folio_status_id', 1)->get();


        foreach ($orders as $order) {

            $seller = User::find($order->created_by);
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
            $order->created_by = $seller;
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
    public function getSales(Request $request)
    {

        $user = User::find($request->user_id);

        $orders = $user->user_type === 1
            ? Order::where('folio_status_id', 2)->where('created_by', $user->id)->get()
            : Order::where('folio_status_id', 2)->get();


        foreach ($orders as $order) {

            $client = Client::where('id', $order->id_cliente)
                ->orWhere('phone', $order->id_cliente)
                ->first();

            $fecha = Carbon::parse($order->created_at)->format('d/m/Y - H:i');
            $productos = $order->productos ? json_decode($order->productos) : null;
            $promos = $order->descuentos ? json_decode($order->descuentos) : null;
            $anticipos = $order->detalle_anticipo ? json_decode($order->detalle_anticipo) : null;
            $pagos = $order->detalles_pago ? json_decode($order->detalles_pago) : null;
            $created_by = User::find($order->created_by);

            $order->fecha = $fecha;
            $order->created_by = $created_by;
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
        $detallesEnvio = $order->detalles_envio ? json_decode($order->detalles_envio) : null;

        // Convertimos la fecha en un formato mas legible
        if ($order->folio_status_id === 2) {
            $notaVenta = FolioNotaVenta::find($order->folio_nota_venta_id);
            $fecha = Carbon::parse($notaVenta->created_at)->format('d/m/Y - H:i');
        } else {
            $cotizacion = FolioCotizaciones::find($order->folio_cotizacion_id);
            $fecha = Carbon::parse($cotizacion->created_at)->format('d/m/Y - H:i');
        }

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
        $order->detalles_envio = $detallesEnvio;


        return response()->json($order, 200);
    }


    /**
     * obtiene las cotizaciones de un cliente
     */
    public function getOrdersByClient($id)
    {
        $orders = Order::where('id_cliente', $id)->paginate(10);

        foreach ($orders as $order) {

            // Ubicamos los datos de cliente y usuario
            $user = User::find($order->created_by);
            $edited = User::find($order->edited_by);
            $client = Client::where('id', $order->id_cliente)
                ->orWhere('phone', $order->id_cliente)
                ->first();

            $fecha = Carbon::parse($order->created_at)->format('d/m/Y - H:i');
            $productos = $order->productos ? json_decode($order->productos) : null;
            $promos = $order->descuentos ? json_decode($order->descuentos) : null;
            $anticipos = $order->detalle_anticipo ? json_decode($order->detalle_anticipo) : null;
            $pagos = $order->detalles_pago ? json_decode($order->detalles_pago) : null;

            $folioType = FolioType::find($order->folio_status_id);


            $order->fecha = $fecha;
            $order->created_by = $user;
            $order->id_cliente = $client;
            $order->edited_by = $edited;
            $order->productos = $productos;
            $order->descuentos = $promos;
            $order->detalle_anticipo = $anticipos;
            $order->detalles_pago = $pagos;
            $order->folio_status_id = $folioType->name;
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

    /**
     * Cambia el estado de aprobacion de una orden
     */

    public function setOrderApproval($id, Request $request)
    {

        if ($id == null) {
            return response()->json([
                'message' => 'No se encontro la cotizacion'
            ], 404);
        } else {
            $order = Order::find($id);
        }

        if (!$request->user_id) {
            return response()->json([
                'message' => 'No se Suministro un usuario valido'
            ], 404);
        } else {
            $user = User::find($request->user_id);
        }

        switch ($user->user_type) {
            case 2:
                $order->pdv_approval = true;
                $order->save();
                break;
            case 3:
                $order->assistant_approval = true;
                $order->save();
                break;
            case 4:
                $order->head_approval = true;
                $order->save();
                break;
            case 5:
                $order->ceo_approval = true;
                $order->save();
                break;
            default:
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta accion'
                ], 401);
                break;
        }

        return response()->json($order, 200);
    }

    /**
     * Obtiene el resumen del mes actual de las ventas
     */
    public function getSalesSummary()
    {

        // Se obtiene el mes actual y la cantidad de semanas que tiene
        $firstDayOfMonth = Carbon::now()->startOfMonth();
        $lastDayOfMonth = Carbon::now()->endOfMonth();
        $weeksInMonth = $firstDayOfMonth->diffInWeeks($lastDayOfMonth) + 1;

        // Se obtienen las cotizaciones
        $invoices = Order::where('folio_status_id', 1)->whereBetween(
            'created_at',
            [$firstDayOfMonth, $lastDayOfMonth]
        )->get();

        // Se obtienen todas las notas de venta
        $sales = Order::where('folio_status_id', 2)->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])->get();

        // Se Obtienen a los vendedores
        $sellers = User::whereIn('user_type', [1, 2])->where('activo', 1)->get();

        // Se declaran las variables estaticas para el resumen
        $totalMXN = 0;
        $maquinas = 0;
        $reguladores = 0;
        $insumos = 0;
        $anticipos = 0;
        $agentes = [];
        $semanas = array_map(function ($numero) {
            return 'semana-' . $numero;
        }, range(1, $weeksInMonth));

        // Se declaran las variables dinamicas para el resumen
        foreach ($sellers as $seller) {
            // $agentes[$seller->id] = array_merge(['vendedor' => $seller->name], array_fill(0, $weeksInMonth, 0), ['anticipos' => 0]);
            $agentes[$seller->id] = array_merge(['vendedor' => $seller->name], array_fill_keys($semanas, 0), ['anticipos' => 0]);
        }

        // Se obtienen los anticipos pagos
        foreach ($invoices as $invoice) {
            $detalles = json_decode($invoice->detalle_anticipo) ? json_decode($invoice->detalle_anticipo) : null;
            $prods = json_decode($invoice->productos) ? json_decode($invoice->productos) : null;
            $hayMaquina = false;
            $vendedor = $invoice->created_by;

            foreach ($prods as $prod) {
                if ($prod->categories == 'maquinas') {
                    $hayMaquina = true;
                    break;
                }
            }

            if ($hayMaquina && $detalles != null) {
                $agentes[$vendedor]['anticipos'] += 1;
                $anticipos += 1;
            }
        }

        foreach ($sales as $order) {
            $totalMXN += $order->total;
            $prods = json_decode($order->productos) ? json_decode($order->productos) : null;
            $folio = FolioNotaVenta::find($order->folio_nota_venta_id);

            $weekOfOrder = $firstDayOfMonth->diffInWeeks(Carbon::parse($folio->created_at)) + 1;

            foreach ($prods as $prod) {
                if (isset($prod->categories) && $prod->categories == 'maquinas') {
                    $maquinas += 1;
                    $agentes[$order->created_by]['semana-' . $weekOfOrder] += 1;
                } elseif (isset($prod->categories) && $prod->categories == 'reguladores') {
                    $reguladores += 1;
                } elseif (isset($prod->categories) && $prod->categories == 'insumos') {
                    $insumos += 1;
                }
            }
        }

        $summary = [
            'semanas' => $weeksInMonth,
            'total' => $totalMXN,
            'anticipos' => $anticipos,
            'maquinas' => $maquinas,
            'reguladores' => $reguladores,
            'insumos' => $insumos,
            'ventas' => $sales->count(),
            'agentes' => $agentes
        ];

        return response()->json($summary, 200);
    }

    function getOrdersGroupedByWeek()
    {
        // Obtener la fecha de inicio del mes actual
        $startOfMonth = now()->startOfMonth()->toDateString();
        // Obtener la fecha de fin del mes actual
        $endOfMonth = now()->endOfMonth()->toDateString();

        // Consulta para obtener los registros del presente mes, separados en semanas
        $ordersByWeek = Order::select(DB::raw('WEEK(created_at) as week_number'), DB::raw('YEAR(created_at) as year'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('year', 'week_number')
            ->get();

        return response()->json($ordersByWeek, 200);
    }
}
