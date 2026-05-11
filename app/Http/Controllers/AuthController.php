<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = \App\Models\Usuario::where('username', $credentials['username'])
            ->where('activo', true)
            ->first();

        if ($user && Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            $user->update(['ultimo_acceso' => now()]);
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['username' => 'Credenciales incorrectas.'])->onlyInput('username');
    }

    public function verificarClaveMaestra(Request $request)
    {
        $ok = $request->clave_maestra !== null
            && config('app.master_password') !== null
            && $request->clave_maestra === config('app.master_password');

        return response()->json(['ok' => $ok]);
    }

    public function registroMaestro(Request $request)
    {
        $request->validate([
            'clave_maestra'   => 'required|string',
            'username'        => 'required|string|max:50|unique:usuarios,username',
            'nombre_completo' => 'required|string|max:100',
            'password'        => 'required|string|min:6|confirmed',
        ], [
            'username.unique'        => 'Ese nombre de usuario ya existe.',
            'password.confirmed'     => 'Las contraseñas no coinciden.',
            'password.min'           => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        if ($request->clave_maestra !== config('app.master_password')) {
            return back()->withErrors(['clave_maestra' => 'Contraseña maestra incorrecta.'])->withInput($request->except('password', 'password_confirmation', 'clave_maestra'));
        }

        \App\Models\Usuario::create([
            'username'        => $request->username,
            'nombre_completo' => $request->nombre_completo,
            'password'        => Hash::make($request->password),
            'rol'             => 'admin',
            'activo'          => true,
        ]);

        return redirect()->route('login')->with('success', 'Usuario administrador creado. Ya puedes iniciar sesión.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
