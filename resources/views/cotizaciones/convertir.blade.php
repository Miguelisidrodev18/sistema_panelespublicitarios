@extends('layouts.app')

@section('title', 'Convertir a Contrato')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="mb-0 fw-semibold">Convertir cotización a contrato</h5>
        <div class="small text-muted">{{ $cotizacion->numero }} — {{ $cotizacion->cliente_nombre }}</div>
    </div>
</div>

<div class="alert alert-info d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-info-circle-fill fs-5"></i>
    <div>Los datos se prelllenan con la cotización. Verificá y completá los campos requeridos antes de confirmar.</div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('cotizaciones.guardar-contrato', $cotizacion) }}" method="POST">
    @csrf

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Datos del contrato</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">N° Contrato <span class="text-danger">*</span></label>
                <input type="text" name="numero_contrato" value="{{ old('numero_contrato', $numero) }}"
                    class="form-control @error('numero_contrato') is-invalid @enderror" required>
                @error('numero_contrato')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipo de contrato <span class="text-danger">*</span></label>
                <input type="text" name="tipo_contrato"
                    value="{{ old('tipo_contrato', $cotizacion->tipo_contrato) }}"
                    class="form-control" list="tipos_contrato" required>
                <datalist id="tipos_contrato">
                    <option value="Panel Digital">
                    <option value="Panel Tradicional">
                    <option value="Marketing Digital">
                    <option value="Mixto">
                </datalist>
            </div>
            <div class="col-md-4">
                <label class="form-label">Empresa (opcional)</label>
                <select name="empresa_id" class="form-select">
                    <option value="">Sin empresa</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
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
                <input type="text" name="contratante"
                    value="{{ old('contratante', $cotizacion->cliente_empresa ?: $cotizacion->cliente_nombre) }}"
                    class="form-control @error('contratante') is-invalid @enderror" required>
                @error('contratante')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipo doc.</label>
                <select name="doc_tipo" class="form-select">
                    <option value="">-</option>
                    <option value="RUC">RUC</option>
                    <option value="CI">CI</option>
                    <option value="Pasaporte">Pasaporte</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">N° documento</label>
                <input type="text" name="doc_numero" value="{{ old('doc_numero') }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion') }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Condiciones económicas</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">Monto total (S/.) <span class="text-danger">*</span></label>
                <input type="number" name="monto_total" id="monto_total"
                    value="{{ old('monto_total', $cotizacion->monto_propuesto) }}"
                    class="form-control" step="1" min="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Adelanto (S/.)</label>
                <input type="number" name="adelanto" id="adelanto" value="{{ old('adelanto', 0) }}"
                    class="form-control" step="1" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Saldo pendiente</label>
                <input type="text" id="saldo_display" class="form-control bg-light" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha inicio</label>
                <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha fin</label>
                <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label">Descripción / Observaciones</label>
            <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $cotizacion->notas) }}</textarea>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">
            <i class="bi bi-arrow-right-circle me-1"></i>Crear Contrato
        </button>
    </div>
</form>

</div>
</div>
@endsection

@push('scripts')
<script>
function calcularSaldo() {
    const total = parseFloat(document.getElementById('monto_total').value) || 0;
    const adelanto = parseFloat(document.getElementById('adelanto').value) || 0;
    document.getElementById('saldo_display').value = 'S/. ' + (total - adelanto).toLocaleString('es');
}
document.getElementById('monto_total').addEventListener('input', calcularSaldo);
document.getElementById('adelanto').addEventListener('input', calcularSaldo);
calcularSaldo();
</script>
@endpush
