<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Import extends Model
{
    use HasFactory;
    // use SoftDeletes;
    protected $fillable = [
        'id_supplier', 'name',
        'import_date',
        'total_amount',
        'note',

    ];
    public function scopeList($query)
    {
        return $query->join('suppliers', 'imports.id_supplier', '=', 'suppliers.id')
            ->select('imports.id', 'imports.id_supplier', 'imports.name', 'imports.import_date', 'imports.total_amount', 'imports.note', 'suppliers.name as supplier_name')
            ->orderBy('imports.id', 'DESC');
    }
    function scopeListApproved()
    {
        return $this->join('suppliers', 'imports.id_supplier', '=', 'suppliers.id')
            ->select(
                'imports.id',
                'imports.id_supplier',
                'imports.status',
                'imports.name',
                'imports.import_date',
                'imports.total_amount',
                'imports.note',
                'suppliers.name as supplier_name'
            )
            ->where('imports.status', "approved")
            ->orderBy('imports.id', 'DESC');
    }
    function scopeListPending()
    {
        return $this->join('suppliers', 'imports.id_supplier', '=', 'suppliers.id')
            ->select(
                'imports.id',
                'imports.id_supplier',
                'imports.name',
                'imports.import_date',
                'imports.total_amount',
                'imports.status',
                'imports.note',
                'suppliers.name as supplier_name'
            )
            ->where('imports.status', "pending")
            ->orderBy('imports.id', 'DESC');
    }
    function scopeListReject()
    {
        return $this->join('suppliers', 'imports.id_supplier', '=', 'suppliers.id')
            ->select(
                'imports.id',
                'imports.status',
                'imports.id_supplier',
                'imports.name',
                'imports.import_date',
                'imports.total_amount',
                'imports.note',
                'suppliers.name as supplier_name'
            )
            ->where('imports.status', "rejected")
            ->orderBy('imports.id', 'DESC');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }
    public function import_details()
    {
        return $this->hasMany(Import_detail::class, 'id_import', 'id');
    }
}
