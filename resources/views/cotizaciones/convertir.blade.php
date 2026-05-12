@extends('layouts.app')

@section('title', 'Convertir a Contrato')
@section('subtitle', $cotizacion->numero . ' — ' . $cotizacion->cliente_nombre)

@section('content')
<div class="form-card" style="max-width:900px">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="page-title">Convertir cotización a contrato</div>
            <div style="font-size:13px;color:var(--text-light);margin-top:2px">{{ $cotizacion->numero }} — {{ $cotizacion->cliente_nombre }}</div>
        </div>
    </div>
</div>

<div class="card card-accent" style="border-color:#93C5FD;margin-bottom:16px">
    <div class="card-body" style="background:#EFF6FF;display:flex;align-items:center;gap:10px">
        <i class="bi bi-info-circle-fill" style="font-size:20px;color:#2563EB"></i>
        <div style="font-size:13px;color:#1E40AF;font-weight:500">Los datos se prelllenan con la cotización. Verificá y completá los campos requeridos antes de confirmar.</div>
    </div>
</div>

@if($errors->any())
<div class="card card-accent" style="border-color:#FCA5A5;margin-bottom:16px">
    <div class="card-body" style="background:#FEF2F2;color:var(--primary);font-size:13px">
        <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
</div>
@endif

<form action="{{ route('cotizaciones.guardar-contrato', $cotizacion) }}" method="POST">
    @csrf

    <div class="card">
        <div class="card-header"><span><i class="bi bi-file-earmark-text"></i>Datos del contrato</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">N° Contrato <span class="req">*</span></label>
                    <input type="text" name="numero_contrato" value="{{ old('numero_contrato', $numero) }}" class="form-control @error('numero_contrato') is-invalid @enderror" required>
                    @error('numero_contrato')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de contrato <span class="req">*</span></label>
                    <input type="text" name="tipo_contrato" value="{{ old('tipo_contrato', $cotizacion->tipo_contrato) }}" class="form-control" list="tipos_contrato" required>
                    <datalist id="tipos_contrato"><option value="Panel Digital"><option value="Panel Tradicional"><option value="Marketing Digital"><option value="Mixto"></datalist>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Empresa (opcional)</label>
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

    <div class="card">
        <div class="card-header"><span><i class="bi bi-person"></i>Datos del contratante</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre / Razón social <span class="req">*</span></label>
                    <input type="text" name="contratante" value="{{ old('contratante', $cotizacion->cliente_empresa ?: $cotizacion->cliente_nombre) }}" class="form-control @error('contratante') is-invalid @enderror" required>
                    @error('contratante')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo doc.</label>
                    <select name="doc_tipo" class="form-select"><option value="">—</option><option value="RUC">RUC</option><option value="CI">CI</option><option value="Pasaporte">Pasaporte</option></select>
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
        <div class="card-header"><span><i class="bi bi-currency-dollar"></i>Condiciones económicas</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Monto total (S/.) <span class="req">*</span></label>
                    <input type="number" name="monto_total" id="monto_total" value="{{ old('monto_total', $cotizacion->monto_propuesto) }}" class="form-control" step="1" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Adelanto (S/.)</label>
                    <input type="number" name="adelanto" id="adelanto" value="{{ old('adelanto', 0) }}" class="form-control" step="1" min="0">
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
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <label class="form-label">Descripción / Observaciones</label>
            <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $cotizacion->notas) }}</textarea>
        </div>
    </div>

    <div class="action-bar">
        <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-arrow-right-circle"></i>Crear Contrato</button>
    </div>
</form>
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
