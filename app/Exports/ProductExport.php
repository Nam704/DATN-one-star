<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class ProductExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Products' => new ProductsSheetExport(),
            'Variants' => new VariantsSheetExport(),
        ];
    }
}

class ProductsSheetExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['iPhone 15', 'New Apple Phone', '1', '2', 'iphone.jpg', 'img1.jpg', 'img2.jpg', 'img3.jpg', 'img4.jpg'],
            ['Samsung S23', 'Samsung Flagship', '1', '3', 's23.jpg', 's23_1.jpg', 's23_2.jpg', 's23_3.jpg', 's23_4.jpg'],
        ];
    }

    public function headings(): array
    {
        return ['Name', 'Description', 'Category ID', 'Brand ID', 'Main Image', 'Album Image 1', 'Album Image 2', 'Album Image 3', 'Album Image 4'];
    }
}

class VariantsSheetExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['iPhone 15', 'IP15-128GB',  'red_128.jpg', 'Red', '128GB', '6GB'],
            ['iPhone 15', 'IP15-256GB',  'blue_256.jpg', 'Blue', '256GB', '6GB'],
            ['Samsung S23', 'S23-512GB',  's23_512.jpg', 'Black', '512GB', '8GB'],
        ];
    }

    public function headings(): array
    {
        return ['Product Name', 'Variant SKU', 'Image', 'Color', 'Storage', 'RAM'];
    }
}