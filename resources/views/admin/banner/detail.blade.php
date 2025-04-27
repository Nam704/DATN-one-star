@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">Chi Tiết Banner</h3>
                </div>
                <div class="card-body">

                    @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Tiêu Đề</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">{{ $banner->title ?? 'Không có' }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Mô Tả</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">{{ $banner->description ?? 'Không có' }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Ảnh Banner</label>
                        <div class="col-sm-9">
                            @if($banner->image)
                            <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner Image" class="img-fluid rounded" style="max-height: 250px;">
                            @else
                            <p class="text-muted">Không có ảnh</p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Trạng Thái</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">{{ (int) $banner->status === 1 ? 'Hoạt động' : 'Không hoạt động'}}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Ngày Bắt Đầu</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">{{ $banner->start_date }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Ngày Kết Thúc</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">{{ $banner->end_date }}</p>
                        </div>
                    </div>

                    <div class="form-group row mt-4">
                        <div class="col text-center">
                            <a href="{{ route('admin.banner.list') }}" class="btn btn-secondary">Quay Lại Danh Sách</a>
                        </div>
                    </div>

                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div>
    </div>
</div>
@endsection