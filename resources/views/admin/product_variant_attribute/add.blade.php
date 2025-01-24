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
                                    <h4 class="header-title mb-4">Thêm giá trị thuộc tính biến thể</h4>
                                    <a class="btn btn-warning btn-sm" style="margin-bottom: 20px"
                                        href="{{ route('admin.product_variant_attributes.index') }}">
                                        Quay lại danh sách
                                    </a>
                                    @if (session('message_error'))
                                        <div class="alert alert-danger" role="alert">
                                            {{ session('message_error') }}
                                        </div>
                                    @endif
                                    <form class="parsley-examples"
                                        action="{{ route('admin.product_variant_attributes.store') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="userName">Sản phẩm biến thể<span
                                                    class="text-danger">*</span></label>
                                            <select name="id_product_variant" id="id_product_variant" class="form-control">
                                                @foreach ($product_variant as $value)
                                                    <option value="{{ $value->id }}">{{ $value->product->name }} -
                                                        {{ $value->sku }}</option>
                                                @endforeach
                                            </select>
                                            @error('id_product_variant')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="userName">Giá trị thuộc tính<span
                                                    class="text-danger">*</span></label>
                                            <select name="id_attribute_value" id="id_attribute_value" class="form-control">
                                                @foreach ($attribute_value as $value)
                                                    <option value="{{ $value->id }}">{{ $value->attribute->name }} -
                                                        {{ $value->value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_attribute_value')
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
