<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Categoria;
use App\Models\Galeria;

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
    public function categorias() : BelongsToMany
    {
        return $this->belongsToMany(Categoria::class);
    }


    public function galeria()
    {
        return $this->hasMany(Galeria::class, 'producto_id');
    }

    public function imagenPrincipal()
    {
        return $this->hasOne(Galeria::class, 'producto_id')->where('es_principal', true);
    }
}
