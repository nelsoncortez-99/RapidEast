<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory;
    protected $table = "categoria";
    protected $primaryKey = "codigo";
    protected $fillable = ['nombre'];
    public $hidden = ['created_at','update_at'];
    public $timestamps = true;

    public static function getFilteredData($search) {
        return Categoria::select('categoria.*')

            ->where('categoria.codigo', 'like', $search)
            ->orWhere('categoria.nombre', 'like', $search);
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage) {
        //se utiliza self para invocar metodo static dentro de la misma clase
        $query = self::getFilteredData($search);
        if ($itemsPerPage !== -1) {//validar para extraer todos los datos
            $query->skip($skip)
                ->take($itemsPerPage);
        }
        $query->orderBy("categoria.$sortBy", $sort);
            
        return $query->get();
            
    }
    public function menus()
{
    return $this->hasMany(Menu::class,'category');
}
}