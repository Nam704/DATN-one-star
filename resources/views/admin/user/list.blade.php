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

                    <a href="{{route('admin.suppliers.getFormAdd') }}" type="button" class="btn btn-sm btn-primary">
                        Add new supplier
                    </a>

                </div>

                <div class="card-body">

                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Avatar</th>
                                <th>Phone</th>

                                <th>Role Name</th>
                                <th>Action</th>

                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->status }}</td>
                                <td>
                                    @if($user->profile_image)
                                    <img src="{{ asset('storage/' . $user->profile_image) }}"
                                        alt="{{ $user->name }}'s avatar" class="rounded-circle" width="40" height="40">
                                    @else
                                    <img src="{{ asset('storage/avatars/default-avatar.png') }}" alt="Default avatar"
                                        class="rounded-circle" width="40" height="40">
                                    @endif
                                </td>
                                <td>{{ $user->phone }}</td>

                                <td>{{ $user->role->name }}</td>
                                <td>
                                    <a type="button" class="btn btn-primary rounded-pill"
                                        href="{{-- route('admin.suppliers.lockOrActive',$supplier->id) --}}">
                                        @if ($user->status=='active')
                                        {{ 'Lock' }}
                                        @else

                                        {{ 'Active' }}
                                        @endif
                                    </a>
                                    <a type="button" class="btn btn-warning rounded-pill"
                                        href="{{-- route('admin.suppliers.getFormUpdate',$supplier->id) --}}">Edit</a>
                                    <a type="button" class="btn btn-danger rounded-pill"
                                        href="{{-- route('admin.suppliers.getFormUpdate',$supplier->id) --}}">Reset
                                        password</a>
                                </td>

                            </tr>
                            @endforeach


                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Avatar</th>
                                <th>Phone</th>

                                <th>Role Name</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
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