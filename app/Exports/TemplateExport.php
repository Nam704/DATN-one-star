<?php

namespace App\Exports;

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TemplateExport implements WithMultipleSheets
{
    /**
     * Trả về các sheet
     */
    public function sheets(): array
    {
        return [
            new Sheet1Export(),
            new Sheet2Export(),
        ];
    }
}
