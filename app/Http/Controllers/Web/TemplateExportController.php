<?php

namespace App\Http\Controllers\Web;

use App\Exports\TemplateExport;
use App\Http\Controllers\Controller;
use App\Models\Product_variant;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TemplateExportController extends Controller
{


    public function exportSamplefile()
    {
        $data = Product_variant::all();

        return Excel::download(new TemplateExport($data), 'templateSample.xlsx');
    }
}
