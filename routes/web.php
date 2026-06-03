<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CobranzaController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\EgresoController;
use App\Http\Controllers\PanelDigitalController;
use App\Http\Controllers\PanelTradicionalController;
use App\Http\Controllers\DeudaController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ControlPublicitarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\ParrillaController;
use App\Http\Controllers\SunatController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\TramiteController;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/registro-maestro', [AuthController::class, 'registroMaestro'])->name('registro.maestro')->middleware('guest');
Route::post('/registro-maestro/verificar', [AuthController::class, 'verificarClaveMaestra'])->name('registro.verificar')->middleware('guest');

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Empresas
    Route::resource('empresas', EmpresaController::class);

    // Cobranzas
    Route::get('/cobranzas', [CobranzaController::class, 'index'])->name('cobranzas.index')
        ->middleware('can-permiso:cobranzas');
    Route::post('/cobranzas', [CobranzaController::class, 'store'])->name('cobranzas.store')
        ->middleware('admin');
    Route::patch('/cobranzas/{cobranza}/pagar', [CobranzaController::class, 'marcarPagada'])->name('cobranzas.pagar')
        ->middleware('admin');
    Route::delete('/cobranzas/{cobranza}', [CobranzaController::class, 'destroy'])->name('cobranzas.destroy')
        ->middleware('admin');
    Route::get('/cobranzas/{cobranza}/recibo/{formato?}', [CobranzaController::class, 'recibo'])
        ->name('cobranzas.recibo')->middleware('can-permiso:cobranzas');

    // Ingresos
    Route::get('/ingresos', [IngresoController::class, 'index'])->name('ingresos.index')
        ->middleware('can-permiso:ingresos');
    Route::post('/ingresos', [IngresoController::class, 'store'])->name('ingresos.store')
        ->middleware('admin');
    Route::delete('/ingresos/{ingreso}', [IngresoController::class, 'destroy'])->name('ingresos.destroy')
        ->middleware('admin');

    // Egresos
    Route::get('/egresos', [EgresoController::class, 'index'])->name('egresos.index')
        ->middleware('can-permiso:egresos');
    Route::post('/egresos', [EgresoController::class, 'store'])->name('egresos.store')
        ->middleware('admin');
    Route::delete('/egresos/{egreso}', [EgresoController::class, 'destroy'])->name('egresos.destroy')
        ->middleware('admin');

    // Paneles Digitales
    Route::resource('paneles-digitales', PanelDigitalController::class)
        ->parameters(['paneles-digitales' => 'panelDigital'])
        ->middleware('can-permiso:paneles_digitales');

    // Paneles Tradicionales
    Route::resource('paneles-tradicionales', PanelTradicionalController::class)
        ->parameters(['paneles-tradicionales' => 'panelTradicional'])
        ->middleware('can-permiso:paneles_tradicionales');

    // Deudas
    Route::resource('deudas', DeudaController::class)->middleware('admin');
    Route::post('/deudas/{deuda}/pago', [DeudaController::class, 'registrarPago'])->name('deudas.pago')
        ->middleware('admin');

    // Contratos
    Route::resource('contratos', ContratoController::class)->middleware('can-permiso:contratos');
    Route::post('/contratos/{contrato}/cobro', [ContratoController::class, 'registrarCobro'])
        ->name('contratos.cobro')->middleware('can-permiso:contratos');
    Route::post('/contratos/{contrato}/importar-cotizacion', [ContratoController::class, 'importarDeCotizacion'])
        ->name('contratos.importar-cotizacion')->middleware('admin');
    Route::post('/contratos/{contrato}/generar-cuotas', [ContratoController::class, 'generarCuotas'])
        ->name('contratos.generar-cuotas')->middleware('admin');
    Route::patch('/contratos/{contrato}/elemento/{elemento}/instalacion',
        [ContratoController::class, 'actualizarInstalacion'])
        ->name('contratos.elemento.instalacion')->middleware('admin');

    // Control Publicitario
    Route::get('/control-publicitario', [ControlPublicitarioController::class, 'index'])
        ->name('control-publicitario.index')->middleware('can-permiso:control_publicitario');
    Route::get('/control-publicitario/exportar', [ControlPublicitarioController::class, 'exportar'])
        ->name('control-publicitario.exportar')->middleware('can-permiso:control_publicitario');
    Route::get('/panel-preview/{tipo}/{codigo}', [ControlPublicitarioController::class, 'panelPreview'])
        ->name('panel-preview')->middleware('can-permiso:control_publicitario');
    Route::post('/control-publicitario', [ControlPublicitarioController::class, 'store'])
        ->name('control-publicitario.store')->middleware('admin');
    Route::get('/control-publicitario/{controlPublicitario}', [ControlPublicitarioController::class, 'show'])
        ->name('control-publicitario.show')->middleware('can-permiso:control_publicitario');
    Route::patch('/control-publicitario/{controlPublicitario}', [ControlPublicitarioController::class, 'update'])
        ->name('control-publicitario.update')->middleware('admin');
    Route::delete('/control-publicitario/{controlPublicitario}', [ControlPublicitarioController::class, 'destroy'])
        ->name('control-publicitario.destroy')->middleware('admin');

    // Cotizaciones
    Route::resource('cotizaciones', CotizacionController::class)
        ->parameters(['cotizaciones' => 'cotizacion'])
        ->middleware('can-permiso:cotizaciones');
    Route::get('/cotizaciones/{cotizacion}/imprimir', [CotizacionController::class, 'imprimir'])
        ->name('cotizaciones.imprimir')->middleware('can-permiso:cotizaciones');
    Route::get('/cotizaciones/{cotizacion}/imprimir-carta', [CotizacionController::class, 'imprimirCarta'])
        ->name('cotizaciones.imprimir-carta')->middleware('can-permiso:cotizaciones');
    Route::get('/cotizaciones/{cotizacion}/convertir', [CotizacionController::class, 'convertirAContrato'])
        ->name('cotizaciones.convertir')->middleware('admin');
    Route::post('/cotizaciones/{cotizacion}/convertir', [CotizacionController::class, 'guardarContrato'])
        ->name('cotizaciones.guardar-contrato')->middleware('admin');

    // Trámites
    Route::resource('tramites', TramiteController::class)
        ->parameters(['tramites' => 'tramite'])
        ->middleware('can-permiso:tramites');
    Route::get('/tramites/{tramite}/proceso', [TramiteController::class, 'imprimirProceso'])
        ->name('tramites.proceso')->middleware('can-permiso:tramites');
    Route::post('/tramites/{tramite}/pdf', [TramiteController::class, 'subirPdf'])
        ->name('tramites.subir-pdf')->middleware('can-permiso:tramites');
    Route::delete('/tramites/{tramite}/pdf', [TramiteController::class, 'eliminarPdf'])
        ->name('tramites.eliminar-pdf')->middleware('can-permiso:tramites');
    Route::post('/tramites/{tramite}/pasos', [TramiteController::class, 'agregarPaso'])
        ->name('tramites.agregar-paso')->middleware('can-permiso:tramites');
    Route::patch('/tramites/{tramite}/pasos/{paso}', [TramiteController::class, 'actualizarPaso'])
        ->name('tramites.pasos.actualizar')->middleware('can-permiso:tramites');
    Route::post('/tramites/{tramite}/pasos/{paso}/pdf', [TramiteController::class, 'subirPdfPaso'])
        ->name('tramites.pasos.subir-pdf')->middleware('can-permiso:tramites');
    Route::delete('/tramites/{tramite}/pasos/{paso}/pdf', [TramiteController::class, 'eliminarPdfPaso'])
        ->name('tramites.pasos.eliminar-pdf')->middleware('can-permiso:tramites');

    // Almacenes
    Route::resource('almacenes', AlmacenController::class)
        ->parameters(['almacenes' => 'almacen'])
        ->middleware('admin');

    // Reportes
    Route::prefix('reportes')->name('reportes.')->middleware('can-permiso:reportes')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/flujo-mensual', [ReporteController::class, 'flujoMensual'])->name('flujo_mensual');
        Route::get('/cobranzas-pendientes', [ReporteController::class, 'cobranzasPendientes'])->name('cobranzas_pendientes');
        Route::get('/ingresos-por-empresa', [ReporteController::class, 'ingresosPorEmpresa'])->name('ingresos_por_empresa');
    });

    // SUNAT proxy (evita CORS en el navegador)
    Route::get('/sunat/ruc/{numero}', [SunatController::class, 'ruc'])->name('sunat.ruc');

    // Parrilla hoy
    Route::get('/parrilla/hoy', [ParrillaController::class, 'hoy'])
        ->name('parrilla.hoy')->middleware('can-permiso:control_publicitario');

    // Usuarios (solo admin)
    Route::resource('usuarios', UsuarioController::class)->middleware('admin');

    // Servicios (solo admin)
    Route::resource('servicios', ServicioController::class)->middleware('admin');
    Route::post('/servicios/quick', [ServicioController::class, 'storeQuick'])
        ->name('servicios.quick')->middleware('admin');

    // Auditoría (solo admin/gerencia)
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index')->middleware('admin');

    // API: foto de panel para cotizaciones
    Route::get('/panel-foto/{tipo}/{id}', [CotizacionController::class, 'getPanelFoto'])->name('panel.foto');
});
