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
                                        href="{{ route('admin.attribute_values.index') }}">
                                        Quay lại danh sách
                                    </a>
                                    @if (session('message_error'))
                                        <div class="alert alert-danger" role="alert">
                                            {{ session('message_error') }}
                                        </div>
                                    @endif
                                    <form class="parsley-examples mt-3"
                                        action="{{ route('admin.attribute_values.update', $attributes_value->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label for="userName">Thuộc tính<span class="text-danger">*</span></label>
                                            <select name="id_attribute" id="id_attribute" class="form-control">
                                                @foreach ($attributes as $value)
                                                    <option value="{{ $value->id }}"
                                                        @if ($value->id == $attributes_value->id_attribute) selected @endif>
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_attribute')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="tags">Giá trị thuộc tính<span
                                                    class="text-danger">*</span></label>
                                            <input name="value" value="{{ $attributes_value->value }}" type="text"
                                                class="form-control">
                                            @error('value')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Trạng thái<span class="text-danger">*</span></label>
                                            <select name="status_value" id="status_value" class="form-control">
                                                <option value="active" @if ($attributes_value->status == 'active') selected @endif>
                                                    Đang hoạt động</option>
                                                <option value="inactive" @if ($attributes_value->status == 'inactive') selected @endif>
                                                    Ngừng hoạt động</option>
                                            </select>
                                            @error('status')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group text-right mb-0">
                                            <button class="btn btn-primary waves-effect waves-light mr-1" type="submit">
                                                Submit
                                            </button>
                                            <button type="reset" class="btn btn-secondary waves-effect waves-light">
                                                Reset
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                            <!-- end card -->
                        </div>
                    </div>
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->
    </div>
@endsection
@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
@endpush
