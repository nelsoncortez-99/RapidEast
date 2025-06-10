<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleOrden;
use Illuminate\Support\Facades\Validator;

class DetalleOrdenController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('detalle/show');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('detalle/create');
    }

    public function ValidarCampos($request) {
        return Validator::make($request->all(),[
            'orden_id' => 'required',
            'menu_id' => 'required',
            'cantidad' => 'required',
            'subtotal' => 'required'
        ], [
            'orden_id.required' => 'Código de orden es obligatorio',
            'menu_id.required' => 'Código de menu es obligatoria',
            'cantidad.required' => 'Cantidad es obligatorio',
            'subtotal.required' => 'Subtotal es obligatoria es obligatorio'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validacion=$this->ValidarCampos($request);
        if($validacion -> fails()){
            return response()->json([
                'code' => 422,
                'message' => $validacion->messages()
            ],422);
        }else{
            $menu= DetalleOrden::create($request->all());
            return response()->json([
                'code' => 200,
                'message' => "Se creó el registro correctamente"
            ],200);
        }   
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $itemsPerPage = $request->input('length', 10);//registros por pagina
        $skip = $request->input('start', 0);//obtener indice inicial

        //para extraer todos los registros
        if ($itemsPerPage == -1) {
            $itemsPerPage =  DetalleOrden::count();
            $skip = 0;
        }

        //config to ordering
        $sortBy = $request->input('columns.'.$request->input('order.0.column').'.data','codigo');
        $sort = ($request->input('order.0.dir') === 'asc') ? 'asc' : 'desc';

        //config to search
        $search = $request->input('search.value', '');
        $search = "%$search%";

        //get register filtered
        $filteredCount = DetalleOrden::getFilteredData($search)->count();
        $detalleorden = DetalleOrden::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        //esto es para reutilizar la funcion para generar datatable en functions.js
        $detalleorden = $detalleorden->map(function ($detalleorden) {
            $detalleorden->path = 'orderDetails';//sirve para la url de editar y eliminar
            return $detalleorden;
        });
        //se retorna una array estructurado para el data table
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => DetalleOrden::count(),
            'recordsFiltered' => $filteredCount,
            'data' => $detalleorden]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $detalleorden=DetalleOrden::where('codigo',$id)->first();
        return view('detalle/update')->with([
            'detalleorden' => $detalleorden
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validacion=$this->ValidarCampos($request);
        if($validacion -> fails()){
            return response()->json([
                'code' => 422,
                'message' => $validacion->messages()
            ],422);
        }else{
            $menu=DetalleOrden::find($id);
            if($menu){
                $menu->update([
                    'orden_id' => $request->orden_id,
                    'menu_id' => $request->menu_id,
                    'cantidad' => $request->cantidad,
                    'subtotal' => $request->subtotal
                ]);
                return response()->json([
                    'code' => 200,
                    'message' => "Registro actualizado"
                ],200);
            }else{
                return response()->json([
                    'code' => 400,
                    'message' => "Registro no encontrado"
                ],400);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu=DetalleOrden::find($id);
        if($menu){
            $menu->delete();
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
