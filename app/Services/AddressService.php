<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Facades\DB;

class AddressService
{
    protected $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }
    public function getFullAddress($model, $modelId)
    {
        // return [$model, $modelId];
        $object = $model->find($modelId);
        if (!$object) {
            return null;
        }
        return $this->address->getAddresses($model, $modelId);
    }
    public function getAddress($model, $modelId, $addressId)
    {
        $object = $model->find($modelId);
        if (!$object) {
            return null;
        }
        return $this->address->getAddress($model, $modelId, $addressId);
    }
    public function storeAddress($model, $modelId, $addressData)
    {
        $object = $model->find($modelId);
        if (!$object) {
            return null;
        }
        // DB::beginTransaction();
        $address = $object->addresses()->create($addressData);

        return $address;
    }
    public function updateAddress($model, $modelId, $addressId, $addressData)
    {

        $object = $model->find($modelId);
        if (!$object) {
            return null;
        }

        $address = $object->addresses()

            ->where('id', $addressId)
            ->first();

        if ($address) {
            $address->update($addressData);
        } else {
            return null;
        }

        return $address;
    }

    public function setDefaultAddress($model, $modelId, $addressId)
    {

        $object = $model->find($modelId);
        if (!$object) {
            return null;
        }
        return $object->addresses()
            ->where('is_default', true)
            ->where('id', '!=', $addressId)
            ->update(['is_default' => false]);
    }
    public function deleteAddressesByModel($model, $modelId, $addressId = null)
    {

        $object = $model->find($modelId);
        if (!$object) {
            return null;
        }

        if ($addressId) {
            return $object->addresses()
                ->where('id', '=', $addressId)
                ->where('is_default', false)
                ->delete();
        } else {
            return $object->addresses()
                ->where('is_default', false)
                ->delete();
        }
    }
}
