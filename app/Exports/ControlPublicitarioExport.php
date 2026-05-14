<?php

namespace App\Exports;

use App\Models\ControlPublicitario;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ControlPublicitarioExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    private array $filters;
    private int $rowIndex = 1;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Control Publicitario';
    }

    public function query()
    {
        $query = ControlPublicitario::query();

        if (!empty($this->filters['estado'])) {
            $query->where('estado', $this->filters['estado']);
        }
        if (!empty($this->filters['tipo_panel'])) {
            $query->where('tipo_panel', $this->filters['tipo_panel']);
        }
        if (!empty($this->filters['buscar'])) {
            $query->where(function ($q) {
                $q->where('empresa_nombre', 'like', '%' . $this->filters['buscar'] . '%')
                  ->orWhere('panel_codigo', 'like', '%' . $this->filters['buscar'] . '%');
            });
        }

        return $query->orderBy('empresa_nombre');
    }

    public function headings(): array
    {
        return [
            'Empresa',
            'Código Panel',
            'Tipo Panel',
            'Estado',
            'Fecha Inicio',
            'Fecha Fin',
            'Días Restantes',
            'Monto Pagado ($)',
            'Monto Pendiente ($)',
            'Notas',
        ];
    }

    public function map($row): array
    {
        $diasRestantes = '';
        if ($row->fecha_fin) {
            $diff = now()->diffInDays($row->fecha_fin, false);
            $diasRestantes = $diff < 0 ? 'VENCIDA' : ($diff === 0 ? 'Hoy' : $diff . ' días');
        }

        return [
            $row->empresa_nombre,
            $row->panel_codigo,
            ucfirst($row->tipo_panel),
            ucfirst($row->estado),
            $row->fecha_inicio?->format('d/m/Y') ?? '',
            $row->fecha_fin?->format('d/m/Y') ?? '',
            $diasRestantes,
            $row->monto_pagado !== null ? number_format($row->monto_pagado, 2) : '',
            $row->monto_pendiente !== null ? number_format($row->monto_pendiente, 2) : '',
            $row->notas ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'J';

        // Header row — fondo azul oscuro, texto blanco, negrita
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 12,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E3A5F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FF2563EB'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Filas de datos — alternas y con bordes
        for ($i = 2; $i <= $lastRow; $i++) {
            $bgColor = ($i % 2 === 0) ? 'FFF0F4FF' : 'FFFFFFFF';
            $sheet->getStyle("A{$i}:J{$i}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $bgColor],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFD1D5DB'],
                    ],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($i)->setRowHeight(18);

            // Colorear columna Estado (D)
            $estado = strtolower($sheet->getCell("D{$i}")->getValue());
            $estadoColor = match ($estado) {
                'activo'    => 'FF16A34A',
                'pausado'   => 'FFD97706',
                'cancelado' => 'FFDC2626',
                default     => 'FF374151',
            };
            $sheet->getStyle("D{$i}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => $estadoColor]],
            ]);

            // Colorear columna Días Restantes (G)
            $diasVal = $sheet->getCell("G{$i}")->getValue();
            if ($diasVal === 'VENCIDA') {
                $sheet->getStyle("G{$i}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFDC2626']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFEE2E2']],
                ]);
            } elseif (is_numeric(rtrim($diasVal, ' días')) && (int) $diasVal <= 3) {
                $sheet->getStyle("G{$i}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFEA580C']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF7ED']],
                ]);
            } elseif (is_numeric(rtrim($diasVal, ' días')) && (int) $diasVal <= 15) {
                $sheet->getStyle("G{$i}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FF2563EB']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEFF6FF']],
                ]);
            }
        }

        // Columnas de monto: alinear a la derecha
        $sheet->getStyle("H2:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Borde exterior grueso
        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color'       => ['argb' => 'FF1E3A5F'],
                ],
            ],
        ]);

        return [];
    }
}
