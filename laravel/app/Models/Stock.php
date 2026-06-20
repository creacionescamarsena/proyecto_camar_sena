<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock';

    protected $primaryKey = 'cod_stock';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'chaqueta_id_chaqueta',
        'talla_id_talla',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function chaqueta()
    {
        return $this->belongsTo(
            Chaqueta::class,
            'chaqueta_id_chaqueta',
            'id_chaqueta'
        );
    }

    public function talla()
    {
        return $this->belongsTo(
            Talla::class,
            'talla_id_talla',
            'id_talla'
        );
    }
}
