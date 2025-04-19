<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // ✅ yang benar

class PurchasesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            1 => ['font' => ['bold' => true]],
        ];
    }
}
