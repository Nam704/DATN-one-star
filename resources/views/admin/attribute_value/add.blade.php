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
                                        href="{{ route('admin.attribute_values.index') }}">
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
                                            <select name="id_attribute" id="id_attribute" class="form-control">
                                                @foreach ($attributes as $value)
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('id_attribute')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="tags">Giá trị thuộc tính<span
                                                    class="text-danger">*</span></label>
                                            <div class="mb-0">
                                                <select name="tags[]" id="tags" class="form-control"
                                                    multiple="multiple" data-role="tagsinput">
                                                    <!-- Các tag có thể chọn -->
                                                </select>
                                            </div>
                                            @error('tags')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <input name="status" value="active" type="hidden">
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
