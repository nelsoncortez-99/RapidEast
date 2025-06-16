<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empleado extends Model
{
    use HasFactory;
    protected $table = "empleados";
    protected $primaryKey = "codigo";
    protected $fillable = ['nombre','apellido','telefono','user'];
    public $hidden = ['created_at','update_at'];
    public $timestamps = true;

    public static function getFilteredData($search) {
    return Empleado::select('empleados.*', 'users.name AS usuario')
        ->leftJoin("users", "users.id", "=", "empleados.user") // Cambiado a users.id
        ->where(function($query) use ($search) {
            $query->where('empleados.codigo', 'like', $search)
                ->orWhere('empleados.nombre', 'like', $search)
                ->orWhere('empleados.apellido', 'like', $search)
                ->orWhere('empleados.telefono', 'like', $search)
                ->orWhere('users.name', 'like', $search);
        });
}
    public function user()
    {
        return $this->belongsTo(User::class, 'user', 'id'); // Relación especial
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage) {
        //se utiliza self para invocar metodo static dentro de la misma clase
        $query = self::getFilteredData($search);
        if ($itemsPerPage !== -1) {//validar para extraer todos los datos
            $query->skip($skip)
                ->take($itemsPerPage);
        }
        $query->orderBy("empleados.$sortBy", $sort);
            
        return $query->get();
            
    }
}
