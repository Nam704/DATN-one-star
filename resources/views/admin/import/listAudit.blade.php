@extends('admin.layouts.layout')
@section('content')
{{-- <h2>{{ $title }}</h2> --}}
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">List Supplier</h4>

                    <div>
                        @if (session('success'))
                        <p class="alert alert-primary">
                            {{ session('success') }}
                        </p>
                        @endif
                        @if ($errors->any()||session('error'))
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                                @if (session('error'))
                                <li>{{ session('error') }}</li>
                                @endif
                            </ul>
                        </div>
                        @endif

                    </div>
                    <a href="{{route('admin.imports.getFormAdd') }}" type="button" class="btn btn-sm btn-primary">
                        Add new import
                    </a>
                    <a href="{{ route('admin.export.exportSamplefile') }}" class="btn btn-sm btn-primary">Get Sample
                        file</a>
                    <div class="row mt-2">
                        <form class="col-6 d-flex" action="{{ route('admin.imports.upload') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" required class="form-control">
                            <button type="submit" class=" ms-1 btn btn-primary">Import</button>
                        </form>
                    </div>



                </div>

                <div class="card-body">
                    {{-- <form action="route('admin.imports.acceptAll')" method="post"> --}}
                        {{-- @csrf --}}


                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped  w-100">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Detail</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($audits as $item)
                                <tr>

                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->user_name }}</td>
                                    <td>{{ $item->action_type }}</td>
                                    <td>{{ $item->total_quantity }}</td>
                                    <td>
                                        <a href="{{-- route('admin.imports.detail',['id'=>$import->id]) --}}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>

                                    </td>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Product</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Detail</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                        {{--
                    </form> --}}

                    {{ $audits->links() }}
                </div> <!-- end card body-->
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
<script src="{{ asset('admin/api/listImport.js') }}"></script>
@endpush