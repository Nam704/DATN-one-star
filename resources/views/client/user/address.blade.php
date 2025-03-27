<div class="tab-pane row" id="address">
    <div class="col-12 mb-20">
        <label></label>
        <div class="row mb-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Chi tiết</th>
                                <th>Xã</th>
                                <th>Huyện</th>
                                <th>Tỉnh/Thành phố</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($addresses as $address)
                                <tr>

                                    <td> {{ $address->address_detail }}</td>
                                    <td>{{ $address->ward_name }}</td>
                                    <td><span
                                            class="success">{{ $address->district_name }}</span>
                                    </td>
                                    <td>{{ $address->province_name }} </td>
                                    <td>

                                        @if ($address->is_default == 0)
                                            <a href="#" class="view ">default</a> ||
                                            <a href="">delete</a>
                                        @else
                                            Is default
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if (count($addresses) < 3)
                <div class="address-select row mb-2">
                    <div class="col-md-4">
                        <select name="province" class="form-select" id="province">
                            <option value="" {{ old('province') ? 'selected' : '' }}>
                                Chọn tỉnh
                            </option>
                        </select>
                        @error('province')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <select name="district" class="form-select" id="district">
                            <option value="" {{ old('district') ? 'selected' : '' }}>
                                Chọn quận
                            </option>
                        </select>
                        @error('district')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <select name="ward" class="form-select" id="ward">
                            <option value="" {{ old('ward') ? 'selected' : '' }}>Chọn
                                phường
                            </option>
                        </select>
                        @error('ward')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mt-2 row">
                        <div class="col-12 row">
                            <div class="col-10">
                                <input id="address_detail" class="form-control"
                                    placeholder="House number and street name" type="text">
                            </div>
                            <div class="col-2">
                                <select name="" id="is_default" class=" form-control ">
                                    <option value="0" selected>Phụ</option>
                                    <option value="1">Mặc định</option>
                                </select>
                            </div>

                        </div>

                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" id="save_address">Save</button>
                    </div>
            @endif
        </div>
    </div>

</div>