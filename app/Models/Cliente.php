<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;
    protected $table = "cliente";
    protected $primaryKey = "codigo";
    protected $fillable = ['nombre','apellido','correo'];
    public $hidden = ['created_at','update_at'];
    public $timestamps = true;

    public static function getFilteredData($search) {
        return Cliente::select('cliente.*')

            ->where('cliente.codigo', 'like', $search)
            ->orWhere('cliente.nombre', 'like', $search)
            ->orWhere('cliente.apellido', 'like', $search)
            ->orWhere('cliente.correo', 'like', $search);
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage) {
        //se utiliza self para invocar metodo static dentro de la misma clase
        $query = self::getFilteredData($search);
        if ($itemsPerPage !== -1) {//validar para extraer todos los datos
            $query->skip($skip)
                ->take($itemsPerPage);
        }
        $query->orderBy("cliente.$sortBy", $sort);
            
        return $query->get();
            
    }
}
