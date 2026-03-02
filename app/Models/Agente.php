<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agente extends Model
{
    protected $fillable = [
        'nombre_pc',
        'sala',
        'estado',
        'ultimo_heartbeat',
        'fecha_registro',
        'info_sistema'
    ];

    protected $casts = [
        'ultimo_heartbeat' => 'datetime',
        'fecha_registro' => 'datetime',
        'info_sistema' => 'json'
    ];

    public function comandos()
    {
        return $this->hasMany(Comando::class);
    }
}

