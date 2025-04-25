<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="{{route('admin.dashboard')}}" class="logo logo-light">
        <span class="logo-lg">
            {{-- <img src="{{ asset('admin/assets/images/logo.png') }}" alt="logo"> --}}

            <img src=" {{ asset('admin/assets/images/logo-3.png') }}" alt="logo"
                style="width: 190px; height: auto; margin-top: 10px;">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('admin/assets/images/logo-sm.png') }}" alt="small logo">
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="index.html" class="logo logo-dark">
        <span class="logo-lg">
            <img src="{{ asset('admin/assets/images/logo-dark.png') }}" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('admin/assets/images/logo-sm.png') }}" alt="small logo">
        </span>
    </a>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!--- Sidemenu -->
        <ul class="side-nav">

            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('dashboard-access'))
            <li class="side-nav-item">
                <a href="{{ route('admin.dashboard') }}" class="side-nav-link">
                    <i class="ri-dashboard-3-line"></i>
                    <span> Bảng điều khiển </span>
                </a>
            </li>
            @endif

            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-orders'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPages" aria-expanded="false" aria-controls="sidebarPages"
                    class="side-nav-link">
                    <i class="ri-shopping-cart-line"></i>
                    <span> Đơn hàng </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPages">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.orders.list') }}">Danh sách</a>
                        </li>

                    </ul>
                </div>
            </li>
            @endif

             @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-users'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPagesAuth" aria-expanded="false"
                    aria-controls="sidebarPagesAuth" class="side-nav-link">
                    <i class="ri-group-2-line"></i>
                    <span> Tài khoản </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPagesAuth">
                    <ul class="side-nav-second-level">
                    @if(auth()->user()->role->name === 'admin')
                        <li>
                            <a href="{{ route('admin.users.index') }}">Danh sách admin</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.listemployee') }}">Danh sách nhân viên</a>
                        </li>
                    @endif
                        <li>
                            <a href="{{ route('admin.users.listuser') }}">Danh sách người dùng</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.listtkkhoa') }}">Danh sách tài khoản khóa</a>
                        </li>

                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-products'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarLayouts" aria-expanded="false" aria-controls="sidebarLayouts"
                    class="side-nav-link">
                    <i class="ri-box-3-line"></i>
                    <span> Sản phẩm</span>
                </a>
                <div class="collapse" id="sidebarLayouts">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.products.list') }}">Danh sách</a>
                        </li>
                        @if(auth()->user()->hasPermission('create-products'))
                        <li>
                            <a href="{{ route('admin.products.create') }}">Thêm mới</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            


            @if(auth()->check() && auth()->user()->role->name === 'admin')
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarRoles" aria-expanded="false" aria-controls="sidebarRoles"
                        class="side-nav-link">
                        <i class="ri-shield-user-line"></i>
                        <span> Phân quyền </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarRoles">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{ route('admin.roles.index') }}">Danh sách </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            {{-- Permission check menu - Chỉ dành cho admin --}}
             {{-- @if(auth()->check() && auth()->user() && auth()->user()->role->name == 'admin') --}}
             <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPermissions" aria-expanded="false"
                    aria-controls="sidebarPermissions" class="side-nav-link">
                    <i class="ri-key-2-line"></i>
                    <span> Kiểm tra quyền </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPermissions">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.permissions.check') }}">Kiểm tra quyền</a>
                        </li>
                    </ul>
                </div>
            </li> 
           {{-- @endif --}}

           @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-imports'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarBaseUI" aria-expanded="false" aria-controls="sidebarBaseUI"
                    class="side-nav-link">
                    <i class="ri-briefcase-line"></i>
                    <span> Nhập hàng </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarBaseUI">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.imports.listPending') }}">Danh sách chờ xử lý</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.imports.listApproved') }}">Danh sách được phê duyệt</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.imports.listRejected') }}">Danh sách bị từ chối</a>
                        </li>
                        @if(auth()->user()->hasPermission('create-imports'))
                                <li>
                                    <a href="{{ route('admin.imports.getFormAdd') }}">Create</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-suppliers'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarExtendedUI" aria-expanded="false"
                    aria-controls="sidebarExtendedUI" class="side-nav-link">
                    <i class="ri-building-line"></i>
                    <span> Nhà cung cấp </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarExtendedUI">
                    <ul class="side-nav-second-level">
                    @if(auth()->user()->hasPermission('create-suppliers'))
                        <li>
                            <a href="{{ route('admin.suppliers.list') }}">Danh sách</a>
                        </li>
                    @endif
                    </ul>
                </div>
            </li>
            @endif

            {{-- blogs --}}
            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-blogs'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarThirdLevel" aria-expanded="false"
                    aria-controls="sidebarThirdLevel" class="side-nav-link">
                    <i class="mdi mdi-post"></i>
                    <span> Tin tức </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarThirdLevel">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.blogs.index') }}">Danh sách</a>
                        </li>
                        @if(auth()->user()->hasPermission('create-blogs'))
                                <li>
                                    <a href="{{ route('admin.blogs.create') }}">Thêm mới</a>
                                </li>
                            @endif
                    </ul>
                </div>
            </li>
            @endif

            {{-- contact --}}
            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-contacts'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPagesAuth" aria-expanded="false"
                    aria-controls="sidebarPagesAuth" class="side-nav-link">
                    <i class="mdi mdi-card-account-mail"></i>
                    <span> Liên hệ </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPagesAuth">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.contacts.index') }}">Danh sách</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            {{-- Banner --}}
            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-banners'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarPagesAuth" aria-expanded="false"
                    aria-controls="sidebarPagesAuth" class="side-nav-link">
                    <i class="mdi mdi-image"></i>
                    <span>Banner</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPagesAuth">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.banner.list') }}">Danh sách</a>
                        </li>
                        @if(auth()->user()->hasPermission('create-banners'))
                                <li>
                                    <a href="{{ route('admin.banner.create') }}">Thêm mới</a>
                                </li>
                            @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-products'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarIcons" aria-expanded="false" aria-controls="sidebarIcons"
                    class="side-nav-link">
                    <i class="ri-store-line"></i>
                    <span> Kho </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarIcons">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.product_audits.list') }}">Danh sách</a>
                        </li>

                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-brands'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCharts" aria-expanded="false"
                    aria-controls="sidebarCharts" class="side-nav-link">
                    <i class="ri-price-tag-3-line"></i>
                    <span> Thương hiệu </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCharts">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.brands.index') }}">Danh sách</a>
                        </li>
                        @if(auth()->user()->hasPermission('create-brands'))
                                <li>
                                    <a href="{{ route('admin.brands.create') }}">Thêm mới</a>
                                </li>
                            @endif

                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-categories'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarForms" aria-expanded="false" aria-controls="sidebarForms"
                    class="side-nav-link">
                    <i class="ri-survey-line"></i>
                    <span> Danh mục </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarForms">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.categories.listCategory') }}">Danh sách</a>
                        </li>

                        @if(auth()->user()->hasPermission('create-categories'))
                                <li>
                                    <a href="{{ route('admin.categories.addCategory') }}">Thêm mới</a>
                                </li>
                            @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-attributes'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarTables" aria-expanded="false"
                    aria-controls="sidebarTables" class="side-nav-link">
                    <i class="ri-table-line"></i>
                    <span> Thuộc tính</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarTables">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.attributes.index') }}">Danh sách</a>
                        </li>

                        @if(auth()->user()->hasPermission('create-attributes'))
                                <li>
                                    <a href="{{ route('admin.attributes.create') }}">Thêm mới</a>
                                </li>
                            @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-statistics'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarMaps" aria-expanded="false" aria-controls="sidebarMaps"
                    class="side-nav-link">
                    <i class="ri-line-chart-line"></i>
                    <span>Thông kê </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarMaps">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{route('admin.statistics.productStatistics')}}">Thống kê sản phẩm</a>
                        </li>
                        <li>
                            <a href="{{route('admin.statistics.dailyStatistics')}}">Thống kê đơn hàng</a>
                        </li>
                        <li>
                            <a href="{{route('admin.users.charts')}}">Thống kê Người dùng</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->check() && auth()->user() && auth()->user()->hasPermission('view-vouchers'))
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarMultiLevel" aria-expanded="false"
                    aria-controls="sidebarMultiLevel" class="side-nav-link">
                    <i class="ri-ticket-2-line"></i>
                    <span> Mã giảm giá </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarMultiLevel">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{route('admin.vouchers.listVoucher')}}">Danh sách</a>
                        </li>
                        @if(auth()->user()->hasPermission('create-vouchers'))
                                <li>
                                    <a href="{{route('admin.vouchers.addVoucher')}}">Thêm mới</a>
                                </li>
                            @endif
                    </ul>
                </div>
            </li>
            @endif
        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>
