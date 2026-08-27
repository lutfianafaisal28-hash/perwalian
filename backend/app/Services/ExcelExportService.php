<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// ============================================================
// EXCELEXPORTSERVICE — Helper Export Elegant .xlsx
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
    public const EMERALD_LIGHT = 'D1FAE5';
    public const BLUE_LIGHT = 'EFF6FF';

    // ── Header row: navy bg, white bold, center ──
    public static function styleHeaderRow(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        if (preg_match('/([A-Z]+)(\d+):/', $range, $m)) {
            $sheet->getRowDimension((int) $m[2])->setRowHeight(22);
        }
    }

    // ── Data rows: zebra, thin borders, default font ──
    public static function styleDataRows(Worksheet $sheet, int $headerRow, int $lastRow, string $lastCol): void
    {
        $range = "A{$headerRow}:{$lastCol}{$lastRow}";
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['size' => 9, 'name' => 'Calibri', 'color' => ['rgb' => '1F2937']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
        ]);
        for ($r = $headerRow + 1; $r <= $lastRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }
        }
    }

    // ── Sheet defaults ──
    public static function setupSheet(Worksheet $sheet, string $title, string $sheetName = 'Data'): void
    {
        $sheet->setTitle(mb_substr($sheetName, 0, 31));
        $sheet->getSheetView()->setZoomScale(95);
        $sheet->getDefaultRowDimension()->setRowHeight(18);
    }

    // ── Title row: merged, navy bold, large ──
    public static function addTitle(Worksheet $sheet, string $title, string $lastCol, int &$row): void
    {
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->setCellValue("A{$row}", $title);
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => self::NAVY], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(22);
        $row++;
    }

    // ── Subtitle row: timestamp + green badge + filter pills ──
    //   $total  = e.g. 12
    //   $filters = e.g. ['search="budi"', 'angkatan 2023']
    public static function addSubtitle(Worksheet $sheet, string $lastCol, int &$row, int $total, array $filters = []): void
    {
        $timestamp = 'Diekspor: '.now()->translatedFormat('d F Y H:i').' WIB';

        // Build text: timestamp on left, badge info as text
        $badgeText = 'Total: '.$total.' data';
        $fullText = $timestamp.'     '.$badgeText;

        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->setCellValue("A{$row}", $fullText);

        // Style: dark gray text, left aligned
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['rgb' => '475569'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Green badge: we cannot style partial merged cell, so apply
        // green fill to the whole row. The badge text is visually prominent.
        $sheet->getStyle("A{$row}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::EMERALD_LIGHT]],
        ]);

        // Re-apply font (fill override) with bold badge look
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => false, 'size' => 9, 'color' => ['rgb' => '065F46'], 'name' => 'Calibri'],
        ]);

        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        // Filter pills row (only if filters exist)
        if (!empty($filters)) {
            $pillText = 'Filter aktif:  '.implode('  |  ', array_map(fn($f) => ' '.$f.' ', $filters));
            $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
            $sheet->setCellValue("A{$row}", $pillText);
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['size' => 8, 'color' => ['rgb' => '1E3A8A'], 'name' => 'Calibri'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_LIGHT]],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(14);
            $row++;
        }
    }

    // ── Thin divider line ──
    public static function addDivider(Worksheet $sheet, string $lastCol, int &$row): void
    {
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CBD5E1');
        $sheet->getRowDimension($row)->setRowHeight(2);
        $row++;
    }

    // ── Backward-compatible wrapper: title + subtitle + divider ──
    public static function addTitleBlock(Worksheet $sheet, string $title, string $lastCol, int &$row, array $opts = []): void
    {
        self::addTitle($sheet, $title, $lastCol, $row);

        $total   = $opts['total'] ?? 0;
        $filters = $opts['filters'] ?? [];
        self::addSubtitle($sheet, $lastCol, $row, $total, $filters);
        self::addDivider($sheet, $lastCol, $row);
    }

    // ── Column widths, freeze pane, auto-filter, print setup ──
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
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.35);
        $sheet->getPageMargins()->setBottom(0.35);
        $sheet->getPageMargins()->setLeft(0.35);
        $sheet->getPageMargins()->setRight(0.35);
        $sheet->setShowGridLines(false);
    }

    // ── Download response helper ──
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

    // ── Column letter helper: 0→A, 1→B, 25→Z, 26→AA ──
    public static function colLetter(int $index): string
    {
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
