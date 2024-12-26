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
                {{-- <h4 class="header-title">Input Types</h4>
                <p class="text-muted mb-0">
                    Most common form control, text-based input fields. Includes support for all
                    HTML5 types: <code>text</code>, <code>password</code>, <code>datetime</code>,
                    <code>datetime-local</code>, <code>date</code>, <code>month</code>,
                    <code>time</code>, <code>week</code>, <code>number</code>, <code>email</code>,
                    <code>url</code>, <code>search</code>, <code>tel</code>, and <code>color</code>.
                </p> --}}
            </div>
            <div class="card-body">
                {{-- <div class="row">
                    <div class="col-lg-12"> --}}
                        <form id="addressForm" action="{{ route('admin.suppliers.add') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Name</label>
                                        <input type="text" id="simpleinput" name="name" value="{{ old('name') }}"
                                            class="form-control">
                                        @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="example-select" class="form-label">Status</label>
                                        <select class="form-select" name="status" id="example-select">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>

                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Phone</label>
                                        <input type="text" id="simpleinput" name="phone" value="{{ old('phone') }}"
                                            class="form-control">
                                        @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-8">


                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Address</label>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <select name="province" class="form-select" id="province">
                                                    <option value="{{ old('province',$province ?? '') }}">Chọn tỉnh
                                                    </option>
                                                </select>
                                                @error('province')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <select name="district" class="form-select" id="district">
                                                    <option value="{{ old('district',$district ?? '') }}">Chọn quận
                                                    </option>
                                                </select>
                                                @error('district')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <select name="ward" class="form-select" id="ward">
                                                    <option value="{{ old('ward',$ward ?? '') }}">Chọn phường</option>
                                                </select>
                                                @error('ward')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12">
                                                <input type="text" id="result" name="addressSelected" readonly
                                                    class="form-control" placeholder="Selected address">
                                                @error('addressSelected')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                        <input type="text" id="simpleinput" name="address" class="form-control"
                                            value="{{ old('address') }}"
                                            placeholder="Detailed address: house number, street number">
                                        @error('address')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 d-grid ">
                                        <button type="submit" class="btn btn-lg btn-success">Add new user</button>
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
<script src="{{ asset('admin/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/axios.min.js') }}"></script>

<script src="{{ asset('admin/api/addSupplier.js') }}"></script>
@endsection