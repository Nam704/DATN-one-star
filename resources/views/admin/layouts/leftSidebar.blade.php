<div class="leftside-menu">
    <!-- Brand Logo Light -->
    <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
        <span class="logo-lg">
            <img src="{{ asset('admin/assets/images/logo-3.png') }}" alt="logo"
                style="width: 190px; height: auto; margin-top: 10px;">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('admin/assets/images/logo-sm.png') }}" alt="small logo">
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
        <span class="logo-lg">
            <img src="{{ asset('admin/assets/images/logo-dark.png') }}" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('admin/assets/images/logo-sm.png') }}" alt="small logo">
        </span>
    </a>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <ul class="side-nav">
            <!-- Dashboard -->
            @if (auth()->check() && auth()->user() && auth()->user()->hasPermission('dashboard-access'))
                <li class="side-nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="side-nav-link">
                        <i class="ri-dashboard-3-line"></i>
                        <span> Bảng điều khiển </span>
                    </a>
                </li>
            @endif

            <!-- Bán hàng -->
            @if (auth()->check() &&
                    auth()->user() &&
                    (auth()->user()->hasPermission('view-orders') || auth()->user()->hasPermission('view-vouchers')))
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarSales" aria-expanded="false" aria-controls="sidebarSales"
                        class="side-nav-link">
                        <i class="ri-shopping-cart-line"></i>
                        <span> Bán hàng </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarSales">
                        <ul class="side-nav-second-level">
                            @if (auth()->user()->hasPermission('view-orders'))
                                <li>
                                    <a href="{{ route('admin.orders.list') }}">Đơn hàng</a>
                                </li>
                            @endif
                            @if (auth()->user()->hasPermission('view-vouchers'))
                                <li>
                                    <a href="{{ route('admin.vouchers.listVoucher') }}">Mã giảm giá</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            <!-- Sản phẩm -->
            @if (auth()->check() &&
                    auth()->user() &&
                    (auth()->user()->hasPermission('view-products') ||
                        auth()->user()->hasPermission('view-categories') ||
                        auth()->user()->hasPermission('view-attributes') ||
                        auth()->user()->hasPermission('view-brands')))
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarProducts" aria-expanded="false"
                        aria-controls="sidebarProducts" class="side-nav-link">
                        <i class="ri-box-3-line"></i>
                        <span> Sản phẩm </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarProducts">
                        <ul class="side-nav-second-level">
                            @if (auth()->user()->hasPermission('view-products'))
                                <li>
                                    <a href="{{ route('admin.products.list') }}">Danh sách sản phẩm</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.products.trash') }}">Sản phẩm ngừng bán</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.product_audits.list') }}">Kho</a>
                                </li>
                            @endif
                            @if (auth()->user()->hasPermission('view-categories'))
                                <li>
                                    <a href="{{ route('admin.categories.listCategory') }}">Danh mục</a>
                                </li>
                            @endif
                            @if (auth()->user()->hasPermission('view-attributes'))
                                <li>
                                    <a href="{{ route('admin.attributes.index') }}">Thuộc tính</a>
                                </li>
                            @endif
                            @if (auth()->user()->hasPermission('view-brands'))
                                <li>
                                    <a href="{{ route('admin.brands.index') }}">Thương hiệu</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            <!-- Nhập hàng -->
            @if (auth()->check() &&
                    auth()->user() &&
                    (auth()->user()->hasPermission('view-imports') || auth()->user()->hasPermission('view-suppliers')))
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarImports" aria-expanded="false"
                        aria-controls="sidebarImports" class="side-nav-link">
                        <i class="ri-briefcase-line"></i>
                        <span> Nhập hàng </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarImports">
                        <ul class="side-nav-second-level">
                            @if (auth()->user()->hasPermission('view-imports'))
                                <li>
                                    <a href="{{ route('admin.imports.listPending') }}">Chờ xử lý</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.imports.listApproved') }}">Được phê duyệt</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.imports.listRejected') }}">Bị từ chối</a>
                                </li>
                            @endif
                            @if (auth()->user()->hasPermission('view-suppliers'))
                                <li>
                                    <a href="{{ route('admin.suppliers.list') }}">Nhà cung cấp</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            <!-- Người dùng -->
            @if (auth()->check() &&
                    auth()->user() &&
                    (auth()->user()->hasPermission('view-users') || auth()->user()->role->name === 'admin'))
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarUsers" aria-expanded="false" aria-controls="sidebarUsers"
                        class="side-nav-link">
                        <i class="ri-group-2-line"></i>
                        <span> Người dùng </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarUsers">
                        <ul class="side-nav-second-level">
                               @if(auth()->user()->role->name === 'admin')
                                <li>
                                    <a href="{{ route('admin.users.index') }}">Danh sách admin</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users.listemployee') }}">Danh sách nhân viên</a>
                                </li>
                                @endif
                                @if (auth()->user()->hasPermission('view-users'))
                                <li>
                                    <a href="{{ route('admin.users.listuser') }}">Danh sách người dùng</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users.listtkkhoa') }}">Tài khoản khóa</a>
                                </li>
                            @endif
                            @if (auth()->user()->role->name === 'admin')
                                <li>
                                    <a href="{{ route('admin.roles.index') }}">Phân quyền</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.permissions.check') }}">Kiểm tra quyền</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            <!-- Nội dung -->
            @if (auth()->check() &&
                    auth()->user() &&
                    (auth()->user()->hasPermission('view-blogs') || auth()->user()->hasPermission('view-contacts') || auth()->user()->hasPermission('view-banners') ))
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarContent" aria-expanded="false"
                        aria-controls="sidebarContent" class="side-nav-link">
                        <i class="mdi mdi-post"></i>
                        <span> Nội dung </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarContent">
                        <ul class="side-nav-second-level">
                            @if (auth()->user()->hasPermission('view-blogs'))
                                <li>
                                    <a href="{{ route('admin.blogs.index') }}">Tin tức</a>
                                </li>
                            @endif
                            @if (auth()->user()->hasPermission('view-contacts'))
                                <li>
                                    <a href="{{ route('admin.contacts.index') }}">Liên hệ</a>
                                </li>
                            @endif
                            @if (auth()->user()->hasPermission('view-banners'))
                                <li>
                                    <a href="{{ route('admin.banner.list') }}">Banner</a>
                                </li>
                            @endif
                            @if (auth()->user()->hasPermission('view-comments'))
                                <li>
                                    <a href="{{ route('admin.comments-product.index') }}">Bình luận</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            <!-- Thống kê -->
            @if (auth()->check() && auth()->user() && auth()->user()->hasPermission('view-statistics'))
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarStatistics" aria-expanded="false"
                        aria-controls="sidebarStatistics" class="side-nav-link">
                        <i class="ri-line-chart-line"></i>
                        <span> Thống kê </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarStatistics">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{ route('admin.statistics.productStatistics') }}">Thống kê sản phẩm</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.statistics.dailyStatistics') }}">Thống kê đơn hàng</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.charts') }}">Thống kê người dùng</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
        </ul>
        <div class="clearfix"></div>
    </div>
</div>
