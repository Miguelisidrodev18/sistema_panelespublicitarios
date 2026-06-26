@extends('layouts.app')

@section('title', 'Nuevo Almacén')
@section('subtitle', 'Registrar equipo o material')

@section('content')
<div class="form-card" style="max-width:700px">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('almacen-items.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Nuevo Almacén</div>
    </div>
</div>

<form action="{{ route('almacen-items.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header"><span><i class="bi bi-box-seam"></i>Datos del almacén</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre de equipo <span class="req">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Maquina de Soldar" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control" placeholder="Ej: ALM-01">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Marca</label>
                    <input type="text" name="marca" value="{{ old('marca') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Serie</label>
                    <input type="text" name="serie" value="{{ old('serie') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipo <span class="req">*</span></label>
                    <select name="tipo" id="tipo-select" class="form-select @error('tipo') is-invalid @enderror" required>
                        <option value="">Seleccionar tipo</option>
                        @foreach(\App\Models\AlmacenItem::TIPOS as $key => $label)
                            <option value="{{ $key }}" {{ old('tipo') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Año de compra</label>
                    <input type="number" name="anio_compra" value="{{ old('anio_compra') }}" class="form-control" min="1900" max="{{ date('Y') + 1 }}">
                </div>
                <div class="col-md-6" id="unidad-medida-group" style="{{ old('tipo') === 'materiales' ? '' : 'display:none' }}">
                    <label class="form-label">Unidad de medida</label>
                    <select name="unidad_medida" class="form-select">
                        @foreach(\App\Models\AlmacenItem::UNIDADES as $key => $label)
                            <option value="{{ $key }}" {{ old('unidad_medida', 'unidad') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Almacén <span class="req">*</span></label>
                    <select name="almacen_id" class="form-select @error('almacen_id') is-invalid @enderror" required>
                        <option value="">Seleccionar almacén</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ old('almacen_id') == $almacen->id ? 'selected' : '' }}>{{ $almacen->nombre }}</option>
                        @endforeach
                    </select>
                    @error('almacen_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Proveedor</label>
                    <select name="proveedor_id" class="form-select">
                        <option value="">Sin proveedor</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}" {{ old('proveedor_id') == $prov->id ? 'selected' : '' }}>{{ $prov->razon_social }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="activo" selected>Activo</option>
                        <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="action-bar">
        <a href="{{ route('almacen-items.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Registrar Almacén</button>
    </div>
</form>
</div>

<script>
document.getElementById('tipo-select').addEventListener('change', function() {
    document.getElementById('unidad-medida-group').style.display = this.value === 'materiales' ? '' : 'none';
});
</script>
@endsection
