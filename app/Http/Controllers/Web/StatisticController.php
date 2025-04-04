<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StatisticController extends Controller
{
    private $user;
    private $product;
    private $category;
    private $order;

    public function __construct(User $user, Product $product, Category $category, Order $order)
    {
        $this->user = $user;
        $this->product = $product;
        $this->category = $category;
        $this->order = $order;
    }

    public function productStatistics(Request $request)
    {
        if (auth()->check()) {
            $startDate = $request->input('start_date', now()->startOfDay()->toDateString());
            $endDate = $request->input('end_date', now()->endOfDay()->toDateString());
            $countData = [
                "product" => $this->product->whereBetween('created_at', [$startDate, $endDate])->count(),
                "revenue" => $this->order->where('id_order_status', '4')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total'),
                "order" => $this->order->where('id_order_status', '4')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                "user" => $this->user->whereBetween('created_at', [$startDate, $endDate])->count()

            ];
            $topProduct = [
                "least_sold_products" => $this->product->least_sold_products($startDate, $endDate),
            ];
            $low_stock_products = $this->product->low_stock_products($startDate, $endDate);
            $categories_with_revenue = $this->category->categories_with_revenue();
            $top_view_products = $this->product->where('status', 'active')->where('view', '>', 0)->orderBy('view', 'desc')->take(10)->get();
            $top_comment_products = [
                [
                    'name' => 'Iphone 14',
                    'image_primary' => '/storage/products/1742179523_67d78cc366b50.png',
                    'total_comments' => 100,
                ],
                [
                    'name' => 'Google Pixel 7 Pro',
                    'image_primary' => '/storage/products/1742179523_67d78cc37b4e1.png',
                    'total_comments' => 80,
                ],
                [
                    'name' => 'Samsung Galaxy A34 5G',
                    'image_primary' => '/storage/products/1742179523_67d78cc383c82.png',
                    'total_comments' => 60,
                ]
            ];
            return view('admin.statistic.productstatistic', compact(
                'countData',
                'topProduct',
                'low_stock_products',
                'categories_with_revenue',
                'top_view_products',
                'top_comment_products',
                'startDate',
                'endDate'
            ));
        } else {
            return redirect()->route('admin.statistics.productStatistics');
        }
    }
    // api biểu đồ sản phẩm bán chạy nhất
    public function topSaleProducts(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $top_sale_products = $this->product->top_sale_products($start_date, $end_date);
        return response()->json($top_sale_products);
    }

    // tạo api cho biểu đồ sản phẩm đã bán
    public function productSold(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $top_sale_products = $this->product->productSold($start_date, $end_date);
        return response()->json($top_sale_products);
    }

    //tạo api cho biểu đồ danh mục sản phẩm
    public function categoryStatistics(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = Category::select('categories.name')
            ->leftJoin('products', 'categories.id', '=', 'products.id_category')
            ->whereNull('categories.deleted_at')
            ->groupBy('categories.id', 'categories.name')
            ->selectRaw('COUNT(products.id) as total_products');

        if ($start_date && $end_date) {
            $query->whereBetween('products.created_at', [$start_date, $end_date]);
        }

        $categories = $query->orderBy('total_products', 'desc')->get();

        return response()->json($categories);
    }


    public function exportTopSaleProducts(Request $request)
    {
        // Lấy tham số ngày từ request
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');

        // Nếu có giá trị, chuyển thành khoảng thời gian đầy đủ
        $start_date_full = $start_date ? $start_date . ' 00:00:00' : null;
        $end_date_full   = $end_date   ? $end_date   . ' 23:59:59' : null;


        // Lấy dữ liệu từ model Product
        $products = $this->product->top_sale_products($start_date_full, $end_date_full);

        // Khởi tạo Spreadsheet và lấy sheet chính
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Tiêu đề báo cáo (dòng 1)
        $sheet->setCellValue('A1', 'Top Sản Phẩm Bán Chạy');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 2. Hiển thị ngày bắt đầu và kết thúc (dòng 2)
        // Nếu không có ngày nào được chọn, hiển thị 'Chưa chọn'
        $displayStart = $start_date ? $start_date : 'Chưa chọn';
        $displayEnd   = $end_date ? $end_date : 'Chưa chọn';
        $sheet->setCellValue('A2', "Ngày bắt đầu: $displayStart");
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('D2', "Ngày kết thúc: $displayEnd");
        $sheet->mergeCells('D2:E2');
        $sheet->getStyle('A2:E2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2:E2')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Dòng 3 để tạo khoảng cách (có thể merge để đảm bảo hiển thị đẹp)
        $sheet->mergeCells('A3:E3');

        // 3. Header bảng (dòng 4)
        $sheet->setCellValue('A4', 'STT');
        $sheet->setCellValue('B4', 'Tên Sản Phẩm');
        $sheet->setCellValue('C4', 'Ảnh Chính');
        $sheet->setCellValue('D4', 'Tổng Số Bán');
        $headerRange = 'A4:E4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Thiết lập độ rộng cột cố định cho cột C (ảnh)
        $sheet->getColumnDimension('C')->setWidth(20);
        foreach (['A', 'B', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 4. Ghi dữ liệu bắt đầu từ dòng 5
        $row = 5;
        if ($products->isEmpty()) {
            $sheet->setCellValue('A5', 'Không có sản phẩm nào trong khoảng thời gian này.');
            $sheet->mergeCells('A5:E5');
            $sheet->getStyle('A5')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        } else {
            $stt = 1;
            foreach ($products as $product) {
                // STT và Tên sản phẩm
                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $product->name);
                $sheet->setCellValue('D' . $row, $product->total_sold);

                // Chèn ảnh vào cột C
                $imagePath = public_path($product->image_primary);
                if (file_exists($imagePath)) {
                    $drawing = new Drawing();
                    $drawing->setPath($imagePath);
                    $drawing->setName('Product Image');
                    $drawing->setDescription('Product Image');
                    $drawing->setCoordinates('C' . $row);
                    $drawing->setWidth(50);
                    $drawing->setHeight(50);
                    // Điều chỉnh offset để ảnh nằm giữa ô
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($row)->setRowHeight(60);
                } else {
                    $sheet->setCellValue('C' . $row, 'No image');
                }

                // Căn giữa nội dung của hàng
                $sheet->getStyle("A{$row}:D{$row}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $row++;
            }
        }

        // 5. Xuất file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'top_sale_products.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }


    public function exportproductSold(Request $request)
    {
        // Lấy tham số ngày từ request
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');

        // Nếu có giá trị, chuyển đổi thành khoảng thời gian đầy đủ
        $start_date_full = $start_date ? $start_date . ' 00:00:00' : null;
        $end_date_full   = $end_date   ? $end_date   . ' 23:59:59' : null;

        // Debug: bạn có thể kiểm tra giá trị này tạm thời
        // dd($start_date_full, $end_date_full);

        // Lấy dữ liệu theo truy vấn từ model Product
        // Giả sử phương thức top_sale_products trong model đã xử lý điều kiện lọc theo created_at
        $products = $this->product->productSold($start_date_full, $end_date_full);

        // Nếu không có dữ liệu nào, bạn có thể dd($products) để xem kết quả
        // dd($products);

        // Khởi tạo Spreadsheet và lấy sheet chính
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Tiêu đề báo cáo (dòng 1)
        $sheet->setCellValue('A1', 'Sản Phẩm Đã Bán');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // 2. Hiển thị ngày bắt đầu và kết thúc (dòng 2)
        $displayStart = $start_date ? $start_date : 'Chưa chọn';
        $displayEnd   = $end_date ? $end_date : 'Chưa chọn';
        $sheet->setCellValue('A2', "Ngày bắt đầu: $displayStart");
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('D2', "Ngày kết thúc: $displayEnd");
        $sheet->mergeCells('D2:E2');
        $sheet->getStyle('A2:E2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2:E2')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Dòng 3 để trống
        $sheet->mergeCells('A3:D3');

        // 3. Header bảng (dòng 4)
        $sheet->setCellValue('A4', 'STT');
        $sheet->setCellValue('B4', 'Tên Sản Phẩm');
        $sheet->setCellValue('C4', 'Ảnh Chính');
        $sheet->setCellValue('D4', 'Tổng Số Bán');
        $headerRange = 'A4:E4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // Thiết lập độ rộng cột cố định cho cột D (ảnh)
        $sheet->getColumnDimension('D')->setWidth(20);
        foreach (['A', 'B', 'C', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 4. Ghi dữ liệu (từ dòng 5)
        if ($products->isEmpty()) {
            $sheet->setCellValue('A5', 'Không có sản phẩm nào trong khoảng thời gian này.');
            $sheet->mergeCells('A5:D5');
            $sheet->getStyle('A5')->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        } else {
            $row = 5;
            $stt = 1;
            foreach ($products as $product) {
                // STT, ID, Tên Sản Phẩm, Tổng Số Bán
                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $product->name);
                $sheet->setCellValue('D' . $row, $product->total_sold);

                // Chèn ảnh vào cột D
                $imagePath = public_path($product->image_primary);
                if (file_exists($imagePath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setPath($imagePath);
                    $drawing->setName('Product Image');
                    $drawing->setDescription('Product Image');
                    $drawing->setCoordinates('C' . $row);
                    $drawing->setWidth(50);
                    $drawing->setHeight(50);
                    // Điều chỉnh offset để ảnh nằm gần giữa ô
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($row)->setRowHeight(60);
                } else {
                    $sheet->setCellValue('C' . $row, 'No image');
                }

                // Căn giữa nội dung của hàng
                $sheet->getStyle("A{$row}:D{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $row++;
            }
        }

        // 5. Xuất file Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'products_sold.xlsx';

        return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
    public function exportLeastSoldProducts(Request $request)
    {
        // Lấy tham số ngày bắt đầu và kết thúc từ request
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');

        // Nếu không có ngày, dùng giá trị mặc định (ngày hôm nay)
        $start_date = $start_date ?: now()->startOfDay()->toDateString();
        $end_date   = $end_date ?: now()->endOfDay()->toDateString();

        // Lấy dữ liệu sản phẩm sắp hết hàng theo khoảng thời gian
        $products = $this->product->least_sold_products($start_date, $end_date);

        // Khởi tạo Spreadsheet và lấy sheet chính
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Tiêu đề báo cáo (dòng 1)
        $sheet->setCellValue('A1', 'Top sản phẩm bán tệ');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // 2. Hiển thị ngày bắt đầu và kết thúc (dòng 2)
        $displayStart = $start_date ? $start_date : 'Chưa chọn';
        $displayEnd   = $end_date ? $end_date : 'Chưa chọn';
        $sheet->setCellValue('A2', "Ngày bắt đầu: $displayStart");
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('D2', "Ngày kết thúc: $displayEnd");
        $sheet->mergeCells('D2:E2');
        $sheet->getStyle('A2:E2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2:E2')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Dòng 3 để tạo khoảng cách
        $sheet->mergeCells('A3:E3');

        // 3. Header bảng (dòng 4)
        $sheet->setCellValue('A4', 'STT');
        $sheet->setCellValue('B4', 'Tên Sản Phẩm');
        $sheet->setCellValue('C4', 'Ảnh ');
        $sheet->setCellValue('D4', 'Tổng Số Lượng');
        $headerRange = 'A4:E4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // Thiết lập độ rộng cột cố định cho cột C (ảnh)
        $sheet->getColumnDimension('C')->setWidth(20);
        foreach (['A', 'B', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 4. Ghi dữ liệu (từ dòng 5)
        $row = 5;
        if ($products->isEmpty()) {
            $sheet->setCellValue('A5', 'Không có sản phẩm nào trong khoảng thời gian này.');
            $sheet->mergeCells('A5:E5');
            $sheet->getStyle('A5')->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        } else {
            $stt = 1;
            foreach ($products as $product) {
                // STT và Tên sản phẩm
                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $product->name);
                $sheet->setCellValue('D' . $row, $product->total_sold);

                // Chèn ảnh vào cột C
                $imagePath = public_path($product->image_primary);
                if (file_exists($imagePath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setPath($imagePath);
                    $drawing->setName('Product Image');
                    $drawing->setDescription('Product Image');
                    $drawing->setCoordinates('C' . $row);
                    $drawing->setWidth(50);
                    $drawing->setHeight(50);
                    // Điều chỉnh offset để ảnh nằm gần giữa ô
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($row)->setRowHeight(60);
                } else {
                    $sheet->setCellValue('C' . $row, 'No image');
                }

                // Căn giữa nội dung của hàng
                $sheet->getStyle("A{$row}:D{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $row++;
            }
        }

        // 5. Xuất file Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'leastsoldproducts' . $start_date . '_to_' . $end_date . '.xlsx';

        return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
    public function exportLowStockProducts(Request $request)
    {
        // Lấy tham số ngày bắt đầu và kết thúc từ request
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');

        // Nếu không có ngày, dùng giá trị mặc định (ngày hôm nay)
        $start_date = $start_date ?: now()->startOfDay()->toDateString();
        $end_date   = $end_date ?: now()->endOfDay()->toDateString();

        // Lấy dữ liệu sản phẩm sắp hết hàng theo khoảng thời gian
        $products = $this->product->low_stock_products($start_date, $end_date);

        // Khởi tạo Spreadsheet và lấy sheet chính
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Tiêu đề báo cáo (dòng 1)
        $sheet->setCellValue('A1', 'Sản phẩm sắp hết hàng (dưới 10 sản phẩm)');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // 2. Hiển thị ngày bắt đầu và kết thúc (dòng 2)
        $displayStart = $start_date ? $start_date : 'Chưa chọn';
        $displayEnd   = $end_date ? $end_date : 'Chưa chọn';
        $sheet->setCellValue('A2', "Ngày bắt đầu: $displayStart");
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('D2', "Ngày kết thúc: $displayEnd");
        $sheet->mergeCells('D2:E2');
        $sheet->getStyle('A2:E2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2:E2')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Dòng 3 để tạo khoảng cách
        $sheet->mergeCells('A3:E3');

        // 3. Header bảng (dòng 4)
        $sheet->setCellValue('A4', 'STT');
        $sheet->setCellValue('B4', 'Tên Sản Phẩm');
        $sheet->setCellValue('C4', 'Ảnh Chính');
        $sheet->setCellValue('D4', 'Tổng Số Lượng');
        $headerRange = 'A4:E4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // Thiết lập độ rộng cột cố định cho cột C (ảnh)
        $sheet->getColumnDimension('C')->setWidth(20);
        foreach (['A', 'B', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 4. Ghi dữ liệu (từ dòng 5)
        $row = 5;
        if ($products->isEmpty()) {
            $sheet->setCellValue('A5', 'Không có sản phẩm nào trong khoảng thời gian này.');
            $sheet->mergeCells('A5:E5');
            $sheet->getStyle('A5')->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        } else {
            $stt = 1;
            foreach ($products as $product) {
                // STT và Tên sản phẩm
                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $product->name);
                $sheet->setCellValue('D' . $row, $product->total_quantity);

                // Chèn ảnh vào cột C
                $imagePath = public_path($product->image_primary);
                if (file_exists($imagePath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setPath($imagePath);
                    $drawing->setName('Product Image');
                    $drawing->setDescription('Product Image');
                    $drawing->setCoordinates('C' . $row);
                    $drawing->setWidth(50);
                    $drawing->setHeight(50);
                    // Điều chỉnh offset để ảnh nằm gần giữa ô
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($row)->setRowHeight(60);
                } else {
                    $sheet->setCellValue('C' . $row, 'No image');
                }

                // Căn giữa nội dung của hàng
                $sheet->getStyle("A{$row}:D{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $row++;
            }
        }

        // 5. Xuất file Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'low_stock_products_' . $start_date . '_to_' . $end_date . '.xlsx';

        return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
    public function exportProductsByCategory(Request $request)
    {
        // Lấy tham số ngày bắt đầu và kết thúc từ request
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');

        // Nếu không có ngày, dùng giá trị mặc định (ngày hôm nay)
        $start_date = $start_date ?: now()->startOfDay()->toDateString();
        $end_date   = $end_date ?: now()->endOfDay()->toDateString();

        // Lấy dữ liệu sản phẩm sắp hết hàng theo khoảng thời gian
        $products = $this->product->least_sold_products($start_date, $end_date);

        // Khởi tạo Spreadsheet và lấy sheet chính
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Tiêu đề báo cáo (dòng 1)
        $sheet->setCellValue('A1', 'Danh mục sản phẩm');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // 2. Hiển thị ngày bắt đầu và kết thúc (dòng 2)
        $displayStart = $start_date ? $start_date : 'Chưa chọn';
        $displayEnd   = $end_date ? $end_date : 'Chưa chọn';
        $sheet->setCellValue('A2', "Ngày bắt đầu: $displayStart");
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('D2', "Ngày kết thúc: $displayEnd");
        $sheet->mergeCells('D2:E2');
        $sheet->getStyle('A2:E2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2:E2')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Dòng 3 để tạo khoảng cách
        $sheet->mergeCells('A3:E3');

        // 3. Header bảng (dòng 4)
        $sheet->setCellValue('A4', 'STT');
        $sheet->setCellValue('B4', 'Tên Sản Phẩm');
        $sheet->setCellValue('C4', 'Ảnh ');
        $sheet->setCellValue('D4', 'Tổng Số Lượng');
        $headerRange = 'A4:E4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // Thiết lập độ rộng cột cố định cho cột C (ảnh)
        $sheet->getColumnDimension('C')->setWidth(20);
        foreach (['A', 'B', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 4. Ghi dữ liệu (từ dòng 5)
        $row = 5;
        if ($products->isEmpty()) {
            $sheet->setCellValue('A5', 'Không có sản phẩm nào trong khoảng thời gian này.');
            $sheet->mergeCells('A5:E5');
            $sheet->getStyle('A5')->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        } else {
            $stt = 1;
            foreach ($products as $product) {
                // STT và Tên sản phẩm
                $sheet->setCellValue('A' . $row, $stt++);
                $sheet->setCellValue('B' . $row, $product->name);
                $sheet->setCellValue('D' . $row, $product->total_sold);

                // Chèn ảnh vào cột C
                $imagePath = public_path($product->image_primary);
                if (file_exists($imagePath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setPath($imagePath);
                    $drawing->setName('Product Image');
                    $drawing->setDescription('Product Image');
                    $drawing->setCoordinates('C' . $row);
                    $drawing->setWidth(50);
                    $drawing->setHeight(50);
                    // Điều chỉnh offset để ảnh nằm gần giữa ô
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                    $sheet->getRowDimension($row)->setRowHeight(60);
                } else {
                    $sheet->setCellValue('C' . $row, 'No image');
                }

                // Căn giữa nội dung của hàng
                $sheet->getStyle("A{$row}:D{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $row++;
            }
        }

        // 5. Xuất file Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'categories_with_revenue' . $start_date . '_to_' . $end_date . '.xlsx';

        return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
    public function exportTopViewProducts()
    {
        // Lấy danh sách sản phẩm có trạng thái active, sắp xếp theo số view giảm dần, giới hạn 10 sản phẩm
        $products = $this->product->where('status', 'active')->where('view', '>', 0)->orderBy('view', 'desc')->take(10)->get();

        // Khởi tạo đối tượng Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Tiêu đề báo cáo (dòng 1)
        $sheet->setCellValue('A1', 'Top sản phẩm có nhiều view nhất');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // 2. Header bảng (dòng 3)
        $sheet->setCellValue('A3', 'STT');
        $sheet->setCellValue('B3', 'Tên Sản Phẩm');
        $sheet->setCellValue('C3', 'Ảnh');
        $sheet->setCellValue('D3', 'Số View');

        $headerRange = 'A3:D3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        $sheet->getColumnDimension('C')->setWidth(10);
        foreach (['A', 'B', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 3. Ghi dữ liệu bắt đầu từ dòng 4
        $row = 4;
        $stt = 1;
        foreach ($products as $product) {
            // STT và tên sản phẩm
            $sheet->setCellValue('A' . $row, $stt++);
            $sheet->setCellValue('B' . $row, $product->name);
            $sheet->setCellValue('D' . $row, $product->view);

            // Chèn ảnh vào cột C
            $imagePath = public_path($product->image_primary);
            if (file_exists($imagePath)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath($imagePath);
                $drawing->setName('Product Image');
                $drawing->setDescription($product->name);
                $drawing->setCoordinates('C' . $row);
                $drawing->setWidth(50);
                $drawing->setHeight(50);
                // Điều chỉnh offset để ảnh nằm gần giữa ô
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row)->setRowHeight(60);
            } else {
                $sheet->setCellValue('C' . $row, 'No image');
            }

            // Căn giữa nội dung của hàng
            $sheet->getStyle("A{$row}:D{$row}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $row++;
        }

        // 4. Xuất file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'top_view_products.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function exportTopCommentProducts()
    {
        // Lấy danh sách sản phẩm có trạng thái active, sắp xếp theo số view giảm dần, giới hạn 10 sản phẩm
        $products = $this->product->where('status', 'active')->where('view', '>', 0)->orderBy('view', 'desc')->take(10)->get();

        // Khởi tạo đối tượng Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Tiêu đề báo cáo (dòng 1)
        $sheet->setCellValue('A1', 'Top sản phẩm có nhiều view nhất');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        // 2. Header bảng (dòng 3)
        $sheet->setCellValue('A3', 'STT');
        $sheet->setCellValue('B3', 'Tên Sản Phẩm');
        $sheet->setCellValue('C3', 'Ảnh');
        $sheet->setCellValue('D3', 'Số View');

        $headerRange = 'A3:D3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        $sheet->getColumnDimension('C')->setWidth(10);
        foreach (['A', 'B', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 3. Ghi dữ liệu bắt đầu từ dòng 4
        $row = 4;
        $stt = 1;
        foreach ($products as $product) {
            // STT và tên sản phẩm
            $sheet->setCellValue('A' . $row, $stt++);
            $sheet->setCellValue('B' . $row, $product->name);
            $sheet->setCellValue('D' . $row, $product->view);

            // Chèn ảnh vào cột C
            $imagePath = public_path($product->image_primary);
            if (file_exists($imagePath)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath($imagePath);
                $drawing->setName('Product Image');
                $drawing->setDescription($product->name);
                $drawing->setCoordinates('C' . $row);
                $drawing->setWidth(50);
                $drawing->setHeight(50);
                // Điều chỉnh offset để ảnh nằm gần giữa ô
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row)->setRowHeight(60);
            } else {
                $sheet->setCellValue('C' . $row, 'No image');
            }

            // Căn giữa nội dung của hàng
            $sheet->getStyle("A{$row}:D{$row}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $row++;
        }

        // 4. Xuất file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'top_comment_products.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
    public function dashboardStatistics()
    {
        return view('admin.statistics.dashboard_statistics');
    }
    public function dailyStatistics(Request $request)
    {
        // Lấy ngày thống kê từ request, nếu không có thì dùng ngày hiện tại
        $date = $request->input('date', now()->toDateString());

        // Tổng số đơn hàng trong ngày
        $totalOrders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->count();

        // Lấy ID của các trạng thái cần thiết
        $deliveredStatusId = DB::table('order_statuses')
            ->where('name', 'Delivered')
            ->value('id');
        $pendingStatusId   = DB::table('order_statuses')
            ->where('name', 'Pending')
            ->value('id');
        $cancelledStatusId = DB::table('order_statuses')
            ->where('name', 'Cancelled')
            ->value('id');

        // Tổng doanh thu trong ngày (chỉ tính đơn Delivered)
        $totalRevenue = DB::table('orders')
            ->whereDate('created_at', $date)
            ->where('id_order_status', $deliveredStatusId)
            ->sum('total');

        // Số đơn hàng theo từng trạng thái trong ngày
        $pendingOrders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->where('id_order_status', $pendingStatusId)
            ->count();

        $deliveredOrders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->where('id_order_status', $deliveredStatusId)
            ->count();

        $cancelledOrders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->where('id_order_status', $cancelledStatusId)
            ->count();

        // Giả sử "Completed Orders" là các đơn Delivered
        $completedOrders = $deliveredOrders;

        // Dữ liệu cho biểu đồ: đếm số đơn hàng theo trạng thái (trong ngày)
        $statistics = DB::table('orders')
            ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
            ->select('order_statuses.name as status', DB::raw('COUNT(orders.id) as total'))
            ->whereDate('orders.created_at', $date)
            ->groupBy('order_statuses.name')
            ->get();

        // Thống kê sản phẩm bán chạy nhất trong ngày
        $productsSales = DB::table('order_details')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->whereDate('orders.created_at', $date)
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // Top 10 người mua nhiều nhất trong ngày (chỉ tính các đơn Delivered)
        $topCustomers = DB::table('orders')
            ->select('id_user', 'user_name', DB::raw('SUM(total) as total_purchase'))
            ->whereDate('created_at', $date)
            ->where('id_order_status', $deliveredStatusId)
            ->groupBy('id_user', 'user_name')
            ->orderByDesc('total_purchase')
            ->limit(10)
            ->get();

        return view('admin.statistics.order_statistics', compact(
            'date',
            'totalOrders',
            'pendingOrders',
            'deliveredOrders',
            'cancelledOrders',
            'completedOrders',
            'totalRevenue',
            'statistics',
            'productsSales',
            'topCustomers'
        ));
    }

    public function weeklyStatistics(Request $request)
    {
        // 1. Lấy ngày bắt đầu và ngày kết thúc từ request, nếu không có sẽ mặc định là đầu và cuối tuần hiện tại
        $startDateInput = $request->input('start_date', now()->startOfWeek()->toDateString());
        $endDateInput   = $request->input('end_date', now()->endOfWeek()->toDateString());

        $startDate = Carbon::parse($startDateInput);
        $endDate   = Carbon::parse($endDateInput);
        $dateRange = [$startDate->toDateTimeString(), $endDate->toDateTimeString()];

        // 2. Lấy ID của các trạng thái cần thiết
        $paidStatusId = DB::table('order_statuses')->where('name', 'Paid')->value('id');
        $deliveredStatusId = DB::table('order_statuses')->where('name', 'Delivered')->value('id');
        $cancelledStatusId = DB::table('order_statuses')->where('name', 'Cancelled')->value('id');

        // 3. Tính toán các chỉ số tổng quan

        // Tổng doanh thu (chỉ tính đơn hàng có trạng thái Delivered)
        $totalRevenue = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->sum('total');

        // Tổng số đơn hàng (tất cả)
        $allTotalOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->count();

        // Số đơn hàng theo từng trạng thái
        $paidOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $paidStatusId)
            ->count();

        $deliveredOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->count();

        $cancelledOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $cancelledStatusId)
            ->count();

        // Tổng số đơn hàng thành công (Paid & Delivered)
        $totalSuccessfulOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->whereIn('id_order_status', [$paidStatusId, $deliveredStatusId])
            ->count();

        // Giá trị trung bình mỗi đơn hàng (AOV) dựa trên doanh thu Delivered
        $averageOrderValue = $deliveredOrders > 0 ? $totalRevenue / $deliveredOrders : 0;

        // 4. Dữ liệu cho biểu đồ theo ngày trong khoảng thời gian được chọn
        $rawDailyStats = DB::table('orders')
            ->select(
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN 1 ELSE 0 END) as delivered_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $cancelledStatusId . ' THEN 1 ELSE 0 END) as cancelled_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN total ELSE 0 END) as day_revenue')
            )
            ->whereBetween('created_at', $dateRange)
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get()
            ->keyBy('order_date');

        // Tạo danh sách các ngày trong khoảng thời gian được chọn
        $period = CarbonPeriod::create($startDate, $endDate);
        $dailyStats = [];
        foreach ($period as $date) {
            $day = $date->toDateString();
            if ($rawDailyStats->has($day)) {
                $dailyStats[] = [
                    'order_date'       => $day,
                    'total_orders'     => $rawDailyStats[$day]->total_orders,
                    'delivered_orders' => $rawDailyStats[$day]->delivered_orders,
                    'cancelled_orders' => $rawDailyStats[$day]->cancelled_orders,
                    'day_revenue'      => $rawDailyStats[$day]->day_revenue,
                ];
            } else {
                $dailyStats[] = [
                    'order_date'       => $day,
                    'total_orders'     => 0,
                    'delivered_orders' => 0,
                    'cancelled_orders' => 0,
                    'day_revenue'      => 0,
                ];
            }
        }

        // 5. Top 20 sản phẩm bán chạy trong khoảng thời gian được chọn
        $productsSales = DB::table('order_details')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId)
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(20)
            ->get();

        // 6. Top 20 khách hàng mua nhiều nhất trong khoảng thời gian (theo tổng giá trị mua hàng, chỉ Delivered)
        $topCustomers = DB::table('orders')
            ->select(
                'id_user',
                'user_name',
                DB::raw('SUM(total) as total_purchase')
            )
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->groupBy('id_user', 'user_name')
            ->orderByDesc('total_purchase')
            ->limit(20)
            ->get();

        // Lấy chi tiết các sản phẩm mà mỗi khách hàng mua (cho các đơn hàng Delivered)
        $customerProducts = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.id_order')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('orders.id_user', 'products.name as product_name', DB::raw('SUM(order_details.quantity) as quantity'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId)
            ->groupBy('orders.id_user', 'products.name')
            ->get();

        $customerProductsGrouped = $customerProducts->groupBy('id_user');

        return view('admin.statistics.weekly_statistics', compact(
            'startDate',
            'endDate',
            'allTotalOrders',
            'totalRevenue',
            'paidOrders',
            'deliveredOrders',
            'cancelledOrders',
            'averageOrderValue',
            'dailyStats',
            'productsSales',
            'totalSuccessfulOrders',
            'topCustomers',
            'customerProductsGrouped',
        ));
    }

    public function monthlyStatistics(Request $request)
    {
        // 1. Xác định khoảng thời gian của tháng
        $monthInput = $request->input('month', now()->format('Y-m'));
        $selectedMonth = Carbon::parse($monthInput . '-01');
        $startOfMonth = $selectedMonth->copy()->startOfMonth();
        $endOfMonth   = $selectedMonth->copy()->endOfMonth();
        $dateRange = [$startOfMonth->toDateTimeString(), $endOfMonth->toDateTimeString()];

        // 2. Lấy ID trạng thái
        $deliveredStatusId = DB::table('order_statuses')->where('name', 'Delivered')->value('id');
        $cancelledStatusId = DB::table('order_statuses')->where('name', 'Cancelled')->value('id');
        $paidStatusId = DB::table('order_statuses')->where('name', 'Paid')->value('id');

        // 3. Tính toán các chỉ số tổng quan

        $totalRevenue = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->sum('total');

        $allTotalOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->count();

        // Tính số đơn hàng theo từng trạng thái
        $paidOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $paidStatusId)
            ->count();

        $deliveredOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->count();

        $cancelledOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $cancelledStatusId)
            ->count();

        $totalSuccessfulOrders = $deliveredOrders; // Chỉ tính Delivered

        $averageOrderValue = $deliveredOrders > 0 ? $totalRevenue / $deliveredOrders : 0;

        // 4. Dữ liệu cho biểu đồ theo ngày trong tháng
        $rawDailyStats = DB::table('orders')
            ->select(
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN 1 ELSE 0 END) as delivered_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $cancelledStatusId . ' THEN 1 ELSE 0 END) as cancelled_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN total ELSE 0 END) as day_revenue')
            )
            ->whereBetween('created_at', $dateRange)
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get()
            ->keyBy('order_date');

        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
        $dailyStats = [];
        foreach ($period as $date) {
            $day = $date->toDateString();
            if ($rawDailyStats->has($day)) {
                $dailyStats[] = [
                    'order_date'       => $day,
                    'total_orders'     => $rawDailyStats[$day]->total_orders,
                    'delivered_orders' => $rawDailyStats[$day]->delivered_orders,
                    'cancelled_orders' => $rawDailyStats[$day]->cancelled_orders,
                    'day_revenue'      => $rawDailyStats[$day]->day_revenue,
                ];
            } else {
                $dailyStats[] = [
                    'order_date'       => $day,
                    'total_orders'     => 0,
                    'delivered_orders' => 0,
                    'cancelled_orders' => 0,
                    'day_revenue'      => 0,
                ];
            }
        }

        // 5. Top 10 sản phẩm bán chạy
        $productsSales = DB::table('order_details')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId) // Chỉ lấy đơn đã giao hàng
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(50)
            ->get();


        // 6. Top 20 người mua nhiều nhất (chi tiết theo Delivered)
        $topCustomers = DB::table('orders')
            ->select(
                'id_user',
                'user_name',
                DB::raw('SUM(total) as total_purchase')
            )
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId) // Chỉ lấy đơn đã giao hàng
            ->groupBy('id_user', 'user_name')
            ->orderByDesc('total_purchase')
            ->limit(50)
            ->get();


        $customerProducts = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.id_order')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('orders.id_user', 'products.name as product_name', DB::raw('SUM(order_details.quantity) as quantity'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId) // Chỉ lấy đơn đã giao hàng
            ->groupBy('orders.id_user', 'products.name')
            ->get();

        $customerProductsGrouped = $customerProducts->groupBy('id_user');

        return view('admin.statistics.monthly_statistics', compact(
            'selectedMonth',
            'startOfMonth',
            'endOfMonth',
            'allTotalOrders',
            'totalRevenue',
            'paidOrders',
            'deliveredOrders',
            'cancelledOrders',
            'averageOrderValue',
            'dailyStats',
            'productsSales',
            'totalSuccessfulOrders',
            'topCustomers',
            'customerProductsGrouped'
        ));
    }

    public function yearlyStatistics(Request $request)
    {
        // 1. Xác định năm cần thống kê (mặc định là năm hiện tại)
        $yearInput = $request->input('year', now()->format('Y'));
        $selectedYear = $yearInput; // ví dụ "2025"
        $startOfYear = Carbon::parse($selectedYear . '-01-01')->startOfDay();
        $endOfYear   = Carbon::parse($selectedYear . '-12-31')->endOfDay();
        $dateRange = [$startOfYear->toDateTimeString(), $endOfYear->toDateTimeString()];

        // 2. Lấy ID các trạng thái cần thiết
        $deliveredStatusId = DB::table('order_statuses')->where('name', 'Delivered')->value('id');
        $cancelledStatusId = DB::table('order_statuses')->where('name', 'Cancelled')->value('id');

        // 3. Tính toán các chỉ số tổng quan
        // Tổng doanh thu (chỉ tính đơn Delivered)
        $totalRevenue = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->sum('total');

        // Tổng số đơn hàng (tất cả) – tuy nhiên, nếu thống kê theo đơn thành công, bạn có thể chỉ lấy Delivered
        $allTotalOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->count();

        // Số đơn Delivered và Cancelled
        $deliveredOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->count();

        $cancelledOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $cancelledStatusId)
            ->count();

        // Tổng số đơn thành công (chỉ Delivered)
        $totalSuccessfulOrders = $deliveredOrders;

        // Giá trị trung bình mỗi đơn (AOV) dựa trên đơn Delivered
        $averageOrderValue = $deliveredOrders > 0 ? $totalRevenue / $deliveredOrders : 0;

        // 4. Dữ liệu cho biểu đồ theo tháng trong năm
        $rawMonthlyStats = DB::table('orders')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN 1 ELSE 0 END) as delivered_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $cancelledStatusId . ' THEN 1 ELSE 0 END) as cancelled_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN total ELSE 0 END) as month_revenue')
            )
            ->whereBetween('created_at', $dateRange)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy('month');

        $monthlyStats = [];
        for ($m = 1; $m <= 12; $m++) {
            if ($rawMonthlyStats->has($m)) {
                $monthlyStats[$m] = [
                    'month'           => $m,
                    'total_orders'    => $rawMonthlyStats[$m]->total_orders,
                    'delivered_orders' => $rawMonthlyStats[$m]->delivered_orders,
                    'cancelled_orders' => $rawMonthlyStats[$m]->cancelled_orders,
                    'month_revenue'   => $rawMonthlyStats[$m]->month_revenue,
                ];
            } else {
                $monthlyStats[$m] = [
                    'month'           => $m,
                    'total_orders'    => 0,
                    'delivered_orders' => 0,
                    'cancelled_orders' => 0,
                    'month_revenue'   => 0,
                ];
            }
        }

        // Tính % tăng giảm doanh thu so với tháng trước
        $monthlyStatsWithComparison = [];
        $prevRevenue = null;
        foreach ($monthlyStats as $m => $data) {
            if (is_null($prevRevenue) || $prevRevenue == 0) {
                $data['pct_change'] = null;
            } else {
                $data['pct_change'] = (($data['month_revenue'] - $prevRevenue) / $prevRevenue) * 100;
            }
            $prevRevenue = $data['month_revenue'];
            $monthlyStatsWithComparison[] = $data;
        }

        // 5. Top 10 sản phẩm bán chạy trong năm (chỉ tính đơn Delivered)
        $productsSales = DB::table('order_details')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId)
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // 6. Top 20 người mua nhiều nhất trong năm (chỉ tính đơn Delivered)
        $topCustomers = DB::table('orders')
            ->select('id_user', 'user_name', DB::raw('SUM(total) as total_purchase'))
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->groupBy('id_user', 'user_name')
            ->orderByDesc('total_purchase')
            ->limit(20)
            ->get();

        // 7. Chi tiết sản phẩm mỗi khách hàng đã mua (chỉ tính các đơn Delivered)
        $customerProducts = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.id_order')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('orders.id_user', 'products.name as product_name', DB::raw('SUM(order_details.quantity) as quantity'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId)
            ->groupBy('orders.id_user', 'products.name')
            ->get();
        $customerProductsGrouped = $customerProducts->groupBy('id_user');

        return view('admin.statistics.yearly_statistics', compact(
            'selectedYear',
            'startOfYear',
            'endOfYear',
            'allTotalOrders',
            'totalRevenue',
            'deliveredOrders',
            'cancelledOrders',
            'averageOrderValue',
            'monthlyStatsWithComparison',
            'productsSales',
            'totalSuccessfulOrders',
            'topCustomers',
            'customerProductsGrouped'
        ));
    }
}
