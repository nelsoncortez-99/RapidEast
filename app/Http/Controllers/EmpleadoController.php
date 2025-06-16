<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('empleados/show');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Rol::all();
        return view('employee/create')->with(['roles'=>$roles]);
    }

    public function ValidarCampos($request) {
        return Validator::make($request->all(), [
        // Datos empleado
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20',
        
        // Datos usuario
        'name' => 'required|string|max:255|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'rol' => 'required|exists:roles,codigo'
    ], [
        'name.unique' => 'El nombre de usuario ya está en uso',
        'email.unique' => 'El correo electrónico ya está registrado',
        'rol.exists' => 'El rol seleccionado no es válido'
    ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'nombre' => 'required',
        'apellido' => 'required',
        'telefono' => 'nullable',
        'username' => 'required|unique:users,name',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
        'rol' => 'required|exists:roles,id'
    ]);

    // Crear usuario
    $user = User::create([
        'name' => $validated['username'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'rol' => $validated['rol']
    ]);

    // Crear empleado
    $empleado = Empleado::create([
        'nombre' => $validated['nombre'],
        'apellido' => $validated['apellido'],
        'telefono' => $validated['telefono'],
        'user' => $user->id
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Empleado registrado correctamente'
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
{
    $request->validate([
        'draw' => 'required|integer',
        'start' => 'integer|min:0',
        'length' => 'integer|min:-1',
    ]);

    $itemsPerPage = $request->input('length', 10);
    $skip = $request->input('start', 0);

    if ($itemsPerPage == -1) {
        $itemsPerPage = Empleado::count();
        $skip = 0;
    }

    $sortBy = $request->input('columns.'.$request->input('order.0.column').'.data', 'codigo');
    $sort = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';

    // Configuración de búsqueda
    $search = $request->input('search.value', '');
    $searchTerm = "%$search%";

    // Obtención de datos
    $query = Empleado::select('empleados.*', 'users.name as usuario_nombre')
        ->leftJoin('users', 'users.id', '=', 'empleados.user');
    
    // Aplicar búsqueda si existe término
    if (!empty($search)) {
        $query->where(function($q) use ($searchTerm) {
            $q->where('codigo', 'like', $searchTerm)
                ->orWhere('nombre', 'like', $searchTerm)
                ->orWhere('apellido', 'like', $searchTerm)
                ->orWhere('telefono', 'like', $searchTerm)
                ->orWhereHas('user', function($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm);
                });
        });
    }

     // Conteo y paginación
    $filteredCount = $query->count();
    
    $empleados = $query->orderBy($sortBy, $sort)
        ->skip($skip)
        ->take($itemsPerPage)
        ->get();

    $data = $empleados->map(function ($empleado) {
        return [
            'codigo' => $empleado->codigo,
            'nombre' => $empleado->nombre,
            'apellido' => $empleado->apellido,
            'telefono' => $empleado->telefono,
            'user' => $empleado->usuario_nombre ?? 'Sin usuario', // Usa directamente el alias del join
            'path' => 'employee',
            'DT_RowId' => 'row_'.$empleado->codigo
        ];
    });

    return response()->json([
        'draw' => intval($request->input('draw')),
        'recordsTotal' => Empleado::count(),
        'recordsFiltered' => $filteredCount,
        'data' => $data
    ]);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $empleado=Empleado::where('codigo',$id)->first();
        return view('empleados/update')->with(['empleado' => $empleado]);
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
            $empleado=Empleado::find($id);
            if($empleado){
                $empleado->update([
                    'nombre' => $request->nombre,
                    'apellido' => $request->apellido,
                    'telefono' => $request->telefono,
                    'user' => $request->user
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
        $empleado=Empleado::find($id);
        if($empleado){
            $empleado->delete();
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
