@extends('admin.layouts.layout')

@section('content')
<style>
    /* Custom styles cho thanh nav tinh tế */
    .custom-nav-tabs {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .custom-nav-tabs .nav-link {
        font-weight: 600;
        color: #495057;
        border: none;
        transition: all 0.3s ease;
        position: relative;
        padding: 1rem 1.5rem;
        margin: 0 0.25rem;
    }
    .custom-nav-tabs .nav-link::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: -2px;
        height: 2px;
        background: transparent;
        transition: background 0.3s ease;
    }
    .custom-nav-tabs .nav-link:hover {
        color: #0056b3;
    }
    .custom-nav-tabs .nav-link.active {
        color: #fff;
        background: linear-gradient(45deg, #0062E6, #33AEFF);
        border-radius: 0.5rem;
    }
    .custom-nav-tabs .nav-link.active::after {
        background: transparent;
    }
    /* Khu vực nội dung báo cáo */
    .custom-content {
        background: #fff;
        border: 1px solid #dee2e6;
        padding: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container mt-2">
    <!-- Thanh nav tab tùy chỉnh -->
    <ul class="nav custom-nav-tabs justify-content-center mb-2">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.thongke.statistics') || (!request()->routeIs('admin.thongke.weeklyStatistics') && !request()->routeIs('admin.thongke.monthlyStatistics') && !request()->routeIs('admin.thongke.yearlyStatistics')) ? 'active' : '' }}" href="{{ route('admin.thongke.statistics') }}">
                <i class="ri-calendar-line me-1"></i> Ngày
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.thongke.weeklyStatistics') ? 'active' : '' }}" href="{{ route('admin.thongke.weeklyStatistics') }}">
                <i class="ri-calendar-2-line me-1"></i> Tuần
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.thongke.monthlyStatistics') ? 'active' : '' }}" href="{{ route('admin.thongke.monthlyStatistics') }}">
                <i class="ri-calendar-todo-line me-1"></i> Tháng
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.thongke.yearlyStatistics') ? 'active' : '' }}" href="{{ route('admin.thongke.yearlyStatistics') }}">
                <i class="ri-calendar-event-line me-1"></i> Năm
            </a>
        </li>
    </ul>

    <!-- Khu vực hiển thị nội dung báo cáo -->
    <div class="tab-content custom-content">
        @yield('statistics-content')
    </div>
</div>
@endsection
