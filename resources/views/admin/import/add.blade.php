@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Velonic</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                    <li class="breadcrumb-item active">Form Elements</li>
                </ol>
            </div>
            <h4 class="page-title">Form Elements</h4>
        </div>
    </div>
</div>

<!-- end page title -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{route('admin.imports.listApproved') }}" type="button" class="btn btn-sm btn-primary">
                    Back to list
                </a>
            </div>
            <div class="card-body">
                {{-- <div class="row">
                    <div class="col-lg-12"> --}}
                        <form id="import-form" action="{{ route('admin.imports.add') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="mb-3 col-md-3">
                                            <label for="simpleinput" class="form-label">Suppliers</label>

                                            <select name="supplier" class="form-select" id="supplier-name">
                                                <option value="" {{ old('supplier') ? 'selected' : '' }}>Suppliers
                                                </option>
                                                @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier') ? 'selected' : ''
                                                    }}>{{ $supplier->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('supplier')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror

                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="simpleinput" class="form-label">Import name</label>
                                            <input type="text" id="import-name" name="name" value="{{ old('name') }}"
                                                class="form-control" readonly>
                                            @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="example-select" class="form-label">Import date</label>
                                            <input type="date" class="form-control" id="import-date" name="import_date"
                                                readonly>

                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="total-amount" class="form-label">Total Amount</label>
                                            <input type="number" id="total-amount" name="total_amount"
                                                class="form-control" step="0.01" readonly>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label for="note" class="form-label">Note</label>
                                            <textarea id="note" name="note" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <!-- Thêm chi tiết nhập hàng -->
                                    <div id="import-details-container">
                                        <h5>Import Details</h5>
                                        <div class="product-rows-container">
                                            <!-- Each product row will be dynamically added here -->
                                        </div>
                                    </div>

                                    <button type="button" id="add-row-btn" class="btn btn-primary">Thêm một sản phẩm
                                    </button>

                                    <div class="mb-3 d-grid ">
                                        <button type="submit" id="btn-submit" class="btn btn-lg btn-success">Add new
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        {{--
                    </div> <!-- end col --> --}}


                    {{--
                </div> --}}
                <!-- end row-->
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->





@endsection
@push('scripts')

<script src="{{ asset('admin/api/addImport.js') }}"></script>
{{-- <script src="{{ asset('admin/api/demoAdd.js') }}"></script> --}}

@endpush