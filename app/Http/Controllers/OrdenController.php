<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleOrden;
use App\Models\User;
use App\Models\MetodoPago;
use App\Models\Menu;
use App\Models\Orden;
use App\Models\Categoria;
use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;

class OrdenController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = Orden::with(['cliente', 'detalles.menu'])->get();
        return view('ordenes.index', compact('ordenes')); // Ahora apunta a index.blade.php
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::all();
        $categorias = Categoria::all();
        $menu = Menu::all();
        $mediospago = MetodoPago::all();

        return view('ordenes.create', compact('clientes', 'categorias', 'menu', 'mediospago'));
    }

    public function ValidarCampos($request) {
        return Validator::make($request->all(),[
            'fecha' => 'required',
            'numeromesa' => 'required',
            'client' => 'required',
            'empleado' => 'required',
            'state' => 'required',
            'mpago' => 'required'
        ], [
            'fecha.required' => 'Fecha es obligatorio',
            'numeromesa.required' => 'Número de mesa es obligatoria',
            'client.required' => 'Cliente es obligatoria es obligatorio',
            'empleado.required' => 'Empleado es obligatoria es obligatorio',
            'state.required' => 'Estado es obligatoria es obligatorio',
            'mpago.required' => 'Método de pago es obligatoria es obligatorio'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'numeromesa' => 'required|integer|min:1',
        'client' => 'required|exists:cliente,codigo',
        'mpago' => 'required|exists:metodopago,codigo',
        'detalle' => 'required|json',
    ]);

    try {
        DB::beginTransaction();

        // Decodificar el detalle (carrito)
        $detalle = json_decode($request->detalle, true);

        // Crear la orden
        $orden = Orden::create([
            'client' => $request->client,
            'numeromesa' => $request->numeromesa,
            'empleado' => auth()->id(),
            'mpago' => $request->mpago,
            'state' => $request->state,
            'fecha' => now(),
            'total' => array_sum(array_column($detalle, 'subtotal')), // Calcular total
        ]);

        // Guardar cada ítem del detalle
        foreach ($detalle as $item) {
            DetalleOrden::create([
                'orden_id' => $orden->codigo,
                'menu_id' => $item['menu_id'],
                'cantidad' => $item['cantidad'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Orden guardada correctamente',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar: ' . $e->getMessage(),
        ], 500);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Buscar la orden por id con las relaciones necesarias
        $orden = Orden::with(['cliente', 'detalles.menu'])->findOrFail($id);

        // Retornar la vista para mostrar esa orden individual
        return view('ordenes.show', compact('orden'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $menu = Menu::all();//extrayendo marcas
        $menu=Orden::where('codigo',$id)->first();
        return view('ordenes/update')->with([
            'orden' => $orden,
            'menu' => $menu
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    // Si es un cambio de estado desde tu función JavaScript
    if ($request->has('estado')) {
        $orden = Orden::findOrFail($id);
        $orden->estado = $orden->estado == 'completado' ? 'pendiente' : 'completado';
        $orden->save();
        
        return response()->json([
            'estado' => $orden->estado,
            'code' => 200,
            'message' => 'Estado actualizado'
        ]);
    }

    // Validación para actualización normal
    $validacion = $this->ValidarCampos($request);
    if($validacion->fails()){
        return response()->json([
            'code' => 422,
            'message' => $validacion->messages()
        ], 422);
    }

    // Actualización normal
    $orden = Orden::find($id);
    if($orden){
        $orden->update([
            'fecha' => $request->fecha,
            'numeromesa' => $request->numeromesa,
            'client' => $request->client,
            'empleado' => $request->empleado,
            'state' => $request->state,
            'mpago' => $request->mpago
        ]);
        
        return response()->json([
            'code' => 200,
            'message' => "Registro actualizado"
        ], 200);
    }
    
    return response()->json([
        'code' => 400,
        'message' => "Registro no encontrado"
    ], 400);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orden=Orden::find($id);
        if($orden){
            $orden->delete();
            return response()->json([
                    'code' => 200,
                    'message' => "Registro eliminado"
                ],200);
        }else{
            return response()->json([
                    'code' => 400,
                    'message' => "Registro no encontrado"
                ],400);
        }
    }
}
