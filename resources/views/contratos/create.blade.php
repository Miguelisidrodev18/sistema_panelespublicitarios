@extends('layouts.app')

@section('title', 'Nuevo Contrato')
@section('subtitle', 'Registrar un nuevo contrato')

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('contratos.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Nuevo Contrato</div>
    </div>
</div>

<form action="{{ route('contratos.store') }}" method="POST">
    @csrf

    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-file-earmark-text"></i>Datos del contrato</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">N° Contrato <span class="req">*</span></label>
                    <input type="text" name="numero_contrato" value="{{ old('numero_contrato') }}"
                        class="form-control @error('numero_contrato') is-invalid @enderror" required>
                    @error('numero_contrato')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de contrato <span class="req">*</span></label>
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
    </div>

    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-person"></i>Datos del contratante</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre / Razón social <span class="req">*</span></label>
                    <input type="text" name="contratante" value="{{ old('contratante') }}"
                        class="form-control @error('contratante') is-invalid @enderror" required>
                    @error('contratante')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo doc.</label>
                    <select name="doc_tipo" class="form-select">
                        <option value="">—</option>
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
    </div>

    <div class="card">
        <div class="card-header">
            <span><i class="bi bi-currency-dollar"></i>Condiciones económicas</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Monto total (S/.) <span class="req">*</span></label>
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
                    <input type="text" id="saldo_display" class="form-control" style="background:#F8FAFC" readonly>
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
    </div>

    <div class="card">
        <div class="card-body">
            <label class="form-label">Descripción / Observaciones</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="background:#fff;color:var(--text-dark);border-bottom:1px solid var(--border)">
            <label class="toggle-switch" style="margin:0">
                <input type="checkbox" name="generar_cuotas" id="generar_cuotas" value="1"
                    {{ old('generar_cuotas') ? 'checked' : '' }} onchange="toggleCuotas()">
                <span class="fw-600">Generar cuotas de cobranza automáticamente</span>
            </label>
        </div>
        <div class="card-body" id="seccionCuotas" style="{{ old('generar_cuotas') ? '' : 'display:none' }}">
            <div class="row g-3">
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
                    <input type="text" id="monto_cuota_display" class="form-control" style="background:#F8FAFC" readonly
                        placeholder="Se calcula del saldo pendiente">
                    <div class="form-hint">Saldo pendiente ÷ N° cuotas. Las fechas respetan la frecuencia de cobro elegida.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="action-bar">
        <a href="{{ route('contratos.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i>Guardar Contrato
        </button>
    </div>
</form>

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
