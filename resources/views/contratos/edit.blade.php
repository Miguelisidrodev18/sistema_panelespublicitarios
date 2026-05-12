@extends('layouts.app')

@section('title', 'Editar Contrato ' . $contrato->numero_contrato)
@section('subtitle', 'Modificar datos del contrato')

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('contratos.show', $contrato) }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Editar {{ $contrato->numero_contrato }}</div>
    </div>
</div>

<form action="{{ route('contratos.update', $contrato) }}" method="POST">
    @csrf @method('PUT')

    <div class="card">
        <div class="card-header"><span><i class="bi bi-file-earmark-text"></i>Datos del contrato</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">N° Contrato</label>
                    <input type="text" class="form-control" style="background:#F8FAFC" value="{{ $contrato->numero_contrato }}" readonly>
                    <div class="form-hint">No se puede cambiar.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de contrato <span class="req">*</span></label>
                    <input type="text" name="tipo_contrato" value="{{ old('tipo_contrato', $contrato->tipo_contrato) }}"
                        class="form-control" list="tipos_contrato" required>
                    <datalist id="tipos_contrato">
                        <option value="Panel Digital"><option value="Panel Tradicional">
                        <option value="Marketing Digital"><option value="Mixto">
                    </datalist>
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
                            <option value="{{ $empresa->id }}" {{ old('empresa_id', $contrato->empresa_id) == $empresa->id ? 'selected' : '' }}>{{ $empresa->nombre }}</option>
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
                    <input type="text" name="contratante" value="{{ old('contratante', $contrato->contratante) }}" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo doc.</label>
                    <select name="doc_tipo" class="form-select">
                        <option value="">—</option>
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
    </div>

    <div class="card">
        <div class="card-header"><span><i class="bi bi-currency-dollar"></i>Condiciones económicas</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Monto total (S/.) <span class="req">*</span></label>
                    <input type="number" name="monto_total" value="{{ old('monto_total', $contrato->monto_total) }}" class="form-control" step="1" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Adelanto (S/.)</label>
                    <input type="number" name="adelanto" value="{{ old('adelanto', $contrato->adelanto) }}" class="form-control" step="1" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Saldo pendiente</label>
                    <input type="text" class="form-control" style="background:#F8FAFC" value="S/. {{ number_format($contrato->saldo_pendiente, 0, ',', '.') }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $contrato->fecha_inicio?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha fin</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $contrato->fecha_fin?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frecuencia de cobro</label>
                    <select name="frecuencia_cobro" class="form-select">
                        @foreach(['mensual'=>'Mensual','bimestral'=>'Bimestral','trimestral'=>'Trimestral','semestral'=>'Semestral','anual'=>'Anual'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('frecuencia_cobro', $contrato->frecuencia_cobro ?? 'mensual') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <label class="form-label">Descripción / Observaciones</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $contrato->descripcion) }}</textarea>
        </div>
    </div>

    <div class="action-bar">
        <a href="{{ route('contratos.show', $contrato) }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar cambios</button>
    </div>
</form>
</div>
@endsection
