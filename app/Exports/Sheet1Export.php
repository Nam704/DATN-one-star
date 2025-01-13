<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Sheet1Export implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'supplier_name', // Tên nhà cung cấp
            'note', // Ghi chú
        ];
    }

    public function array(): array
    {
        return [
            ['Chim', 'Ghi chú A'], // Dữ liệu mẫu
        ];
    }
}
