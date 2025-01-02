@extends('admin.layouts.layout')
@section('content')
{{-- <h2>{{ $title }}</h2> --}}
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">List Supplier</h4>
                    @if (session('success'))
                    <p class="alert alert-primary">
                        {{ session('success') }}
                    </p>
                    @endif

                    <a href="{{route('admin.imports.getFormAdd') }}" type="button" class="btn btn-sm btn-primary">
                        Add new import
                    </a>

                </div>

                <div class="card-body">

                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Name supplier</th>
                                <th>Import date</th>
                                <th>Total amount</th>
                                <th>Detail</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($imports as $import)
                            <tr>
                                <td>{{ $import->name }}</td>
                                <td>{{ $import->supplier_name }}</td>
                                <td>{{ $import->import_date }}</td>
                                <td>{{ $import->total_amount }}</td>
                                <td>
                                    <a href="{{ route('admin.imports.detail',['id'=>$import->id]) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>

                                </td>
                                <td>
                                    <a href="{{-- route('admin.imports.delete',['id'=>$import->id]) --}}"
                                        class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>

                                        Delete

                                    </a>
                                    <a href="{{route('admin.imports.edit',['id'=>$import->id]) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>

                                </td>
                                </td>
                            </tr>


                            @endforeach


                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Name supplier</th>
                                <th>Import date</th>
                                <th>Total amount</th>
                                <th>Detail</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                    {{-- {{ $imports->links() }} --}}
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
@endpush