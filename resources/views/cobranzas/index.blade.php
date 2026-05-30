@extends('layouts.app')

@section('title', 'Cobranzas')
@section('subtitle', 'Control de cuotas y pagos')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <span style="font-size:13px;color:var(--text-light);font-weight:500">{{ $cobranzas->total() }} cuota(s)</span>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-collection"></i></div>
        <div>
            <div class="stat-value">{{ $cobranzas->total() }}</div>
            <div class="stat-label">Total cuotas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-value" style="color:#D97706">{{ $cobranzas->where('estado', 'pendiente')->count() }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="stat-value" style="color:#059669">{{ $cobranzas->where('estado', 'pagada')->count() }}</div>
            <div class="stat-label">Pagadas</div>
        </div>
    </div>
</div>

<div class="filter-bar">
    <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%" method="GET">
        <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" style="max-width:250px" placeholder="Buscar empresa...">
        <select name="estado" class="form-select" style="max-width:160px">
            <option value="">Todos los estados</option>
            <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="pagada" {{ request('estado') === 'pagada' ? 'selected' : '' }}>Pagada</option>
            <option value="vencida" {{ request('estado') === 'vencida' ? 'selected' : '' }}>Vencida</option>
        </select>
        <input type="month" name="mes" value="{{ request('mes') }}" class="form-control" style="max-width:160px">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i>Filtrar</button>
        <a href="{{ route('cobranzas.index') }}" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

<div class="card">
    <div class="card-header ch-green">
        <span><i class="bi bi-cash-coin"></i>Registro de Cobranzas</span>
        <span style="font-size:12px;font-weight:500;color:var(--text-light)">{{ $cobranzas->total() }} cuota(s)</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Empresa</th><th>Contrato</th><th>N° Cuota</th><th>Concepto</th><th>Monto</th>
                    <th>Vencimiento</th><th>Estado</th>
                    @if(auth()->user()->esAdmin())<th class="td-end">Acciones</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($cobranzas as $cob)
                <tr>
                    <td class="fw-700" style="color:var(--text-dark)">
                        @if($cob->empresa)
                            <a href="{{ route('empresas.show', $cob->empresa) }}" style="color:var(--text-dark);text-decoration:none">{{ $cob->empresa->nombre }}</a>
                        @else {{ $cob->empresa_nombre ?? '—' }} @endif
                    </td>
                    <td>
                        @if($cob->contrato_id)
                            <a href="{{ route('contratos.show', $cob->contrato_id) }}"
                               style="font-size:12px;font-weight:600;color:#2563EB;text-decoration:none;white-space:nowrap">
                                <i class="bi bi-file-earmark-text" style="margin-right:3px"></i>
                                {{ $cob->contrato?->numero_contrato ?? 'CONT-'.$cob->contrato_id }}
                            </a>
                        @else
                            <span class="text-muted" style="font-size:12px">—</span>
                        @endif
                    </td>
                    <td class="fw-600">{{ $cob->numero_cuota }}</td>
                    <td class="text-muted">{{ $cob->concepto ?? '—' }}</td>
                    <td class="fw-700">S/. {{ number_format($cob->monto, 0, ',', '.') }}</td>
                    <td>
                        {{ $cob->fecha_vencimiento->format('d/m/Y') }}
                        @if($cob->estado === 'pendiente' && $cob->fecha_vencimiento->isPast())
                            <span class="badge badge-danger" style="margin-left:4px">Vencida</span>
                        @endif
                    </td>
                    <td>
                        @if($cob->estado === 'pagada')
                            <span class="badge badge-success"><i class="bi bi-check-lg"></i>Pagada</span>
                        @else
                            <span class="badge badge-warning">Pendiente</span>
                        @endif
                    </td>
                    @if(auth()->user()->esAdmin())
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            {{-- Botón recibo --}}
                            <div class="dropdown" style="position:relative" id="drop-{{ $cob->id }}">
                                <button type="button"
                                        class="btn btn-sm btn-secondary btn-icon"
                                        title="Imprimir recibo"
                                        onclick="toggleReciboDrop({{ $cob->id }})">
                                    <i class="bi bi-receipt"></i>
                                </button>
                                <div id="drop-menu-{{ $cob->id }}"
                                     style="display:none;position:absolute;right:0;top:calc(100% + 4px);
                                            background:#fff;border:1px solid #E2E8F0;border-radius:10px;
                                            box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:500;min-width:150px;overflow:hidden">
                                    <a href="{{ route('cobranzas.recibo', [$cob, 'a4']) }}" target="_blank"
                                       style="display:flex;align-items:center;gap:8px;padding:9px 14px;font-size:13px;
                                              color:#0F172A;text-decoration:none;transition:background .12s"
                                       onmouseover="this.style.background='#F8FAFC'"
                                       onmouseout="this.style.background=''">
                                        <i class="bi bi-file-earmark-text" style="color:#374151"></i> Formato A4
                                    </a>
                                    <a href="{{ route('cobranzas.recibo', [$cob, '80mm']) }}" target="_blank"
                                       style="display:flex;align-items:center;gap:8px;padding:9px 14px;font-size:13px;
                                              color:#0F172A;text-decoration:none;border-top:1px solid #F1F5F9;transition:background .12s"
                                       onmouseover="this.style.background='#F8FAFC'"
                                       onmouseout="this.style.background=''">
                                        <i class="bi bi-printer" style="color:#374151"></i> Ticket 80mm
                                    </a>
                                </div>
                            </div>

                            @if($cob->estado === 'pendiente')
                            <form action="{{ route('cobranzas.pagar', $cob) }}" method="POST" id="form-pagar-{{ $cob->id }}">
                                @csrf @method('PATCH')
                                <button type="button" class="btn btn-sm btn-success btn-icon" title="Marcar pagada"
                                    onclick="confirmarAccion('form-pagar-{{ $cob->id }}','pagar',
                                        '{{ addslashes($cob->empresa?->nombre ?? 'Sin empresa') }}',
                                        '{{ number_format($cob->monto,2) }}',
                                        'Cuota {{ $cob->numero_cuota }}')">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('cobranzas.destroy', $cob) }}" method="POST" id="form-del-{{ $cob->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger btn-icon" title="Eliminar"
                                    onclick="confirmarAccion('form-del-{{ $cob->id }}','eliminar',
                                        '{{ addslashes($cob->empresa?->nombre ?? 'Sin empresa') }}',
                                        '{{ number_format($cob->monto,2) }}',
                                        'Cuota {{ $cob->numero_cuota }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-cash-coin"></i><p>No hay cuotas registradas</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cobranzas->hasPages())
    <div class="card-footer">{{ $cobranzas->withQueryString()->links() }}</div>
    @endif
</div>
{{-- Modal de confirmación ─────────────────────────────────── --}}
<div id="modal-confirm"
     style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.5);
            align-items:center;justify-content:center;padding:16px">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:380px;
                box-shadow:0 20px 50px rgba(0,0,0,.25);overflow:hidden">

        {{-- Header con color dinámico --}}
        <div id="mc-header"
             style="padding:18px 20px 14px;display:flex;align-items:center;gap:12px">
            <div id="mc-icon-wrap"
                 style="width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i id="mc-icon" style="font-size:20px"></i>
            </div>
            <div>
                <div id="mc-title" style="font-weight:700;font-size:15px;color:#0F172A"></div>
                <div id="mc-subtitle" style="font-size:12px;color:#64748B;margin-top:2px"></div>
            </div>
        </div>

        {{-- Detalle de la cuota --}}
        <div style="padding:0 20px 16px">
            <div id="mc-detail"
                 style="background:#F8FAFC;border-radius:8px;padding:12px 14px;font-size:13px;color:#334155;line-height:1.7">
            </div>
        </div>

        {{-- Botones --}}
        <div style="display:flex;gap:10px;padding:12px 20px 18px;justify-content:flex-end">
            <button type="button" id="mc-cancel"
                    style="background:#F1F5F9;color:#374151;border:none;padding:9px 20px;border-radius:8px;
                           font-size:13px;font-weight:600;cursor:pointer">
                Cancelar
            </button>
            <button type="button" id="mc-confirm"
                    style="border:none;padding:9px 22px;border-radius:8px;
                           font-size:13px;font-weight:700;cursor:pointer;color:#fff">
                Confirmar
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let _pendingFormId = null;

function confirmarAccion(formId, tipo, empresa, monto, cuota) {
    _pendingFormId = formId;

    const esPagar   = tipo === 'pagar';
    const color     = esPagar ? '#059669' : '#DC2626';
    const bgLight   = esPagar ? '#D1FAE5' : '#FEE2E2';
    const icono     = esPagar ? 'bi-check-circle-fill' : 'bi-trash-fill';
    const titulo    = esPagar ? 'Marcar como pagada' : 'Eliminar cuota';
    const subtitulo = esPagar ? 'Esta acción registrará el pago' : 'Esta acción no se puede deshacer';
    const btnTxt    = esPagar ? '✓ Marcar pagada' : 'Sí, eliminar';

    document.getElementById('mc-header').style.background = bgLight;
    document.getElementById('mc-icon-wrap').style.background = color;
    document.getElementById('mc-icon').className = 'bi ' + icono;
    document.getElementById('mc-icon').style.color = '#fff';
    document.getElementById('mc-title').textContent   = titulo;
    document.getElementById('mc-subtitle').textContent = subtitulo;
    document.getElementById('mc-confirm').style.background = color;
    document.getElementById('mc-confirm').textContent = btnTxt;

    document.getElementById('mc-detail').innerHTML =
        '<div><span style="color:#94A3B8;font-size:11px;font-weight:700;text-transform:uppercase">Empresa</span>' +
        '<div style="font-weight:600;margin-top:1px">' + empresa + '</div></div>' +
        '<div style="margin-top:8px"><span style="color:#94A3B8;font-size:11px;font-weight:700;text-transform:uppercase">Cuota</span>' +
        '<div style="font-weight:600;margin-top:1px">' + cuota + ' &nbsp;·&nbsp; <span style="color:#059669">S/. ' + monto + '</span></div></div>';

    document.getElementById('modal-confirm').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

document.getElementById('mc-confirm').addEventListener('click', function () {
    if (_pendingFormId) {
        document.getElementById(_pendingFormId).submit();
    }
    cerrarConfirm();
});

document.getElementById('mc-cancel').addEventListener('click', cerrarConfirm);

document.getElementById('modal-confirm').addEventListener('click', function (e) {
    if (e.target === this) cerrarConfirm();
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') cerrarConfirm();
    if (e.key === 'Enter' && _pendingFormId) {
        document.getElementById(_pendingFormId).submit();
        cerrarConfirm();
    }
});

function cerrarConfirm() {
    document.getElementById('modal-confirm').style.display = 'none';
    document.body.style.overflow = '';
    _pendingFormId = null;
}

// ── Dropdown de recibo ────────────────────────────────────────
function toggleReciboDrop(id) {
    const menu = document.getElementById('drop-menu-' + id);
    const isOpen = menu.style.display !== 'none';
    // cerrar todos
    document.querySelectorAll('[id^="drop-menu-"]').forEach(m => m.style.display = 'none');
    if (!isOpen) menu.style.display = 'block';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('[id^="drop-"]')) {
        document.querySelectorAll('[id^="drop-menu-"]').forEach(m => m.style.display = 'none');
    }
});
</script>
@endpush

@endsection
