<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Sheet2Export implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'sku product variant', // Mã sản phẩm
            'quantity',            // Số lượng
            'price_per_unit',      // Giá mỗi đơn vị
            'expected_price',      // Giá dự kiến
        ];
    }

    public function array(): array
    {
        return [
            ['Iphone-14-plus-violet-512GB', 10, 1000, 800],
            ['Samsung-s22-ultra-sliver-1TB', 12, 2000, 3000],
        ];
    }
}
