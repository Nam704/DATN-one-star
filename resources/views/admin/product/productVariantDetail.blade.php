@extends('admin.layouts.layout')
 @section('content')
     <style>
         .product-details-tab img {
             max-width: 100%;
             height: auto;
         }
 
         .variant-image {
             width: 80px;
             height: 80px;
             object-fit: contain;
         }
 
         .single-zoom-thumb img {
             width: 70px;
             height: 70px;
             object-fit: cover;
         }
 
         .product-album {
             display: flex;
             gap: 10px;
             overflow-x: auto;
             padding: 10px 0;
         }
 
         .product-album img {
             width: 80px;
             height: 80px;
             object-fit: cover;
             cursor: pointer;
         }
     </style>
     <div class="container-fluid mt-3">
         <div class="card">
             <div class="card-header">
                 <h4 class="header-title">Chi tiết biến thể: {{ $variant->sku }}</h4>
             </div>
             <div class="card-body">
                 <h5>Danh sách người dùng đã đặt hàng</h5>
                 <table class="table table-bordered">
                     <thead>
                         <tr>
                             <th>ID</th>
                             <th>Tên người dùng</th>
                             <th>Email</th>
                             <th>Số điện thoại</th>
                             <th>ID Đơn hàng</th>
                             <th>Trạng thái đơn hàng</th>
                             <th>Địa chỉ</th> <!-- Thêm cột Địa chỉ -->
                         </tr>
                     </thead>
                     <tbody>
                         @foreach($users as $userData)
                         <tr>
                             <td>{{ $userData['user']->id }}</td>
                             <td>{{ $userData['user']->name }}</td>
                             <td>{{ $userData['user']->email }}</td>
                             <td>{{ $userData['user']->phone }}</td>
                             <td>{{ $userData['order_id'] }}</td>
                             <td>{{ $userData['order_status'] }}</td>
                             <td>{{ $userData['address'] }}</td> <!-- Hiển thị địa chỉ -->
                         </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 @endsection