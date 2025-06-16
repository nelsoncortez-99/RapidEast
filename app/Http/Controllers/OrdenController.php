<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleOrden;
use App\Models\MetodoPago;
use App\Models\Menu;
use App\Models\Estado;
use App\Models\Orden;
use App\Models\Categoria;
use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrdenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = Orden::with(['cliente', 'detalles.menu'])->get();
        return view('ordenes.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::all();
        $categorias = Categoria::all();
        $menu = Menu::all();
        $estados = Estado::all();
        $mediospago = MetodoPago::all();

        return view('ordenes.create', compact('clientes', 'categorias', 'menu', 'mediospago','estados'));
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

            $detalle = json_decode($request->detalle, true);

            $orden = Orden::create([
                'client' => $request->client,
                'numeromesa' => $request->numeromesa,
                'empleado' => auth()->id(),
                'mpago' => $request->mpago,
                'state' => 1, // Estado inicial: Pendiente
                'fecha' => now(),
                'total' => array_sum(array_column($detalle, 'subtotal')),
            ]);

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
        $orden = Orden::with(['cliente', 'detalles.menu'])->findOrFail($id);
        return view('ordenes.show', compact('orden'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $orden = Orden::with(['detalles.menu', 'cliente', 'metodoPago'])->findOrFail($id);
        $clientes = Cliente::all();
        $estados = Estado::all();
        $categorias = Categoria::with('menus')->get();
        $mediospago = MetodoPago::all();
        $menu = Menu::all();
        
        return view('ordenes.create', [
            'orden' => $orden,
            'clientes' => $clientes,
            'categorias' => $categorias,
            'mediospago' => $mediospago,
            'estados' => $estados,
            'menu' => $menu,
            'editing' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $orden = Orden::findOrFail($id);
        
        $validated = $request->validate([
            'numeromesa' => 'required|integer|min:1',
            'client' => 'required|exists:cliente,codigo',
            'mpago' => 'required|exists:metodopago,codigo',
            'state' => 'required|exists:estado,codigo',
            'detalle' => 'required|json'
        ]);

        try {
            DB::beginTransaction();
            
            $orden->update([
                'numeromesa' => $validated['numeromesa'],
                'client' => $validated['client'],
                'state' => $validated['state'],
                'mpago' => $validated['mpago']
            ]);

            $orden->detalles()->delete();
            $detalles = json_decode($validated['detalle'], true);
            $total = 0;
            
            foreach ($detalles as $item) {
                $subtotal = $item['precio'] * $item['cantidad'];
                $total += $subtotal;
                
                DetalleOrden::create([
                    'orden_id' => $orden->codigo,
                    'menu_id' => $item['menu_id'],
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $subtotal
                ]);
            }

            $orden->update(['total' => $total]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Orden actualizada correctamente',
                'redirect' => route('order.index')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $orden = Orden::findOrFail($id);
            
            DB::transaction(function() use ($orden) {
                $orden->detalles()->delete();
                $orden->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Orden eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders data for API
     */
    public function data()
    {
        $ordenes = Orden::with(['cliente', 'detalles.menu'])
                        ->orderBy('state')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json(['data' => $ordenes]);
    }
}