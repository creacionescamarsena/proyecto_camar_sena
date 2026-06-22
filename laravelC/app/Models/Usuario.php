<?php

namespace App\Models;

use App\Models\TipoDocumento;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;
    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';

    public $incrementing = false;

    public $timestamps = true;

    protected $keyType = 'string';

    protected $fillable = [
        'id_usuario',
        'nombres',
        'apellidos',
        'tipo_documento_id',
        'correo',
        'telefono',
        'contraseña',
        'rol',
        'estado',
        'email',
        'password',
    ];

    protected $hidden = [
        'contraseña',
        'password',
        'remember_token',
    ];

    protected $casts = [
        'estado' => 'string',
        'rol' => 'string',
    ];

    public function getAuthIdentifierName(): string
    {
        return $this->getKeyName();
    }

    public function getAuthPassword(): string
    {
        return $this->getAttribute('contraseña') ?? $this->getAttribute('password') ?? '';
    }

    public function getEmailAttribute(): ?string
    {
        return $this->attributes['correo'] ?? $this->attributes['email'] ?? null;
    }

    public function setEmailAttribute($value): void
    {
        $this->attributes['correo'] = $value;
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->attributes['contraseña'] ?? $this->attributes['password'] ?? null;
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['contraseña'] = $value;
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    public function tipo_documento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id', 'id_tipo');
    }
}