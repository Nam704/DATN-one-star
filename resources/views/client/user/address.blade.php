<div class="tab-pane fade" id="address">
    <h3>Địa chỉ giao hàng</h3>

    <div class="table-responsive">
        <table class="table" id="address-list-table">
            <thead>
                <tr>
                    <th>Địa chỉ</th>
                    <th>Đặt mặc định</th>
                    <th>Hành động</th>
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
                                data-ward="{{ $address->id_ward ?? '' }}">
                                Sửa
                            </button>
                            @if (!$address->is_default)
                                <button class="btn btn-sm btn-success set-default-address" data-id="{{ $address->id }}">
                                   Đặt mặc định
                                </button>
                            @endif
                            <button class="btn btn-sm btn-danger delete-address" data-id="{{ $address->id }}">
                                Xóa
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Không có địa chỉ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mb-4">
        <h4>Thêm địa chỉ mới</h4>
        <div id="address-alert"></div>

        <div id="address-form" class="address-form p-3 border rounded row">

            <input type="hidden" id="address_id" value="">

            <div class="form-group mb-3 col-md-4">
                <label for="province">Thành phố/Tỉnh</label>
                <select id="province" class="form-control">

                </select>
            </div>

            <div class="form-group mb-3 col-md-4">
                <label for="district">Quận/huyện</label>
                <select id="district" class="form-control">

                </select>
            </div>

            <div class="form-group mb-3 col-md-4">
                <label for="ward">Phường/xã</label>
                <select id="ward" class="form-control">

                </select>
            </div>

            <div class="form-group mb-3 col-md-12">
                <label for="address_detail">Địa chỉ chi tiết</label>
                <input type="text" id="address_detail" class="form-control"
                    placeholder="Số nhà, đường....">
            </div>

            <div class="form-check mb-3 col-md-12">
                <input type="checkbox" id="is_default" class="form-check-input">
                <label class="form-check-label" for="is_default">Đặt làm địa chỉ mặc định</label>
            </div>

            <div class="">
                <button id="save_address" class="btn btn-primary">Lưu địa chỉ</button>
                <button id="cancel-edit" class="btn btn-secondary">Hủy bỏ</button>
            </div>
        </div>
    </div>
</div>
