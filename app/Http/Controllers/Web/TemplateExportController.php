<?php

namespace App\Http\Controllers\Web;

use App\Exports\TemplateExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TemplateExportController extends Controller
{
    public function exportSamplefile()
    {
        return Excel::download(new TemplateExport, 'templateSample.xlsx');
    }
}
