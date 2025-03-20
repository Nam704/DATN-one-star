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

    public function top_10_products( )
    {
        return DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.id_product')
            ->join('order_details', 'product_variants.id', '=', 'order_details.id_variant')  // Changed column name
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->where('orders.id_order_status', '=', 4)
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

    public function least_sold_products()
    {
        return DB::table('products')
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.id_product')
            ->leftJoin('order_details', 'product_variants.id', '=', 'order_details.id_variant') 
            // ->leftJoin('orders', 'order_details.id_order', '=', 'orders.id')
            // ->where('products.status', '=', 'active')
            // ->where('orders.id_order_status', '=', 4) //đặt hàng thành công
            ->select(
                'products.id',
                'products.name',
                'products.image_primary',
                DB::raw('COALESCE(SUM(order_details.quantity), 0) as total_sold')
            )
            ->where('products.status', '=', 'active')
            ->groupBy('products.id', 'products.name', 'products.image_primary')
            ->orderBy('total_sold', 'asc')
            ->limit(8)
            ->get();
    }

    public function low_stock_products()
    {
        return DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.id_product')
            ->select(
                'products.id',
                'products.name',
                'products.image_primary',
                DB::raw('SUM(product_variants.quantity) as total_quantity')
            )
            ->where('products.status', '=', 'active')
            ->where('product_variants.status', '=', 'active')
            ->groupBy('products.id', 'products.name', 'products.image_primary')
            ->having('total_quantity', '<', 10)
            ->orderBy('total_quantity', 'asc')
            ->get();
    }




}
