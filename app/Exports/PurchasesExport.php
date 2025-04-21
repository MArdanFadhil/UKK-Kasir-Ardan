<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class PurchasesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    public function collection()
    {
        return Purchase::all();
    }

    public function map($purchase): array
    {
        $change = $purchase->total_pay - $purchase->total_price;

        return [
            $purchase->id,
            $purchase->pay_date,
            $purchase->total_price,
            $purchase->total_pay,
            $purchase->total_return,
            $purchase->member_id,
            $purchase->user_id,
            $purchase->used_point,
            $change,
            $purchase->created_at,
            $purchase->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Bayar',
            'Total Harga',
            'Total Bayar',
            'Total Kembali',
            'ID Member',
            'ID User',
            'Poin Digunakan',
            'Kembalian',
            'Dibuat Pada',
            'Diperbarui Pada',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true]], // karena header ada di baris ke-2
        ];
    }

    public function startCell(): string
    {
        return 'A2'; // Header mulai dari baris ke-2
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            // Judul utama
            $event->sheet->mergeCells('A1:K1');
            $event->sheet->setCellValue('A1', 'LAPORAN DATA PEMBELIAN');
            $event->sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);

            // Subjudul (misalnya periode atau keterangan lain)
            $event->sheet->mergeCells('A2:K2');
            $event->sheet->setCellValue('A2', 'Periode: 01-04-2025 s/d 21-04-2025');
            $event->sheet->getStyle('A2')->applyFromArray([
                'font' => ['italic' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);
        },
    ];
}

}
