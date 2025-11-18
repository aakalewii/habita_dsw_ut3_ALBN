<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada (opcional si sigue convención plural)
     */
    protected $table = 'productos';

    /**
     * Clave primaria (por defecto es 'id')
     */
    protected $primaryKey = 'id';

    /* Para utilizar el request->all() */
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'materiales',
        'dimensiones',
        'color_principal',
        'imagen_principal',
        'destacado',
        'categoria_id'
    ];

    /**
     * Conversión de tipos automáticos
     */
    protected $casts = [
        'precio' => 'decimal:2',
        'destacado' => 'boolean',
    ];

    /**
     * Relación: un producto pertenece a una categoría
     */
    public function categorias() : BelongsToMany{
        return $this->belongsToMany(Categoria::class);
    }
}
