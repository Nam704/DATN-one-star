@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-4">Sửa giá trị thuộc tính</h4>
                                    <a class="btn btn-warning btn-sm mb-2"
                                       href="{{ route('admin.attribute_values.list') }}">
                                        Quay lại danh sách
                                    </a>
                                    @if (session('message_error'))
                                        <div class="alert alert-danger" role="alert">
                                            {{ session('message_error') }}
                                        </div>
                                    @endif
                                    <form class="parsley-examples mt-3"
                                          action="{{ route('admin.attribute_values.update', $value->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="form-group">
                                            <label>Thuộc tính <span class="text-danger">*</span></label>
                                            <select name="id_attribute" id="id_attribute" class="form-control">
                                                @foreach ($attributes as $id => $name)
                                                    <option value="{{ $id }}"
                                                        @if ($id == $value->id_attribute) selected @endif>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_attribute')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Giá trị thuộc tính <span class="text-danger">*</span></label>
                                            <input name="value" value="{{ old('value', $value->value) }}" type="text"
                                                   class="form-control">
                                            @error('value')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Trạng thái<span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="active" @if ($value->status == 'active') selected @endif>
                                                    Đang hoạt động
                                                </option>
                                                <option value="inactive" @if ($value->status == 'inactive') selected @endif>
                                                    Ngừng hoạt động
                                                </option>
                                            </select>
                                            @error('status')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="form-group text-right mb-0">
                                            <button class="btn btn-primary waves-effect waves-light mr-1" type="submit">
                                                Lưu
                                            </button>
                                            <button type="reset" class="btn btn-secondary waves-effect waves-light">
                                                Làm lại
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
@endpush
