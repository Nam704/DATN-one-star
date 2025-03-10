@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="mt-4">
                        <h1 class="text-center">IMAGE</h1>
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <a href="{{ route('admin.images.create') }}" class="btn btn-primary">Thêm mới hình ảnh</a>
                        </div>
                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Total Amount</th>
                                    <th>Voucher</th>
                                    <th>Email</th>
                                    <th>Order Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                @foreach ($orders as $item)
                                    <tr id="row_{{ $item->id }}">
                                        <td>{{ $item->id }}</td>
                                        <td>{{ optional($item->user)->name }}</td>
                                        <td>{{ $item->phone_number }}</td>
                                        <td>{{ optional($item->address)->address_detail }}</td>
                                        <td>{{ $item->total_amount }}</td>
                                        <td>{{ optional($item->voucher)->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ optional($item->orderStatus)->name }}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#ModalShow{{ $item->id }}">
                                                Xem chi tiết đơn hàng
                                            </button>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#ModalEdit" data-id="{{ $item->id }}"
                                                data-status="{{ optional($item->orderStatus)->id }}">
                                                Sửa
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->
    </div>

    <!-- Modal Show -->
    <div class="modal fade" id="ModalShow{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="ModalShowLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Chi Tiết Đơn Hàng #{{ $item->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="fixed-header-datatable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Phone Number</th>
                                <th>Address</th>
                                <th>Voucher</th>
                                <th>Email</th>
                                <th>Order Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $item)
                                <tr>
                                    <td>{{ optional($item->user)->name }}</td>
                                    <td>{{ $item->phone_number }}</td>
                                    <td>{{ optional($item->address)->address_detail }}</td>
                                    <td>{{ optional($item->voucher)->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ optional($item->orderStatus)->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hình ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Tổng giá</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item->orderDetails as $detail)
                                <tr>
                                    <td>{{ $detail->id }}</td>
                                    <td>
                                        @if ($detail->productVariant->images)
                                            <img src="{{ asset($detail->productVariant->images->url) }}"
                                                alt="{{ $detail->product_name }}" class="img-thumbnail"
                                                style="width: 100px; height: 100px; object-fit: contain; object-position: center;">
                                        @else
                                            <p>Không có hình ảnh</p>
                                        @endif
                                    </td>
                                    <td>{{ optional($detail->productVariant->product)->name }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ $detail->unit_price }}</td>
                                    <td>{{ $detail->total_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-end fw-bold fs-5 mt-3">
                        Tổng tiền: {{ $item->total_amount }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sửa Trạng Thái Đơn Hàng -->
    <div class="modal fade" id="ModalEdit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="ModalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa trạng thái đơn hàng</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Input ẩn để lưu order_id -->
                    <input type="hidden" id="order_id">
                    <div class="mb-3">
                        <label for="order_status_id" class="form-label">Trạng thái đơn hàng</label>
                        <select name="order_status_id" id="order_status_id" class="form-select">
                            @foreach ($orderStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="btnUpdateStatus">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <!-- Include Pusher -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <!-- Include Laravel Echo -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.0/echo.iife.min.js"></script>

    <script>
        // Khởi tạo Echo với cấu hình thực tế (thay thế key và cluster)
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true,
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        });

        // Khi modal Sửa trạng thái mở, lấy data-id và data-status từ nút kích hoạt
        var ModalEdit = document.getElementById('ModalEdit');
        ModalEdit.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var orderId = button.getAttribute('data-id');
            var orderStatus = button.getAttribute('data-status');
            document.getElementById('order_id').value = orderId;
            document.getElementById('order_status_id').value = orderStatus;
        });

        // Khi click nút cập nhật trạng thái
        document.getElementById('btnUpdateStatus').addEventListener('click', function() {
            const orderId = document.getElementById('order_id').value;
            const orderStatusId = document.getElementById('order_status_id').value;

            axios.post('{{ route("admin.orders.updateStatus") }}', {
                    order_id: orderId,
                    order_status_id: orderStatusId
                })
                .then(function(response) {
                    if (response.data.success) {
                        const updatedOrder = response.data.order;
                        // Cập nhật UI ngay trên client gửi request
                        let tr = document.querySelector(`tr#row_${updatedOrder.id}`);
                        if (tr) {
                            tr.innerHTML = `
                                <td>${updatedOrder.id}</td>
                                <td>${updatedOrder.user ? updatedOrder.user.name : ''}</td>
                                <td>${updatedOrder.phone_number}</td>
                                <td>${updatedOrder.address ? updatedOrder.address.address_detail : ''}</td>
                                <td>${updatedOrder.total_amount}</td>
                                <td>${updatedOrder.voucher ? updatedOrder.voucher.name : ''}</td>
                                <td>${updatedOrder.email}</td>
                                <td>${updatedOrder.orderStatus ? updatedOrder.orderStatus.name : ''}</td>
                                <td>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#ModalShow${updatedOrder.id}">
                                        Xem chi tiết đơn hàng
                                    </button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#ModalEdit" data-id="${updatedOrder.id}"
                                        data-status="${updatedOrder.order_status_id}">
                                        Sửa
                                    </button>
                                </td>
                            `;
                        }
                        // Thông báo thành công
                        const successAlert = `
                            <div class="alert alert-success alert-dismissible fade show">
                                ${response.data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin',
                            successAlert);
                    }
                })
                .catch(function(error) {
                    console.error(error);
                    alert('Có lỗi xảy ra khi cập nhật trạng thái đơn hàng.');
                });
        });

        // Lắng nghe sự kiện realtime từ server trên kênh "orders"
        Echo.channel('orders')
    .listen('OrderUpdated', event => {
        console.log(event); // Add this line to debug the event data

        let tr = document.querySelector(`tr#row_${event.id}`);
        if (tr) {
            tr.innerHTML = `
                <td>${event.id}</td>
                <td>${event.user ? event.user.name : ''}</td>
                <td>${event.phone_number}</td>
                <td>${event.address ? event.address.address_detail : ''}</td>
                <td>${event.total_amount}</td>
                <td>${event.voucher ? event.voucher.name : ''}</td>
                <td>${event.email}</td>
                <td>${event.orderStatus ? event.orderStatus.name : ''}</td>
                <td>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalShow${event.id}">
                        Xem chi tiết đơn hàng
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-id="${event.id}" data-status="${event.order_status_id}">
                        Sửa
                    </button>
                </td>
            `;
        }
    });

    </script>
@endpush
