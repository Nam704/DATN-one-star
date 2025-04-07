<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{

    use HasFactory, SoftDeletes;


    protected $fillable = [
        'id',
        'name',
        'id_brand',
        'id_category',
        'description',
        'image_primary',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function Category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }


    public function Brand()
    {
        return $this->belongsTo(Brand::class, 'id_brand');
    }



    public static function scopeList($query)
    {
        return $query->select('id', 'name', 'status')
            ->latest('id');
    }
    public function listActive()
    {
        return self::with([
            'Category:id,name,status',
            'Brand:id,name,status',
            'variants:id_product,quantity,price'
        ])
            ->addSelect([
                'total_quantity' => Product_variant::selectRaw('SUM(quantity)')
                    ->whereColumn('id_product', 'products.id'),
                'min_price' => Product_variant::selectRaw('MIN(price)')
                    ->whereColumn('id_product', 'products.id'),
                'max_price' => Product_variant::selectRaw('MAX(price)')
                    ->whereColumn('id_product', 'products.id')
            ])
            ->orderBy('id', 'desc');
    }

    public static function scopeTotal($query)
    {
        return $query->where('status', 'active');
    }
    public function variants()
    {
        return $this->hasMany(Product_variant::class, 'id_product');
    }
    public function product_albums()
    {
        return $this->hasMany(Product_albums::class, 'id_product');
    }
    public function getProductWithDetails()
    {
        return $this->load([
            'variants' => function ($query) {
                $query->select('id', 'id_product', 'sku', 'status', 'quantity', 'price')
                    ->with([
                        'images' => function ($query) {
                            $query->select('id', 'id_product_variant', 'url');
                        },
                        'attributeValues'
                    ]);
            },
            'category:id,name',
            'brand:id,name',
            'product_albums:id,id_product,image_path',

        ])->append('attributes')
            ->select('id', 'name', 'id_brand', 'id_category', 'description', 'image_primary', 'status');
    }   

//     public static function getProductWithDetails($id)
// {
//     return self::where('id', $id)->with([
//         'variants' => function ($query) {
//             $query->select('id', 'id_product', 'sku', 'status', 'quantity', 'price')
//                 ->with([
//                     'images' => function ($query) {
//                         $query->select('id', 'id_product_variant', 'url');
//                     },
//                     'attributeValues'
//                 ]);
//         },
//         'category:id,name',
//         'brand:id,name',
//         'product_albums:id,id_product,image_path',
//     ])->first()->append('attributes');
// }

    public function getPriceRange()
    {
        return $this->variants()
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();
    }
    function quantity()
    {
        return $this->variants()->sum('quantity');
    }
    public function getAttributesAttribute()
    {
        $attributes = [];

        // Kiểm tra nếu product có variants
        if (!$this->relationLoaded('variants')) {
            return $attributes;
        }

        // Duyệt qua tất cả các variants để lấy attributes
        foreach ($this->variants as $variant) {
            if (!$variant->relationLoaded('attributeValues')) {
                continue;
            }

            foreach ($variant->attributeValues as $attr) {
                $attributes[$attr->attribute_name]['name'] = $attr->attribute_name;
                $attributes[$attr->attribute_name]['id'] = $attr->attribute_id;
                $attributes[$attr->attribute_name]['values'][$variant->id][$attr->id] = $attr->value;
            }
        }

        // Loại bỏ các giá trị trùng lặp
        foreach ($attributes as &$attr) {
            foreach ($attr['values'] as $variant_id => &$values) {
                $values = array_unique($values);
            }
        }

        return array_values($attributes);
    }

    public function top_sale_products($start_date, $end_date)
      {
          if ($start_date && $end_date) {
              return DB::table('products')
                  ->join('product_variants', 'products.id', '=', 'product_variants.id_product')
                  ->join('order_details', 'product_variants.id', '=', 'order_details.id_variant')
                  ->join('orders', 'order_details.id_order', '=', 'orders.id')
                  ->where('orders.id_order_status', '=', 7)
                  ->whereBetween('orders.created_at', [$start_date, $end_date])
                  ->select(
                      'products.id',
                      'products.name',
                      'products.image_primary',
                      DB::raw('SUM(order_details.quantity) as total_sold')
                  )
                  ->groupBy('products.id', 'products.name', 'products.image_primary')
                  ->orderBy('total_sold', 'desc')
                  ->limit(10)
                  ->get();
          }
  
          return DB::table('products')
              ->join('product_variants', 'products.id', '=', 'product_variants.id_product')
              ->join('order_details', 'product_variants.id', '=', 'order_details.id_variant')
              ->join('orders', 'order_details.id_order', '=', 'orders.id')
              ->where('orders.id_order_status', '=', 7)
              ->select(
                  'products.id',
                  'products.name',
                  'products.image_primary',
                  DB::raw('SUM(order_details.quantity) as total_sold')
              )
              ->groupBy('products.id', 'products.name', 'products.image_primary')
              ->orderBy('total_sold', 'desc')
              ->limit(10)
              ->get();
      }
    public function top_sale_products_today($start_date, $end_date)
    {
        if ($start_date && $end_date) {
            return DB::table('products')
                ->join('product_variants', 'products.id', '=', 'product_variants.id_product')
                ->join('order_details', 'product_variants.id', '=', 'order_details.id_variant')
                ->join('orders', 'order_details.id_order', '=', 'orders.id')
                ->where('orders.id_order_status', '=', 7)
                ->whereBetween('orders.created_at', [$start_date, $end_date])
                ->select(
                    'products.id',
                    'products.name',
                    'products.image_primary',
                    DB::raw('SUM(order_details.quantity) as total_sold')
                )
                ->groupBy('products.id', 'products.name', 'products.image_primary')
                ->orderBy('total_sold', 'desc')
                ->limit(10)
                ->get();
        }

        return DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.id_product')
            ->join('order_details', 'product_variants.id', '=', 'order_details.id_variant')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->where('orders.id_order_status', '=', 7)
            ->select(
                'products.id',
                'products.name',
                'products.image_primary',
                DB::raw('SUM(order_details.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.image_primary')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();
    }
    public function least_sold_products_today($start_date = null, $end_date = null)
{
    $query = DB::table('products')
        ->leftJoin('product_variants', 'products.id', '=', 'product_variants.id_product')
        ->leftJoin('order_details', 'product_variants.id', '=', 'order_details.id_variant')
        ->leftJoin('orders', 'order_details.id_order', '=', 'orders.id')
        ->select(
            'products.id',
            'products.name',
            'products.image_primary',
            DB::raw("
                COALESCE(SUM(
                    CASE 
                        WHEN orders.id_order_status = 7
                        " . ($start_date && $end_date ? " AND orders.created_at BETWEEN '$start_date' AND '$end_date'" : "") . "
                        THEN order_details.quantity 
                        ELSE 0 
                    END
                ), 0) as total_sold
            ")
        )
        ->where('products.status', 'active')
        ->groupBy('products.id', 'products.name', 'products.image_primary')
        ->orderBy('total_sold', 'asc')
        ->limit(10);

    return $query->get();
}


public function top_view_product($start_date, $end_date)
    {
        return DB::table('products')
            ->where('status', '=', 'active')
            ->select(
                'id',
                'name',
                'image_primary',
                'view'
            )
            ->orderBy('view', 'desc')
            ->limit(10)
            ->get();
    }


    public function least_sold_products($start_date, $end_date)
    {
        return DB::table('products')
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.id_product')
            ->leftJoin('order_details', 'product_variants.id', '=', 'order_details.id_variant')
            ->leftJoin('orders', 'order_details.id_order', '=', 'orders.id')
            ->select(
                'products.id',
                'products.name',
                'products.image_primary',
                DB::raw('COALESCE(SUM(CASE WHEN orders.id_order_status =  7 AND orders.created_at BETWEEN "' . $start_date . '" AND "' . $end_date . '" THEN order_details.quantity ELSE 0 END), 0) as total_sold')
            )
            ->where('products.status', '=', 'active')
            ->where('product_variants.status', '=', 'active')
            ->where('products.created_at', '<=', $end_date) // Chỉ lấy sản phẩm đã tồn tại trước hoặc tại $endDate
            ->groupBy('products.id', 'products.name', 'products.image_primary')
            ->orderBy('total_sold', 'asc')
            ->get();
    }
    
    
    
    public function low_stock_products($start_date, $end_date)
{
    $query = DB::table('products')
        ->join('product_variants', 'products.id', '=', 'product_variants.id_product')
        ->select(
            'products.id',
            'products.name',
            'products.image_primary',
            DB::raw('SUM(product_variants.quantity) as total_quantity')
        )
        ->where('products.status', '=', 'active')
        ->where('product_variants.status', '=', 'active')
        ->whereBetween('product_variants.updated_at', [$start_date, $end_date])
        ->groupBy('products.id', 'products.name', 'products.image_primary')
        ->havingRaw('SUM(product_variants.quantity) < 10')
        ->orderBy('total_quantity', 'asc');

    return $query->get(); 
}

  
      // sản phẩm đã bán

    public function productSold($start_date, $end_date)
    {
        if ($start_date && $end_date) {
            return DB::table('products')
                ->join('product_variants', 'products.id', '=', 'product_variants.id_product')
                ->join('order_details', 'product_variants.id', '=', 'order_details.id_variant')
                ->join('orders', 'order_details.id_order', '=', 'orders.id')
                ->where('orders.id_order_status', '=', 7)
                ->whereBetween('orders.created_at', [$start_date, $end_date])
                ->select(
                    'products.id',
                    'products.name',
                    'products.image_primary',
                    DB::raw('SUM(order_details.quantity) as total_sold')
                )
                ->groupBy('products.id', 'products.name', 'products.image_primary')
                ->orderBy('total_sold', 'desc')
                ->get();
        }
        return DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.id_product')
            ->join('order_details', 'product_variants.id', '=', 'order_details.id_variant')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->where('orders.id_order_status', '=', 7)
            ->select(
                'products.id',
                'products.name',
                'products.image_primary',
                DB::raw('SUM(order_details.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.image_primary')
            ->orderBy('total_sold', 'desc')
            ->get();
    }



}
