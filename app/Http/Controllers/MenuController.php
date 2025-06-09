<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Categoria;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('menus/show');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categoria = Categoria::all();//extrayendo marcas
        return view('menus/create')->with(['categoria'=>$categoria]);
    }

    public function ValidarCampos($request) {
        return Validator::make($request->all(),[
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required',
            'category' => 'required'
        ], [
            'nombre.required' => 'Nombre es obligatorio',
            'descripcion.required' => 'Descripción es obligatoria',
            'precio.required' => 'Precio es obligatorio',
            'category.required' => 'Categoría es obligatoria es obligatorio'
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
            $menu= Menu::create($request->all());
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
            $itemsPerPage =  Menu::count();
            $skip = 0;
        }

        //config to ordering
        $sortBy = $request->input('columns.'.$request->input('order.0.column').'.data','codigo');
        $sort = ($request->input('order.0.dir') === 'asc') ? 'asc' : 'desc';

        //config to search
        $search = $request->input('search.value', '');
        $search = "%$search%";

        //get register filtered
        $filteredCount = Menu::getFilteredData($search)->count();
        $menu = Menu::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        //esto es para reutilizar la funcion para generar datatable en functions.js
        $menu = $menu->map(function ($menu) {
            $menu->path = 'menu';//sirve para la url de editar y eliminar
            return $menu;
        });
        //se retorna una array estructurado para el data table
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => Menu::count(),
            'recordsFiltered' => $filteredCount,
            'data' => $menu]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categoria = Categoria::all();//extrayendo marcas
        $menu=Menu::where('codigo',$id)->first();
        return view('menus/update')->with([
            'menu' => $menu,
            'categoria' => $categoria
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
            $menu=Menu::find($id);
            if($menu){
                $menu->update([
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'precio' => $request->precio,
                    'category' => $request->category
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
        $menu=Menu::find($id);
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
