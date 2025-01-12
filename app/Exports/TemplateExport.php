<?php

namespace App\Exports;

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TemplateExport implements WithMultipleSheets
{
    /**
     * Trả về các sheet
     */
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function sheets(): array
    {
        return [
            new Sheet1Export(),
            new Sheet2Export($this->data),
        ];
    }
}