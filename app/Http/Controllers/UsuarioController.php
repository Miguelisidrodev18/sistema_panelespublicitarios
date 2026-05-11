<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('empresa')->orderBy('nombre_completo')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $empresas = Empresa::activas()->orderBy('nombre')->get();
        $permisos_disponibles = $this->getPermisosDisponibles();
        return view('usuarios.create', compact('empresas', 'permisos_disponibles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:usuarios',
            'password' => 'required|string|min:6|confirmed',
            'email' => 'nullable|email|max:100',
            'nombre_completo' => 'required|string|max:200',
            'rol' => 'required|in:admin,empresa',
            'empresa_id' => 'nullable|exists:empresas,id',
            'permisos' => 'nullable|array',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['permisos'] = $request->permisos ?? [];

        Usuario::create($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Usuario $usuario)
    {
        $empresas = Empresa::activas()->orderBy('nombre')->get();
        $permisos_disponibles = $this->getPermisosDisponibles();
        return view('usuarios.edit', compact('usuario', 'empresas', 'permisos_disponibles'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'email' => 'nullable|email|max:100',
            'nombre_completo' => 'required|string|max:200',
            'rol' => 'required|in:admin,empresa',
            'empresa_id' => 'nullable|exists:empresas,id',
            'activo' => 'boolean',
            'permisos' => 'nullable|array',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }

        $validated['permisos'] = $request->permisos ?? [];

        $usuario->update($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update(['activo' => false]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario desactivado.');
    }

    private function getPermisosDisponibles(): array
    {
        return [
            'empresas' => 'Gestión de Empresas',
            'cobranzas' => 'Cobranzas',
            'ingresos' => 'Ingresos',
            'egresos' => 'Egresos',
            'paneles_digitales' => 'Paneles Digitales',
            'paneles_tradicionales' => 'Paneles Tradicionales',
            'deudas' => 'Deudas',
            'contratos' => 'Contratos',
            'cotizaciones' => 'Cotizaciones',
            'control_publicitario' => 'Control Publicitario',
            'reportes' => 'Reportes',
            'documentos' => 'Documentos',
            'carpetas' => 'Carpetas/Archivos',
            'almacenes' => 'Almacenes',
            'calendario' => 'Calendario',
            'usuarios' => 'Gestión de Usuarios',
        ];
    }
}
