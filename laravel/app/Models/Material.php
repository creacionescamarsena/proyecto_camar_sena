<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materiales';

    protected $primaryKey = 'id_materiales';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'material',
        'proveedor_material_cod_proveedor',
        'precio',
        'cantidad',
        'estado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'cantidad' => 'integer',
        'estado' => 'string',
    ];

    public function chaquetas()
    {
        return $this->belongsToMany(
            Chaqueta::class,
            'chaqueta_has_materiales',
            'materiales_id_materiales',
            'chaqueta_id_chaqueta',
            'id_materiales',
            'id_chaqueta'
        );
    }
 
}
