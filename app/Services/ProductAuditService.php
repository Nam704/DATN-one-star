<?php

namespace App\Services;

use App\Models\Product_audit;

class ProductAuditService
{

    public function createAudit(array $data)
    {
        return Product_audit::create($data);
    }


    public function getAuditApproved()
    {
        return Product_audit::WithProductAndUserSummary()->paginate(100);
    }
    public function getAuditPending()
    {
        return Product_audit::WithProductAndUserSummarPending()->paginate(100);
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
    public function deleteAudit(int $id)
    {
        $audit = Product_audit::where('id_import', $id)->first();
        if (!$audit) {
            return response()->json([
                'message' => 'Audit not found',
            ], 404);
        }
        $audit->delete();
        return response()->json([
            'message' => 'Audit deleted successfully',
            'audit' => $audit
        ], 200);
    }
}
