<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// ============================================================
// EXCELEXPORTSERVICE (Helper Export Elegant .xlsx)
// ============================================================
// Terpusat styling agar semua export konsisten: header navy,
// border halus, zebra rows, auto-filter, freeze pane, print.
// Dipakai oleh Mahasiswa, Dosen, Rekap, Bimbingan.
// ============================================================
class ExcelExportService
{
    public const NAVY = '1E3A8A';
    public const NAVY_DARK = '172554';
    public const EMERALD = '10B981';

    // Header style: navy background, white bold, center
    public static function styleHeaderRow(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Calibri'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
        ]);
        $sheet->getRowDimension(explode(':', $range)[0][1] ?? 4)->setRowHeight(22);
        // Fallback: set header row height explicitly if range like A4:H4
        if (preg_match('/:([A-Z]+)(\d+)/', $range, $m)) {
            $sheet->getRowDimension((int) $m[2])->setRowHeight(22);
        } elseif (preg_match('/([A-Z]+)(\d+):/', $range, $m)) {
            $sheet->getRowDimension((int) $m[2])->setRowHeight(22);
        }
    }

    public static function styleDataRows(Worksheet $sheet, int $headerRow, int $lastRow, string $lastCol): void
    {
        $range = "A{$headerRow}:{$lastCol}{$lastRow}";
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => '1F2937']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
        ]);
        // Zebra
        for ($r = $headerRow + 1; $r <= $lastRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }
        }
        // Center No., Angkatan, Semester, Tanggal cols via caller if needed
    }

    public static function setupSheet(Worksheet $sheet, string $title, string $sheetName = 'Data'): void
    {
        $sheet->setTitle(mb_substr($sheetName, 0, 31));
        $sheet->getSheetView()->setZoomScale(95);
        $sheet->getDefaultRowDimension()->setRowHeight(18);
    }

    // Title block rows 1-2: merged title + subtitle
    public static function addTitleBlock(Worksheet $sheet, string $title, string $subtitle, string $lastCol, int &$row): void
    {
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->setCellValue("A{$row}", $title);
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => self::NAVY], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(20);
        $row++;
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->setCellValue("A{$row}", $subtitle);
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['size' => 8, 'color' => ['rgb' => '64748B'], 'italic' => true, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(13);
        $row++;
        // thin divider line
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
        $sheet->getRowDimension($row)->setRowHeight(2);
        $row++;
    }

    public static function finalize(Worksheet $sheet, int $headerRow, string $lastCol, array $widths, bool $autoFilter = true): void
    {
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
        $sheet->freezePane('A' . ($headerRow + 1));
        if ($autoFilter) {
            $sheet->setAutoFilter("A{$headerRow}:{$lastCol}{$headerRow}");
        }
        $sheet->getSheetView()->setZoomScale(95);
        // Print setup
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.35);
        $sheet->getPageMargins()->setBottom(0.35);
        $sheet->getPageMargins()->setLeft(0.35);
        $sheet->getPageMargins()->setRight(0.35);
        $sheet->setShowGridLines(false);
    }

    public static function downloadResponse(Spreadsheet $spreadsheet, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public static function colLetter(int $index): string
    {
        // 0->A, 1->B
        $letter = '';
        $n = $index;
        while ($n >= 0) {
            $letter = chr($n % 26 + 65) . $letter;
            $n = intdiv($n, 26) - 1;
            if ($n < 0) break;
        }
        return $letter;
    }
}
