<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orden extends Model
{
    use HasFactory;

    protected $table = "ordenes";
    protected $primaryKey = "codigo";
    protected $fillable = ['fecha', 'numeromesa', 'client', 'empleado', 'state', 'mpago'];
    protected $hidden = ['created_at', 'updated_at'];
    public $timestamps = true;

    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'orden_id', 'codigo');
    }

    // Relaciones a cliente, empleado, estado y metodo de pago
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'client', 'codigo');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado', 'codigo');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'state', 'codigo');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'mpago', 'codigo');
    }

    public function eliminarOrdenCompleta(): bool
    {
        try {
            DB::beginTransaction();
            
            // Eliminar todos los detalles primero
            $this->detalles()->delete();
            
            // Luego eliminar la orden
            $resultado = $this->delete();
            
            DB::commit();
            
            return $resultado;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Actualiza el estado de la orden
     */
    public function actualizarEstadoOrden(string $nuevoEstado): bool
    {
        $this->state = $nuevoEstado;
        return $this->save();
    }

    // Métodos de búsqueda que tienes
    public static function getFilteredData($search) {
        return Orden::select('ordenes.*')
            ->join("cliente", "cliente.codigo", "=", "ordenes.client")
            ->join("empleados", "empleados.codigo", "=", "ordenes.empleado")
            ->join("estado", "estado.codigo", "=", "ordenes.state")
            ->join("metodopago", "metodopago.codigo", "=", "ordenes.mpago")

            ->where('ordenes.codigo', 'like', $search)
            ->orWhere('ordenes.fecha', 'like', $search)
            ->orWhere('ordenes.numeromesa', 'like', $search)
            ->orWhere('cliente.nombre', 'like', $search)
            ->orWhere('empleados.nombre', 'like', $search)
            ->orWhere('estado.nombre', 'like', $search)
            ->orWhere('metodopago.nombre', 'like', $search);
    }

    public function getTotalAttribute()
    {
        return $this->detalles->sum('subtotal');
    }
    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage) {
        $query = self::getFilteredData($search);
        if ($itemsPerPage !== -1) {
            $query->skip($skip)->take($itemsPerPage);
        }
        $query->orderBy("ordenes.$sortBy", $sort);

        return $query->get();
    }
}