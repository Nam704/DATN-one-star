<div class="tab-pane fade" id="address">
    <h3>Addresses</h3>

    <div class="table-responsive">
        <table class="table" id="address-list-table">
            <thead>
                <tr>
                    <th>Address</th>
                    <th>Default</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($addresses as $address)
                    <tr id="address-{{ $address->id }}">
                        <td>
                            {{ $address->address_detail }}
                            @if (isset($address->id_ward) && isset($wardData[$address->id_ward]))
                                <?php $ward = $wardData[$address->id_ward]; ?>
                                , {{ $ward->name }}
                                , {{ $ward->district->name }}
                                , {{ $ward->district->province->name }}
                            @endif
                        </td>
                        <td>{{ $address->is_default ? 'Yes' : 'No' }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-address" data-id="{{ $address->id }}"
                                data-detail="{{ $address->address_detail }}" data-ward="{{ $address->ward_id ?? '' }}"
                                data-district="{{ isset($address->ward_id) && isset($wardData[$address->ward_id]) ? $wardData[$address->ward_id]->district->id : '' }}"
                                data-province="{{ isset($address->ward_id) && isset($wardData[$address->ward_id]) ? $wardData[$address->ward_id]->district->province->id : '' }}"
                                data-default="{{ $address->is_default }}">
                                Edit
                            </button>
                            @if (!$address->is_default)
                                <button class="btn btn-sm btn-success set-default-address" data-id="{{ $address->id }}">
                                    Set Default
                                </button>
                            @endif
                            <button class="btn btn-sm btn-danger delete-address" data-id="{{ $address->id }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No addresses found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mb-4">
        <h4>Add New Address</h4>
        <div id="address-alert"></div>

        <div id="address-form" class="address-form p-3 border rounded row">

            <input type="hidden" id="address_id" value="">

            <div class="form-group mb-3 col-md-4">
                <label for="province">Province/City</label>
                <select id="province" class="form-control">
                    <option value="">Select Province/City</option>
                </select>
            </div>

            <div class="form-group mb-3 col-md-4">
                <label for="district">District</label>
                <select id="district" class="form-control" disabled>
                    <option value="">Select District</option>
                </select>
            </div>

            <div class="form-group mb-3 col-md-4">
                <label for="ward">Ward</label>
                <select id="ward" class="form-control" disabled>
                    <option value="">Select Ward</option>
                </select>
            </div>

            <div class="form-group mb-3 col-md-12">
                <label for="address_detail">Address Detail</label>
                <input type="text" id="address_detail" class="form-control"
                    placeholder="Enter your street, house number, etc.">
            </div>

            <div class="form-check mb-3 col-md-12">
                <input type="checkbox" id="is_default" class="form-check-input">
                <label class="form-check-label" for="is_default">Set as default address</label>
            </div>

            <div class="">
                <button id="save_address" class="btn btn-primary">Save Address</button>
                <button id="cancel-edit" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
    </div>
</div>
