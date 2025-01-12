<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Sheet2Export implements FromArray, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

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
        $data = []; // Khởi tạo mảng rỗng

        foreach ($this->data as $item) {
            $data[] = [
                $item->sku,
                // $item->quantity,
                // $item->price_per_unit,
                // $item->expected_price,
            ];
        }

        return $data; // Trả về dữ liệu
    }
}
