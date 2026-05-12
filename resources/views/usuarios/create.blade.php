@extends('layouts.app')

@section('title', 'Nuevo Usuario')
@section('subtitle', 'Crear cuenta de acceso')

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('usuarios.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Nuevo Usuario</div>
    </div>
</div>

<form action="{{ route('usuarios.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header"><span><i class="bi bi-key"></i>Datos de acceso</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Usuario <span class="req">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" class="form-control @error('username') is-invalid @enderror" required>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contraseña <span class="req">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirmar contraseña <span class="req">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span><i class="bi bi-person"></i>Perfil</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre completo <span class="req">*</span></label>
                    <input type="text" name="nombre_completo" value="{{ old('nombre_completo') }}" class="form-control @error('nombre_completo') is-invalid @enderror" required>
                    @error('nombre_completo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rol <span class="req">*</span></label>
                    <select name="rol" class="form-select" id="rolSelect" required>
                        <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="empresa" {{ old('rol') === 'empresa' ? 'selected' : '' }}>Empresa</option>
                    </select>
                </div>
                <div class="col-12" id="empresaField" style="{{ old('rol') === 'empresa' ? '' : 'display:none' }}">
                    <label class="form-label">Empresa vinculada</label>
                    <select name="empresa_id" class="form-select">
                        <option value="">Sin empresa</option>
                        @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="permisosCard" style="{{ old('rol') === 'admin' ? 'display:none' : '' }}">
        <div class="card-header">
            <span><i class="bi bi-shield-check"></i>Permisos</span>
            <button type="button" class="btn btn-sm btn-secondary" onclick="toggleAll(this)">Seleccionar todo</button>
        </div>
        <div class="card-body">
            <div class="perm-grid">
                @foreach($permisos_disponibles as $key => $label)
                <label class="perm-chip">
                    <input type="checkbox" name="permisos[]" value="{{ $key }}" class="permiso-check"
                        {{ in_array($key, old('permisos', [])) ? 'checked' : '' }}>
                    {{ $label }}
                </label>
                @endforeach
            </div>
        </div>
    </div>

    <div class="action-bar">
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Crear Usuario</button>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('rolSelect').addEventListener('change', function () {
    const isEmpresa = this.value === 'empresa';
    document.getElementById('empresaField').style.display = isEmpresa ? '' : 'none';
    document.getElementById('permisosCard').style.display = isEmpresa ? '' : 'none';
});

function toggleAll(btn) {
    const checks = document.querySelectorAll('.permiso-check');
    const allChecked = Array.from(checks).every(c => c.checked);
    checks.forEach(c => c.checked = !allChecked);
    btn.textContent = allChecked ? 'Seleccionar todo' : 'Deseleccionar todo';
}
</script>
@endpush
