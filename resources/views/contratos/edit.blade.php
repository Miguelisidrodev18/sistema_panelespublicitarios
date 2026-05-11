@extends('layouts.app')

@section('title', 'Editar Contrato ' . $contrato->numero_contrato)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('contratos.show', $contrato) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Editar Contrato {{ $contrato->numero_contrato }}</h5>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('contratos.update', $contrato) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Datos del contrato</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">N° Contrato</label>
                <input type="text" class="form-control bg-light" value="{{ $contrato->numero_contrato }}" readonly>
                <div class="form-text">El número de contrato no se puede cambiar.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipo de contrato <span class="text-danger">*</span></label>
                <input type="text" name="tipo_contrato" value="{{ old('tipo_contrato', $contrato->tipo_contrato) }}"
                    class="form-control @error('tipo_contrato') is-invalid @enderror"
                    list="tipos_contrato" required>
                <datalist id="tipos_contrato">
                    <option value="Panel Digital">
                    <option value="Panel Tradicional">
                    <option value="Marketing Digital">
                    <option value="Mixto">
                </datalist>
                @error('tipo_contrato')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="activo" {{ old('estado', $contrato->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="finalizado" {{ old('estado', $contrato->estado) === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                    <option value="cancelado" {{ old('estado', $contrato->estado) === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label">Empresa (opcional)</label>
                <select name="empresa_id" class="form-select">
                    <option value="">Sin empresa</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}"
                            {{ old('empresa_id', $contrato->empresa_id) == $empresa->id ? 'selected' : '' }}>
                            {{ $empresa->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Datos del contratante</div>
        <div class="card-body row g-3">
            <div class="col-md-8">
                <label class="form-label">Nombre / Razón social <span class="text-danger">*</span></label>
                <input type="text" name="contratante" value="{{ old('contratante', $contrato->contratante) }}"
                    class="form-control @error('contratante') is-invalid @enderror" required>
                @error('contratante')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipo doc.</label>
                <select name="doc_tipo" class="form-select">
                    <option value="">-</option>
                    <option value="RUC" {{ old('doc_tipo', $contrato->doc_tipo) === 'RUC' ? 'selected' : '' }}>RUC</option>
                    <option value="CI" {{ old('doc_tipo', $contrato->doc_tipo) === 'CI' ? 'selected' : '' }}>CI</option>
                    <option value="Pasaporte" {{ old('doc_tipo', $contrato->doc_tipo) === 'Pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">N° documento</label>
                <input type="text" name="doc_numero" value="{{ old('doc_numero', $contrato->doc_numero) }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $contrato->direccion) }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Condiciones económicas</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">Monto total (S/.) <span class="text-danger">*</span></label>
                <input type="number" name="monto_total" id="monto_total"
                    value="{{ old('monto_total', $contrato->monto_total) }}"
                    class="form-control @error('monto_total') is-invalid @enderror" step="1" min="0" required>
                @error('monto_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Adelanto (S/.)</label>
                <input type="number" name="adelanto" id="adelanto"
                    value="{{ old('adelanto', $contrato->adelanto) }}"
                    class="form-control" step="1" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Saldo pendiente actual</label>
                <input type="text" class="form-control bg-light"
                    value="S/. {{ number_format($contrato->saldo_pendiente, 0, ',', '.') }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha inicio</label>
                <input type="date" name="fecha_inicio"
                    value="{{ old('fecha_inicio', $contrato->fecha_inicio?->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha fin</label>
                <input type="date" name="fecha_fin"
                    value="{{ old('fecha_fin', $contrato->fecha_fin?->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Frecuencia de cobro</label>
                <select name="frecuencia_cobro" class="form-select">
                    @foreach(['mensual' => 'Mensual', 'bimestral' => 'Bimestral (c/2 meses)', 'trimestral' => 'Trimestral (c/3 meses)', 'semestral' => 'Semestral (c/6 meses)', 'anual' => 'Anual'] as $val => $label)
                        <option value="{{ $val }}" {{ old('frecuencia_cobro', $contrato->frecuencia_cobro ?? 'mensual') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label">Descripción / Observaciones</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $contrato->descripcion) }}</textarea>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('contratos.show', $contrato) }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>Guardar cambios
        </button>
    </div>
</form>

</div>
</div>
@endsection
