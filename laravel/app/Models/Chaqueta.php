<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chaqueta extends Model
{
    protected $table = 'chaqueta';

    public function getRouteKeyName()
    {
        return 'id_chaqueta';
    }

    protected $primaryKey = 'id_chaqueta';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'id_chaqueta',
        'modelo_chaqueta',
        'precio',
        'imagen',
        'estado',
        'categoria_id_categoria',
    ];

    protected $casts = [
        'precio' => 'decimal:1',
        'estado' => 'string',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id_categoria', 'id_categoria');
    }

    public function materiales()
    {
        return $this->belongsToMany(
            Material::class,
            'chaqueta_has_materiales',
            'chaqueta_id_chaqueta',
            'materiales_id_materiales',
            'id_chaqueta',
            'id_materiales'
        );
    }

    public function tallas()
    {
        return $this->belongsToMany(
            Talla::class,
            'chaqueta_has_talla',
            'chaqueta_id_chaqueta',
            'talla_id_talla',
            'id_chaqueta',
            'id_talla'
        )->orderBy('orden');
    }

    public function stock()
    {
        return $this->hasMany(
            \App\Models\Stock::class,
            'chaqueta_id_chaqueta',
            'id_chaqueta'
        );
    }
}
