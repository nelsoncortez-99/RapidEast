<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleOrden extends Model
{
    use HasFactory;

    protected $table = "detalleorden";
    protected $primaryKey = "codigo";
    protected $fillable = ['orden_id', 'menu_id', 'cantidad', 'subtotal'];
    protected $hidden = ['created_at', 'updated_at'];
    public $timestamps = true;

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id', 'codigo');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'codigo');
    }

    // Métodos de búsqueda
    public static function getFilteredData($search)
    {
        return DetalleOrden::select('detalleorden.*')
            ->join("menu", "menu.codigo", "=", "detalleorden.menu_id")
            ->join("ordenes", "ordenes.codigo", "=", "detalleorden.orden_id")

            ->where('detalleorden.codigo', 'like', $search)
            ->orWhere('menu.nombre', 'like', $search)
            ->orWhere('ordenes.numeromesa', 'like', $search)
            ->orWhere('ordenes.fecha', 'like', $search)
            ->orWhere('detalleorden.cantidad', 'like', $search)
            ->orWhere('detalleorden.subtotal', 'like', $search);
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage)
    {
        $query = self::getFilteredData($search);
        if ($itemsPerPage !== -1) {
            $query->skip($skip)->take($itemsPerPage);
        }
        $query->orderBy("detalleorden.$sortBy", $sort);

        return $query->get();
    }
}