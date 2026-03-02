<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sala extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'capacidad',
        'ubicacion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    /**
     * Relación: Una sala tiene muchos agentes
     */
    public function agentes(): HasMany
    {
        return $this->hasMany(Agente::class);
    }

    /**
     * Obtener total de agentes conectados en la sala
     */
    public function totalAgentes(): int
    {
        return $this->agentes()->count();
    }

    /**
     * Obtener agentes conectados en la sala
     */
    public function agentesConectados(): int
    {
        return $this->agentes()->where('estado', 'conectado')->count();
    }

    /**
     * Obtener agentes desconectados en la sala
     */
    public function agentesDesconectados(): int
    {
        return $this->agentes()->where('estado', 'desconectado')->count();
    }

    /**
     * Obtener array con estadísticas de la sala
     */
    public function estadisticas(): array
    {
        return [
            'sala_id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'ubicacion' => $this->ubicacion,
            'capacidad' => $this->capacidad,
            'total_agentes' => $this->totalAgentes(),
            'agentes_conectados' => $this->agentesConectados(),
            'agentes_desconectados' => $this->agentesDesconectados(),
            'estado' => $this->estado,
            'porcentaje_ocupacion' => round(($this->totalAgentes() / $this->capacidad) * 100, 2),
        ];
    }
}
