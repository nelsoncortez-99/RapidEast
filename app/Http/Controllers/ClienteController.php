<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clientes/show');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes/create');
    }

    public function ValidarCampos($request) {
        return Validator::make($request->all(),[
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required'
        ], [
            'nombre.required' => 'Nombre es obligatorio',
            'apellido.required' => 'Apellido es obligatorio',
            'correo.required' => 'Correo es obligatorio'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validacion = $this->ValidarCampos($request);

    if ($validacion->fails()) {
        return response()->json([
            'code' => 422,
            'message' => $validacion->messages()
        ], 422);
    } else {
        $cliente = Cliente::create($request->all());

        return response()->json([
            'code' => 200,
            'message' => "Se creó el registro correctamente",
            'cliente' => [
                'codigo' => $cliente->codigo,
                'nombre' => $cliente->nombre,
                'apellido' => $cliente->apellido
            ]
        ], 200);
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
            $itemsPerPage =  Cliente::count();
            $skip = 0;
        }

        //config to ordering
        $sortBy = $request->input('columns.'.$request->input('order.0.column').'.data','codigo');
        $sort = ($request->input('order.0.dir') === 'asc') ? 'asc' : 'desc';

        //config to search
        $search = $request->input('search.value', '');
        $search = "%$search%";

        //get register filtered
        $filteredCount = Cliente::getFilteredData($search)->count();
        $cliente = Cliente::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        //esto es para reutilizar la funcion para generar datatable en functions.js
        $cliente = $cliente->map(function ($cliente) {
            $cliente->path = 'client';//sirve para la url de editar y eliminar
            return $cliente;
        });
        //se retorna una array estructurado para el data table
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => Cliente::count(),
            'recordsFiltered' => $filteredCount,
            'data' => $cliente]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cliente=Cliente::where('codigo',$id)->first();
        return view('clientes/update')->with(['cliente' => $cliente]);
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
            $cliente=Cliente::find($id);
            if($cliente){
                $cliente->update([
                    'nombre' => $request->nombre,
                    'apellido' => $request->apellido,
                    'correo' => $request->correo
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
        $cliente=Cliente::find($id);
        if($cliente){
            $cliente->delete();
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
