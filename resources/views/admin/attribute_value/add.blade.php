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
                                    <h4 class="header-title mb-4">Thêm giá trị thuộc tính</h4>
                                    <a class="btn btn-warning btn-sm" style="margin-bottom: 20px"
                                        href="{{ route('admin.attribute_values.list') }}">
                                        Quay lại danh sách
                                    </a>
                                    @if (session('message_error'))
                                        <div class="alert alert-danger" role="alert">
                                            {{ session('message_error') }}
                                        </div>
                                    @endif
                                    <form class="parsley-examples" action="{{ route('admin.attribute_values.store') }}"
                                        method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="userName">Thuộc tính<span class="text-danger">*</span></label>
                                            <select name="id_attribute" class="form-control">
                                                @foreach($attributes as $attribute)
                                                  <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                                @endforeach
                                              </select>
                                            @error('id_attribute')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Giá trị thuộc tính<span class="text-danger">*</span></label>
                                            <input type="text" name="value" class="form-control"
                                                placeholder="Ví dụ: Đỏ, Xanh, Lớn…" value="{{ old('value') }}">
                                            @error('value')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Trạng thái</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="active" {{ request()->old('status', 'active') == 'active' ? 'selected' : '' }}>
                                                    Đang hoạt động
                                                </option>
                                                <option value="inactive" {{ request()->old('status') == 'inactive' ? 'selected' : '' }}>
                                                    Ngừng hoạt động
                                                </option>

                                            </select>
                                            @error('status')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>


                                        <div class="form-group">
                                            <input name="updated_at" value="{{ now() }}" type="hidden">
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
