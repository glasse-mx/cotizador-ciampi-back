<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\FolioCotizaciones;
use App\Models\FolioNotaCancelada;
use App\Models\FolioNotaVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FoliosController extends Controller
{
    public function createFolioCotizacion()
    {
        $folio = new FolioCotizaciones();
        $folio->save();

        return response()->json([
            "mensaje" => "OK",
            $folio
        ]);
    }

    public function createFolioNotaVenta()
    {
        $folio = new FolioNotaVenta();
        $folio->save();

        return response()->json([
            "mensaje" => "OK",
            $folio
        ]);
    }

    public function createFolioNotaCancelada()
    {
        $folio = new FolioNotaCancelada();
        $folio->save();

        return response()->json([
            "mensaje" => "OK",
            $folio
        ]);
    }
}
