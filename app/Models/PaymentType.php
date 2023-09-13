<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;

    protected $table = 'payment_type'; // Reemplaza con el nombre de tu tabla
    protected $primaryKey = 'id'; // Si la clave primaria es diferente, ajústala
}
