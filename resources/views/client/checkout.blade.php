@extends('client.layouts.layout')

@section('content')
    @if (session('success'))
        <script>
            swal("Good job!", "{{ session('success') }}", "success");
        </script>
    @elseif (session('error'))
        <script>
            swal("Oops!", "{{ session('error') }}", "error");
        </script>
    @endif

    <div class="container mt-2 mb-3">
        <h3 class="text-center mb-5 main-title">Thanh Toán</h3>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h4 class="card-title mb-4">Thông Tin Thanh Toán</h4>
                        <form action="{{ route('client.product.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="selected_variant_id" value="{{ $variant->id }}">
                            <input type="hidden" name="quantity" value="{{ $quantity }}">
                            <input type="hidden" name="unit_price" value="{{ $variant->price }}">
                            <input type="hidden" name="total_price" value="{{ $variant->price * $quantity }}">

                            <div class="mb-4">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Họ và Tên
                                </label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-2"></i>Số Điện Thoại
                                </label>
                                <input type="text" class="form-control form-control-lg" id="phone" name="phone"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>Địa Chỉ
                                </label>
                                <textarea class="form-control form-control-lg" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-4"
                                style="background-color: #FFD54C; border: none;">
                                Đặt Hàng
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">Thông Tin Đơn Hàng</h4>
                        <div class="text-center mb-4">
                            <img id="main-image" class="img-fluid rounded product-image"
                                src="{{ isset($images[0]) ? Storage::url('images/' . $images[0]->url) : asset('default-image.jpg') }}"
                                alt="{{ $variant->product->name }}">
                        </div>
                        <h5 class="product-title">{{ $variant->product->name }}</h5>
                        <p class="card-text"><strong>SKU:</strong> <span class="text-muted">{{ $variant->sku }}</span></p>
                        <p class="card-text"><strong>Số lượng:</strong> <span class="badge"
                                style="background-color: #FFD54C">{{ $quantity }}</span></p>
                        <p class="card-text"><strong>Giá:</strong> <span
                                style="color: #FFD54C">{{ number_format($variant->price, 0, ',', '.') }} </span></p>
                        <p class="card-text"><strong>Tổng cộng:</strong> <span class="fw-bold"
                                style="color: #FFD54C">{{ number_format($variant->price * $quantity, 0, ',', '.') }}
                            </span></p>

                        <h6 class="mt-4 mb-3">Thuộc Tính:</h6>
                        <ul class="list-group list-group-flush">
                            @foreach ($attributes as $attribute)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $attribute['attribute_name'] }}
                                    <span style="background-color: #FFD54C"
                                        class="badge rounded-pill">{{ $attribute['attribute_value'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #FFD54C;
            --text-color: #333;
            --background-color: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .main-title {
            font-weight: 700;
            color: var(--text-color);
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }

        .main-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 30px rgba(255, 213, 76, 0.2);
            transform: translateY(-5px);
        }

        .form-control,
        .btn {
            border-radius: 10px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 213, 76, 0.25);
            border-color: var(--primary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #FFE082;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 213, 76, 0.4);
        }

        .product-image {
            box-shadow: 0 5px 15px rgba(255, 213, 76, 0.2);
            transition: all 0.3s ease;
        }

        .product-image:hover {
            transform: scale(1.05);
        }

        .product-title {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 15px;
        }

        .badge {
            font-weight: 500;
            padding: 8px 12px;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
            color: var(--text-color);
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .list-group-item {
            border: none;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 213, 76, 0.2);
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
        }

        .fas {
            color: var(--primary-color);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
@endpush
