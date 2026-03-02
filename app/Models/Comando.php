<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comando extends Model
{
    protected $fillable = [
        'agente_id',
        'sala_id',
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

    /**
     * Relación: Un comando pertenece a un agente
     */
    public function agente(): BelongsTo
    {
        return $this->belongsTo(Agente::class);
    }

    /**
     * Relación: Un comando pertenece a una sala
     */
    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class);
    }
}
