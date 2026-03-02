<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agente extends Model
{
    protected $fillable = [
        'nombre_pc',
        'sala_id',
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

    /**
     * Determinar si el agente está conectado
     * (conectado si recibió heartbeat en los últimos 15 segundos)
     */
    public function isConectado(): bool
    {
        if (!$this->ultimo_heartbeat) {
            return false;
        }
        
        $tiempoTranscurrido = now()->diffInSeconds($this->ultimo_heartbeat);
        return $tiempoTranscurrido <= 15; // 15 segundos de tolerancia
    }

    /**
     * Relación: Un agente pertenece a una sala
     */
    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class);
    }

    /**
     * Relación: Un agente tiene muchos comandos
     */
    public function comandos(): HasMany
    {
        return $this->hasMany(Comando::class);
    }

    /**
     * Obtener información formateada del agente
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        
        // Incluir la relación sala si está cargada
        if ($this->relationLoaded('sala')) {
            $data['sala'] = $this->sala ? [
                'id' => $this->sala->id,
                'nombre' => $this->sala->nombre,
            ] : null;
        }
        
        return $data;
    }
}
