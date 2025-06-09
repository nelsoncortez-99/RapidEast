<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;
    protected $table = "menu";
    protected $primaryKey = "codigo";
    protected $fillable = ['nombre','descripcion','precio','category'];
    public $hidden = ['created_at','update_at'];
    public $timestamps = true;

    public static function getFilteredData($search) {
        return Empleado::select('menu.*', 'categoria.nombre AS categoria')
            ->join("categoria", "categoria.codigo", "=", "menu.category")

            ->where('menu.codigo', 'like', $search)
            ->orWhere('menu.nombre', 'like', $search)
            ->orWhere('menu.apellido', 'like', $search)
            ->orWhere('menu.telefono', 'like', $search)
            ->orWhere('categoria.nombre', 'like', $search);
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage) {
        //se utiliza self para invocar metodo static dentro de la misma clase
        $query = self::getFilteredData($search);
        if ($itemsPerPage !== -1) {//validar para extraer todos los datos
            $query->skip($skip)
                ->take($itemsPerPage);
        }
        $query->orderBy("menu.$sortBy", $sort);
            
        return $query->get();
            
    }
}
