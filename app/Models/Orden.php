<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orden extends Model
{
    use HasFactory;
    protected $table = "ordenes";
    protected $primaryKey = "codigo";
    protected $fillable = ['fecha','numeromesa','total','client','empleado','state','mpago'];
    public $hidden = ['created_at','update_at'];
    public $timestamps = true;

    public static function getFilteredData($search) {
        return Orden::select('ordenes.*', 'categoria.nombre AS categoria')
            ->join("cliente", "cliente.codigo", "=", "ordenes.client")
            ->join("empleados", "empleados.codigo", "=", "ordenes.empleado")
            ->join("estado", "estado.codigo", "=", "ordenes.state")
            ->join("metodopago", "metodopago.codigo", "=", "ordenes.mpago")

            ->where('ordenes.codigo', 'like', $search)
            ->orWhere('ordenes.fecha', 'like', $search)
            ->orWhere('ordenes.numeromesa', 'like', $search)
            ->orWhere('ordenes.total', 'like', $search)
            ->orWhere('cliente.nombre', 'like', $search)
            ->orWhere('empleados.nombre', 'like', $search)
            ->orWhere('estado.nombre', 'like', $search)
            ->orWhere('metodopago.nombre', 'like', $search);
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage) {
        //se utiliza self para invocar metodo static dentro de la misma clase
        $query = self::getFilteredData($search);
        if ($itemsPerPage !== -1) {//validar para extraer todos los datos
            $query->skip($skip)
                ->take($itemsPerPage);
        }
        $query->orderBy("ordenes.$sortBy", $sort);
            
        return $query->get();
            
    }
}
