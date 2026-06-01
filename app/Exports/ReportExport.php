<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(private Collection $transactions)
    {
    }

    public function headings(): array
    {
        return ['Tanggal', 'Tipe', 'Kategori', 'Deskripsi', 'User', 'Rekening', 'Nominal', 'Privat'];
    }

    public function array(): array
    {
        return $this->transactions->map(fn ($transaction) => [
            $transaction->date->format('Y-m-d H:i:s'),
            $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
            $transaction->category?->name,
            $transaction->description,
            $transaction->user?->name,
            $transaction->bank?->name,
            (float) $transaction->amount,
            $transaction->is_private ? 'Ya' : 'Tidak',
        ])->all();
    }
}
