<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['address_detail', 'is_default', 'id_ward'];

    // Quan hệ polymorphic
    public function addressable()
    {
        return $this->morphTo(); // Định nghĩa quan hệ polymorphic
    }
    public static function getAddressDetailsByWard($idWard)
    {
        return DB::table('addresses as a')
            ->join('wards as w', 'a.id_ward', '=', 'w.id')
            ->join('districts as d', 'w.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->where('a.id_ward', '=', $idWard)
            ->select('a.address_detail', 'w.name as ward_name', 'd.name as district_name', 'p.name as province_name')
            ->get();
    }
}
