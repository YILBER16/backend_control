<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comando extends Model
{
    protected $fillable = [
        'agente_id',
        'nombre_pc',
        'tipo',
        'parametros',
        'estado',
        'resultado',
        'fecha_envio',
        'fecha_ejecucion'
    ];

    protected $casts = [
        'parametros' => 'json',
        'fecha_envio' => 'datetime',
        'fecha_ejecucion' => 'datetime'
    ];

    public function agente()
    {
        return $this->belongsTo(Agente::class);
    }
}

