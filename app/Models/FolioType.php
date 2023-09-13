<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolioType extends Model
{
    use HasFactory;

    protected $table = 'folio_status'; // Reemplaza con el nombre de tu tabla
    protected $primaryKey = 'id'; // Si la clave primaria es diferente, ajústala
}
