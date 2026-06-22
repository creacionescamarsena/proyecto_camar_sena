<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';

    protected $primaryKey = 'id_categoria';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'tipo_categoria',
        'estado_categoria',
    ];

    public function chaquetas()
    {
        return $this->hasMany(Chaqueta::class, 'categoria_id_categoria', 'id_categoria');
    }
}
