<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('usuarios.show');
    }

    public function create()
    {
        $roles = Rol::all();
        return view('usuarios.create', ['roles' => $roles]); // Corregido: 'roles' en lugar de 'users'
    }

    protected function validateUser(Request $request, $ignoreId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email'.($ignoreId ? ",$ignoreId" : ''),
            'rol' => 'required|exists:roles,codigo',
            'password' => $ignoreId ? 'nullable|min:8' : 'required|min:8'
        ];

        $messages = [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.unique' => 'Este correo ya está registrado',
            'rol.required' => 'El rol es obligatorio',
            'rol.exists' => 'El rol seleccionado no es válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    public function store(Request $request)
    {
        $validator = $this->validateUser($request);
        
        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'Usuario creado correctamente',
            'data' => $user
        ]);
    }

    public function show(Request $request)
    {
        $query = User::with('rol')
            ->select('users.*');

        // Búsqueda
        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhereHas('rol', function($q) use ($search) {
                      $q->where('nombre', 'like', "%$search%");
                  });
            });
        }

        // Ordenamiento
        if ($order = $request->input('order.0')) {
            $column = $request->input("columns.{$order['column']}.data", 'id');
            $query->orderBy($column, $order['dir']);
        }

        $total = $query->count();
        $users = $query->skip($request->input('start', 0))
                      ->take($request->input('length', 10))
                      ->get();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => User::count(),
            'recordsFiltered' => $total,
            'data' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol->nombre ?? 'Sin rol',
                    'path' => 'user',
                    'DT_RowId' => 'row_'.$user->id
                ];
            })
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Rol::all();
        return view('usuarios.update', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = $this->validateUser($request, $id);
        
        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'rol' => $request->rol
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'code' => 200,
            'message' => 'Usuario actualizado correctamente'
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return response()->json([
            'code' => 200,
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
}