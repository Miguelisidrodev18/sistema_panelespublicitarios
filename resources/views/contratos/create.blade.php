@extends('layouts.app')

@section('title', 'Nuevo Contrato')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('contratos.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Nuevo Contrato</h5>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('contratos.store') }}" method="POST">
    @csrf

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Datos del contrato</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">N° Contrato <span class="text-danger">*</span></label>
                <input type="text" name="numero_contrato" value="{{ old('numero_contrato') }}"
                    class="form-control @error('numero_contrato') is-invalid @enderror" required>
                @error('numero_contrato')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipo de contrato <span class="text-danger">*</span></label>
                <input type="text" name="tipo_contrato" value="{{ old('tipo_contrato') }}"
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
                <input type="text" name="contratante" value="{{ old('contratante') }}"
                    class="form-control @error('contratante') is-invalid @enderror" required>
                @error('contratante')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipo doc.</label>
                <select name="doc_tipo" class="form-select">
                    <option value="">-</option>
                    <option value="RUC" {{ old('doc_tipo') === 'RUC' ? 'selected' : '' }}>RUC</option>
                    <option value="CI" {{ old('doc_tipo') === 'CI' ? 'selected' : '' }}>CI</option>
                    <option value="Pasaporte" {{ old('doc_tipo') === 'Pasaporte' ? 'selected' : '' }}>Pasaporte</option>
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
                <input type="number" name="monto_total" id="monto_total" value="{{ old('monto_total', 0) }}"
                    class="form-control @error('monto_total') is-invalid @enderror" step="1" min="0" required>
                @error('monto_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            <div class="col-md-4">
                <label class="form-label">Frecuencia de cobro</label>
                <select name="frecuencia_cobro" class="form-select">
                    <option value="mensual" {{ old('frecuencia_cobro', 'mensual') === 'mensual' ? 'selected' : '' }}>Mensual</option>
                    <option value="bimestral" {{ old('frecuencia_cobro') === 'bimestral' ? 'selected' : '' }}>Bimestral (c/2 meses)</option>
                    <option value="trimestral" {{ old('frecuencia_cobro') === 'trimestral' ? 'selected' : '' }}>Trimestral (c/3 meses)</option>
                    <option value="semestral" {{ old('frecuencia_cobro') === 'semestral' ? 'selected' : '' }}>Semestral (c/6 meses)</option>
                    <option value="anual" {{ old('frecuencia_cobro') === 'anual' ? 'selected' : '' }}>Anual</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label">Descripción / Observaciones</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-3 d-flex align-items-center gap-2">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="generar_cuotas" id="generar_cuotas" value="1"
                    {{ old('generar_cuotas') ? 'checked' : '' }} onchange="toggleCuotas()">
                <label class="form-check-label fw-medium" for="generar_cuotas">
                    Generar cuotas de cobranza automáticamente
                </label>
            </div>
        </div>
        <div class="card-body row g-3" id="seccionCuotas" style="{{ old('generar_cuotas') ? '' : 'display:none' }}">
            <div class="col-md-3">
                <label class="form-label">N° de cuotas</label>
                <input type="number" name="num_cuotas" id="num_cuotas" value="{{ old('num_cuotas', 1) }}"
                    class="form-control" min="1" max="60" onchange="calcularCuota()">
            </div>
            <div class="col-md-4">
                <label class="form-label">Primera fecha de vencimiento</label>
                <input type="date" name="primera_fecha" value="{{ old('primera_fecha') }}" class="form-control">
            </div>
            <div class="col-md-5">
                <label class="form-label">Monto estimado por cuota</label>
                <input type="text" id="monto_cuota_display" class="form-control bg-light" readonly
                    placeholder="Se calcula del saldo pendiente">
                <div class="form-text">Saldo pendiente ÷ N° cuotas. Las fechas respetan la frecuencia de cobro elegida.</div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-check-lg me-1"></i>Guardar Contrato
        </button>
    </div>
</form>

</div>
</div>
@endsection

@push('scripts')
<script>
function getSaldo() {
    const total = parseFloat(document.getElementById('monto_total').value) || 0;
    const adelanto = parseFloat(document.getElementById('adelanto').value) || 0;
    return total - adelanto;
}
function calcularSaldo() {
    const saldo = getSaldo();
    document.getElementById('saldo_display').value = 'S/. ' + saldo.toLocaleString('es');
    calcularCuota();
}
function calcularCuota() {
    const saldo = getSaldo();
    const n = parseInt(document.getElementById('num_cuotas')?.value) || 1;
    const cuota = n > 0 ? Math.round(saldo / n) : 0;
    const el = document.getElementById('monto_cuota_display');
    if (el) el.value = 'S/. ' + cuota.toLocaleString('es');
}
function toggleCuotas() {
    const checked = document.getElementById('generar_cuotas').checked;
    document.getElementById('seccionCuotas').style.display = checked ? '' : 'none';
    if (checked) calcularCuota();
}
document.getElementById('monto_total').addEventListener('input', calcularSaldo);
document.getElementById('adelanto').addEventListener('input', calcularSaldo);
calcularSaldo();
</script>
@endpush
