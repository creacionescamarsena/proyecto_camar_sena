<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $primaryKey = 'id_pedido';

    public $timestamps = false;

    protected $fillable = [
        'facturacion_id_facturacion',
        'fecha_pedido',
        'estado'
    ];
}