<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolioNotaCancelada extends Model
{
    use HasFactory;

    protected $table = 'folios_notas_canceladas'; // Reemplaza con el nombre de tu tabla
    protected $primaryKey = 'id'; // Si la clave primaria es diferente, ajústala
    public $timestamps = true;
}
