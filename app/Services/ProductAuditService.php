<?php

namespace App\Services;

use App\Models\Product_audit;

class ProductAuditService
{

    public function createAudit(array $data)
    {
        return Product_audit::create($data);
    }


    public function getAllAudits()
    {
        return Product_audit::with(['user', 'productVariant'])->get();
    }
    public function getAuditsByUser(int $userId)
    {
        return Product_audit::with(['user', 'productVariant'])
            ->where('id_user', $userId)
            ->get();
    }


    public function updateAudit(int $id, array $data)
    {
        $audit = Product_audit::findOrFail($id);
        $audit->update($data);
        return $audit;
    }
}
