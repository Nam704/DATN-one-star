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
                                        href="{{ route('admin.product_variant_attributes.index') }}">
                                        Quay lại danh sách
                                    </a>
                                    @if (session('message_error'))
                                        <div class="alert alert-danger" role="alert">
                                            {{ session('message_error') }}
                                        </div>
                                    @endif
                                    <form class="parsley-examples mt-3"
                                        action="{{ route('admin.product_variant_attributes.update', $pva->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label for="userName">Sản phẩm biến thể<span
                                                    class="text-danger">*</span></label>
                                            <select name="id_product_variant" id="id_product_variant" class="form-control">
                                                @foreach ($product_variant as $value)
                                                    <option value="{{ $value->id }}"
                                                        @if ($value->id == $pva->id_product_variant) selected @endif>
                                                        {{ $value->product->name }} - {{ $value->sku }}
                                                    </option>
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
                                                    <option value="{{ $value->id }}"
                                                        @if ($value->id == $pva->id_attribute_value) selected @endif>
                                                        {{ $value->attribute->name }} -
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
