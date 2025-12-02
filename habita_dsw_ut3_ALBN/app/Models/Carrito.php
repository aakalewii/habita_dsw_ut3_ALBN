<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Carrito extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'sesion_id', 'total', 'estado'];

    public function items(): HasMany
    {
        return $this->hasMany(CarritoItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }
}
