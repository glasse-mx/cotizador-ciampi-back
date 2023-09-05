<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolioCotizaciones extends Model
{
    use HasFactory;

    protected $table = 'folios_cotizaciones'; // Reemplaza con el nombre de tu tabla
    protected $primaryKey = 'id'; // Si la clave primaria es diferente, ajústala
    public $timestamps = true;

    public function order()
    {
        return $this->belongsTo(Order::class, 'folio_cotizacion_id');
    }
}
