<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estado extends Model
{
    use HasFactory;
    protected $table = "estado";
    protected $primaryKey = "codigo";
    protected $fillable = ['nombre'];
    public $hidden = ['created_at','update_at'];
    public $timestamps = true;

    public static function getFilteredData($search) {
        return Estado::select('estado.*')

            ->where('estado.codigo', 'like', $search)
            ->orWhere('estado.nombre', 'like', $search);
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage) {
        //se utiliza self para invocar metodo static dentro de la misma clase
        $query = self::getFilteredData($search);
        if ($itemsPerPage !== -1) {//validar para extraer todos los datos
            $query->skip($skip)
                ->take($itemsPerPage);
        }
        $query->orderBy("estado.$sortBy", $sort);
            
        return $query->get();
            
    }
}
