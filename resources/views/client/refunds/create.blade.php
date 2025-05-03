@extends('client.layouts.home.layout')
@section('title', 'Order Detail')
@section('content')
    <div class="container">
        <h2>Yêu Cầu Hoàn Tiền</h2>

        {{-- Hiển thị thông báo thành công/lỗi --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- Hiển thị lỗi validation --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('refunds.store') }}">
            @csrf

            <div class="mb-3">
                <label>Chọn Đơn Hàng</label>
                <select name="order_id" class="form-select @error('order_id') is-invalid @enderror" required>
                    @foreach ($orders as $order)
                        <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                            Đơn #{{ $order->id }} - {{ $order->code }}
                        </option>
                    @endforeach
                </select>
                @error('order_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Tên Ngân Hàng</label>
                <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                    value="{{ old('bank_name') }}" required>
                @error('bank_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Số Tài Khoản</label>
                <input type="text" name="account_number"
                    class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number') }}"
                    required>
                @error('account_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Tên Chủ Tài Khoản</label>
                <input type="text" name="account_holder"
                    class="form-control @error('account_holder') is-invalid @enderror" value="{{ old('account_holder') }}"
                    required>
                @error('account_holder')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Gửi Yêu Cầu</button>
        </form>
    </div>
@endsection
