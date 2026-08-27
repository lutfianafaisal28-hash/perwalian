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
    public const NAVY       = '1E3A8A';
    public const NAVY_DARK  = '172554';
    public const NAVY_LIGHT = '2563EB';
    public const EMERALD      = '10B981';
    public const EMERALD_LIGHT = 'D1FAE5';
    public const BLUE_LIGHT   = 'EFF6FF';
    public const GRAY_50  = 'F8FAFC';
    public const GRAY_100 = 'F1F5F9';
    public const GRAY_200 = 'E2E8F0';
    public const GRAY_300 = 'CBD5E1';
    public const GRAY_600 = '475569';
    public const GRAY_800 = '1E293B';
    public const WHITE = 'FFFFFF';

    // ── Header row: navy bg, white bold, center ──
    public static function styleHeaderRow(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => self::WHITE],
                'size'  => 10,
                'name'  => 'Calibri',
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::NAVY],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::NAVY_DARK]],
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::NAVY_DARK]],
                'left'   => ['borderStyle' => Border::BORDER_THIN,   'color' => ['rgb' => self::GRAY_300]],
                'right'  => ['borderStyle' => Border::BORDER_THIN,   'color' => ['rgb' => self::GRAY_300]],
            ],
        ]);
        if (preg_match('/([A-Z]+)(\d+):/', $range, $m)) {
            $sheet->getRowDimension((int) $m[2])->setRowHeight(24);
        }
    }

    // ── Data rows: zebra, thin borders, default font ──
    public static function styleDataRows(Worksheet $sheet, int $headerRow, int $lastRow, string $lastCol): void
    {
        $range = "A{$headerRow}:{$lastCol}{$lastRow}";
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'size'  => 9,
                'name'  => 'Calibri',
                'color' => ['rgb' => self::GRAY_800],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::GRAY_200]],
                'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::GRAY_200]],
                'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::GRAY_200]],
                'right'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::GRAY_200]],
            ],
        ]);
        // Zebra striping
        for ($r = $headerRow + 1; $r <= $lastRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB(self::GRAY_50);
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
            'font' => [
                'bold'  => true,
                'size'  => 14,
                'color' => ['rgb' => self::NAVY],
                'name'  => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(26);
        $row++;
    }

    // ── Subtitle row: navy bg, white text, timestamp + total badge ──
    public static function addSubtitle(Worksheet $sheet, string $lastCol, int &$row, int $total, array $filters = []): void
    {
        $timestamp = 'Diekspor: '.now()->translatedFormat('d F Y H:i').' WIB';
        $badgeText = '  Total: '.$total.' data';
        $fullText  = $timestamp.'     '.$badgeText;

        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->setCellValue("A{$row}", $fullText);

        // Navy background with WHITE text for readability
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => [
                'bold'  => false,
                'size'  => 9,
                'color' => ['rgb' => self::WHITE],
                'name'  => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::NAVY_DARK],
            ],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::NAVY]],
                'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::NAVY]],
                'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::NAVY]],
                'right'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::NAVY]],
            ],
        ]);

        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        // Filter pills row (only if filters exist) — white text on navy-light
        if (!empty($filters)) {
            $pillText = 'Filter:  '.implode('  |  ', array_map(fn($f) => ' '.$f.' ', $filters));
            $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
            $sheet->setCellValue("A{$row}", $pillText);
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => [
                    'bold'  => false,
                    'size'  => 8,
                    'color' => ['rgb' => self::WHITE],
                    'name'  => 'Calibri',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => self::NAVY_LIGHT],
                ],
                'borders' => [
                    'left'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::NAVY]],
                    'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::NAVY]],
                ],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(14);
            $row++;
        }
    }

    // ── Thin divider line ──
    public static function addDivider(Worksheet $sheet, string $lastCol, int &$row): void
    {
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::GRAY_300);
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
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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
