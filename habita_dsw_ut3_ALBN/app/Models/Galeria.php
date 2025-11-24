<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeria extends Model
{
    protected $table = 'galeria';
    protected $fillable = ['producto_id', 'ruta', 'es_principal'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
