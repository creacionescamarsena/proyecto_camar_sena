<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Talla extends Model
{
    protected $table = 'talla';

    protected $primaryKey = 'id_talla';

    public $timestamps = false;

    protected $fillable = [
        'talla',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function chaquetas()
    {
        return $this->belongsToMany(
            Chaqueta::class,
            'chaqueta_has_talla',
            'talla_id_talla',
            'chaqueta_id_chaqueta',
            'id_talla',
            'id_chaqueta'
        );
    }
}
