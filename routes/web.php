<?php

use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CartControllerSession;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductController as AppProductController;
use App\Http\Controllers\Client\BlogController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GoogleController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ImportController;
use App\Http\Controllers\Web\MailController;
use App\Http\Controllers\Web\StatisticController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UserContronler;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Web\ImageController;
use App\Http\Controllers\Web\ProductAuditController;
use App\Http\Controllers\Web\ProductVariantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BrandController;
use App\Http\Controllers\Web\AttributeController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\TemplateExportController;
use App\Http\Controllers\Web\BlogController as AppBlogController;
use App\Http\Controllers\Web\ContactController as AppContactController;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\Web\ExcelController;
use App\Http\Controllers\Client\AuthController  as ClientAuthController;
use App\Http\Controllers\Client\CommentController;;

use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\PaymentController as ClientPaymentController;
use App\Http\Controllers\Web\AddressController;
use App\Http\Controllers\Web\BannerController;
use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\Web\CommentController as WebCommentController;
use App\Http\Controllers\Web\PermissionController;
use App\Http\Controllers\Web\ProductDashboardController;
use App\Http\Controllers\Web\RefundController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\VoucherController;
use App\Models\Voucher;




// Người dùng
Route::middleware(['auth'])->group(function () {
    Route::get('/refunds/create', [RefundController::class, 'create'])->name('refunds.create');
    Route::post('/refunds', [RefundController::class, 'store'])->name('refunds.store');
});

// Nhân viên
Route::prefix('staff')->middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/refunds', [RefundController::class, 'index'])->name('staff.refunds.index');
    Route::post('/refunds/{refund}/approve', [RefundController::class, 'approve'])->name('staff.refunds.approve');
    Route::post('/refunds/{refund}/reject', [RefundController::class, 'reject'])->name('staff.refunds.reject');
});

// Quản lý
Route::prefix('manager')->middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/refunds/{refund}/process', [RefundController::class, 'finalProcess'])->name('manager.refunds.process');
});






Route::prefix('auth/')->name('auth.')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('login', 'getFormLogin')->name('getFormLogin');
        Route::post('login', 'login')->name('login');
        Route::get('register', 'getFormRegister')->name('getFormRegister');
        Route::post('register', 'register')->name('register');
        Route::get('forgot-password', 'getFormForgotPassword')->name('getFormForgotPassword');
        Route::post('forgot-password', 'sendPasswordResetEmail')->name('sendPasswordResetEmail');
        Route::get('reset-password/{id}/{token}', 'getfromResetPassword')->name('getfromResetPassword');
        Route::post('reset-password', 'resetPassword')->name('resetPassword');
    });

    // Google login
    Route::controller(GoogleController::class)->group(function () {
        Route::get('google', 'redirectToGoogle')->name('google');
        Route::get('google/callback', 'handleGoogleCallback');
    });
});

Route::prefix('auth/')->name('auth.')->middleware(['auth', 'check.lock'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout')->name('logout');
        Route::get('getProfileAdmin', 'getProfileAdmin')->name('getProfileAdmin');
    });
});

Route::prefix('chat')->name('chat.')->controller(ChatController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
});



Route::prefix('admin')->name('admin.')->middleware(['role:admin,employee'])->group(
    function () {
        Route::prefix('orders')->name("orders.")->controller(OrderController::class)->group(function () {
            Route::get('list', 'list')->name('list');
            Route::post('update-list', 'update');
            Route::post('/update-status/{orderId}', 'updateStatus')->name('updateStatus');
            Route::get('detail/{id}', 'detail')->name('detail');
            Route::post('accept-all', 'acceptAll')->name('acceptAll');
            Route::post('{orderId}/process-cancellation', 'processCancellation')->name('process_cancellation');
        });
        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');
            Route::get('order-status', [DashboardController::class, 'orderStatusStatistics'])->name('orderStatus');
            Route::get('/daily-statistics-dashboard', [DashboardController::class, 'dailyStatistics_Dashboard'])->name('dailyStatistics_Dashboard');
            Route::get('/weekly-order-stats', [DashboardController::class, 'weeklyOrderStats'])->name('weeklyOrderStats');
        });

        Route::prefix('statistics')->controller(StatisticController::class)->name('statistics.')->group(function () {
            Route::get('product-statistic', 'productStatistics')->name('productStatistics')->middleware('permission:view-statistics');
            Route::get('exportTopSaleProducts', 'exportTopSaleProducts')->name('exportTopSaleProducts')->middleware('permission:view-statistics');
            Route::get('exportproductSold', 'exportproductSold')->name('exportproductSold')->middleware('permission:view-statistics');
            Route::get('exportTop10SaleProducts', 'exportTop10SaleProducts')->name('exportTop10SaleProducts')->middleware('permission:view-statistics');
            Route::get('exportLeastSoldProducts', 'exportLeastSoldProducts')->name('exportLeastSoldProducts')->middleware('permission:view-statistics');
            Route::get('exportLowStockProducts', 'exportLowStockProducts')->name('exportLowStockProducts')->middleware('permission:view-statistics');
            Route::get('exportProductsByCategory', 'exportProductsByCategory')->name('exportProductsByCategory')->middleware('permission:view-statistics');
            Route::get('exportTopViewProducts', 'exportTopViewProducts')->name('exportTopViewProducts')->middleware('permission:view-statistics');
            Route::get('exportTopCommentProducts', 'exportTopCommentProducts')->name('exportTopCommentProducts')->middleware('permission:view-statistics');
            Route::get('topSaleProducts', 'topSaleProducts')->name('topSaleProducts')->middleware('permission:view-statistics');
            Route::get('productSold', 'productSold')->name('productSold')->middleware('permission:view-statistics');
            Route::get('categoryStatistics', 'categoryStatistics')->name('categoryStatistics')->middleware('permission:view-statistics');
        });

        Route::controller(ProductDashboardController::class)->group(function () {
            Route::get('dashboardProduct', 'dashboardProduct')->name('dashboardProduct');
            Route::get('topSaleProducts', 'topSaleProducts')->name('topSaleProducts');
            Route::get('topViewProducts', 'topViewProducts')->name('topViewProducts');
            Route::get('topLeastProducts', 'topLeastProducts')->name('topLeastProducts');
            Route::get('lowStockProducts', 'lowStockProducts')->name('lowStockProducts');
        });


        Route::prefix('excels')->name('excels.')->controller(ExcelController::class)->group(function () {
            Route::post('create-product', 'createByExcel')->name('createProduct');
        });
        Route::prefix('export')->name('export.')->controller(TemplateExportController::class)->group(
            function () {
                Route::get('/export-sample-file', 'exportSamplefile')->name('exportSamplefile');
            }
        );
        Route::prefix('statistics')->name('statistics.')->controller(StatisticController::class)->group(function () {
            Route::get('/daily-statistics', 'dailyStatistics')->name('dailyStatistics')->middleware('permission:view-statistics');
            Route::get('/weekly-statistics', 'weeklyStatistics')->name('weeklyStatistics')->middleware('role:admin');
            Route::get('/monthly-statistics', 'monthlyStatistics')->name('monthlyStatistics')->middleware('role:admin');
            Route::get('/yearly-statistics', 'yearlyStatistics')->name('yearlyStatistics')->middleware('role:admin');
            Route::get('/dashboard-statistics', 'dashboardStatistics')->name('dashboardStatistics')->middleware('permission:view-statistics');
        });

        Route::prefix('categories')->name('categories.')->controller(CategoryController::class)->group(function () {
            Route::get('list-category',  'listCategory')->name('listCategory')->middleware('permission:view-categories');
            Route::get('add-category',  'addCategory')->name('addCategory')->middleware('permission:create-categories');
            Route::post('add-category',  'addPostCategory')->name('addPostCategory')->middleware('permission:create-categories');
            Route::get('edit-category/{id}',  'editCategory')->name('editCategory')->middleware('permission:edit-categories');
            Route::put('edit-category/{id}',  'editPutCategory')->name('editPutCategory')->middleware('permission:edit-categories');
            Route::delete('delete-category/{id}',  'deleteCategory')->name('deleteCategory')->middleware('permission:delete-categories');
            Route::get('trash',  'trash')->name('trash')->middleware('permission:view-categories');
            Route::post('restore/{id}',  'restoreCategory')->name('restoreCategory')->middleware('permission:edit-categories');
            Route::delete('destroy-permanent/{id}', 'destroyPermanent')->name('destroyPermanent')->middleware('permission:delete-categories');
        });

        Route::prefix('attributes')->controller(AttributeController::class)->name('attributes.')->group(function () {
            Route::get('/', 'index')->name('index')->middleware('permission:view-attributes');
            Route::get('/create', 'create')->name('create')->middleware('permission:create-attributes');
            Route::post('/', 'store')->name('store')->middleware('permission:create-attributes');
            Route::get('/{id}/edit', 'edit')->name('edit')->middleware('permission:edit-attributes');
            Route::put('/{id}', 'update')->name('update')->middleware('permission:edit-attributes');
            Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status')->middleware('permission:edit-attributes');
            Route::get('/trash', 'trash')->name('trash')->middleware('permission:view-attributes');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete')->middleware('permission:delete-attributes');
            Route::post('/{id}/restore', 'restore')->name('restore')->middleware('permission:edit-attributes');
            Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:delete-attributes');
        });

        // Brands Routes
        Route::prefix('brands')->controller(BrandController::class)->name('brands.')->group(function () {
            Route::get('/', 'index')->name('index')->middleware('permission:view-brands');
            Route::get('/create', 'create')->name('create')->middleware('permission:create-brands');
            Route::post('/', 'store')->name('store')->middleware('permission:create-brands');
            Route::get('/{id}/edit', 'edit')->name('edit')->middleware('permission:edit-brands');
            Route::put('/{id}', 'update')->name('update')->middleware('permission:edit-brands');
            Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status')->middleware('permission:edit-brands');
            Route::get('/trash', 'trash')->name('trash')->middleware('permission:view-brands');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete')->middleware('permission:delete-brands');
            Route::post('/{id}/restore', 'restore')->name('restore')->middleware('permission:edit-brands');
            Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:delete-brands');
        });

        // Blogs
        Route::prefix('blogs')->controller(AppBlogController::class)->name('blogs.')->group(function () {
            Route::get('/', 'index')->name('index')->middleware('permission:view-blogs');
            Route::get('/create', 'create')->name('create')->middleware('permission:create-blogs');
            Route::post('/', 'store')->name('store')->middleware('permission:create-blogs');
            Route::get('/{id}/show', 'show')->name('show')->middleware('permission:view-blogs');
            Route::get('/{id}/edit', 'edit')->name('edit')->middleware('permission:edit-blogs');
            Route::put('/{id}', 'update')->name('update')->middleware('permission:edit-blogs');

            Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:delete-blogs');
            Route::get('/trash', 'trash')->name('trash')->middleware('permission:view-blogs');
            Route::post('/{id}/restore', 'restore')->name('restore')->middleware('permission:edit-blogs');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete')->middleware('permission:delete-blogs');
        });

        Route::prefix('banner')->name('banner.')->controller(BannerController::class)->group(function () {
            Route::get('list',  'list')->name('list')->middleware('permission:view-banners');
            Route::get('create',  'create')->name('create')->middleware('permission:create-banners');
            Route::post('store',  'store')->name('store')->middleware('permission:create-banners');
            Route::get('edit/{id}',  'edit')->name('edit')->middleware('permission:edit-banners');
            Route::put('update/{id}',  'update')->name('update')->middleware('permission:edit-banners');
            Route::delete('delete/{id}',  'delete')->name('delete')->middleware('permission:delete-banners');
            Route::get('detail/{id}',  'detail')->name('detail')->middleware('permission:view-banners');
        });

        // Contact
        Route::prefix('contacts')->controller(AppContactController::class)->name('contacts.')->group(function () {
            Route::get('/', 'index')->name('index')->middleware('permission:view-contacts');
            Route::get('/create', 'create')->name('create')->middleware('permission:create-contacts');
            Route::post('/', 'store')->name('store')->middleware('permission:create-contacts');
            Route::get('/{id}/show', 'show')->name('show')->middleware('permission:view-contacts');
            Route::get('/{id}/edit', 'edit')->name('edit')->middleware('permission:edit-contacts');
            Route::put('/{id}', 'update')->name('update')->middleware('permission:edit-contacts');

            Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:delete-contacts');
            Route::get('/trash', 'trash')->name('trash')->middleware('permission:view-contacts');
            Route::post('/{id}/restore', 'restore')->name('restore')->middleware('permission:edit-contacts');
            Route::delete('delete/{id}', 'delete')->name('delete')->middleware('permission:delete-contacts');
        });

        //comments
        Route::prefix('comments')->controller(WebCommentController::class)->name('comments.')->group(function () {
            Route::get('/', 'index')->name('index')->middleware('permission:view-comments');
            Route::get('listapprove', 'listapprove')->name('listapprove')->middleware('permission:view-comments');
            Route::get('listreject', 'listreject')->name('listreject')->middleware('permission:view-comments');
            Route::get('listdelete', 'listdelete')->name('listdelete')->middleware('permission:view-comments');
            Route::post('approve/{id}', 'approve')->name('approve')->middleware('permission:edit-comments');
            Route::post('reject/{id}', 'reject')->name('reject')->middleware('permission:edit-comments');
            Route::get('show/{id}', 'show')->name('show')->middleware('permission:view-comments');
            Route::delete('destroy/{id}', 'destroy')->name('destroy')->middleware('permission:delete-comments');
            Route::delete('delete/{id}', 'delete')->name('delete')->middleware('permission:delete-comments');
            Route::post('restore/{id}', 'restore')->name('restore')->middleware('permission:edit-comments');
        });


        Route::prefix('products')->controller(ProductController::class)->name('products.')->group(function () {
            Route::get('/create',  'create')->name('create')->middleware('permission:create-products'); // Hiển thị form thêm sản phẩm
            Route::post('/store',  'store')->name('store')->middleware('permission:create-products');
            Route::get('/',  'list')->name('list')->middleware('permission:view-products');
            Route::get('trash',  'trash')->name('trash')->middleware('permission:view-products');
            Route::get('/edit/{id}',  'edit')->name('edit')->middleware('permission:edit-products');
            Route::post('/update/{id}',  'update')->name('update')->middleware('permission:edit-products');
            Route::get('get-creat-product-sample-file', 'exportCreateExcel')->name('exportCreateExcel')->middleware('permission:create-products');
            Route::post('import-product', 'import')->name('importProduct')->middleware('permission:create-products');
            Route::get('detail/{id}', 'detail')->name('detail')->middleware('permission:view-products');
            Route::get('stas/{id}', 'stas')->name('stas')->middleware('permission:view-products');
            Route::delete('lock/{id}', 'lock')->name('lock')->middleware('permission:edit-products');
            Route::post('open-product/{id}', 'openProduct')->name('openProduct')->middleware('permission:edit-products');
            Route::get('product-variant-detail/{productId}/{variantId}', 'variantDetails')->name('product-variant-detail')->middleware('permission:view-products');
            Route::get('/filter','filter')->name('filter');
        });

        //Address
        Route::prefix('address')->name('address.')->controller(AddressController::class)->group(function () {
            Route::get('provinces', 'getProvinces')->name('getProvinces');
            Route::get('districts/{provinceId}', 'getDistricts')->name('getDistricts');
            Route::get('wards/{districtId}', 'getWards')->name('getWards');
        });

        // Users
        Route::prefix('users')->controller(UserContronler::class)->name('users.')->group(function () {
            Route::get('/', 'index')->name('index')->middleware('role:admin');
            Route::get('listemployee', 'listemployee')->name('listemployee')->middleware('role:admin');
            Route::get('listuser', 'listuser')->name('listuser')->middleware('permission:view-users');
            Route::get('listtkkhoa', 'listtkkhoa')->name('listtkkhoa')->middleware('permission:view-users');
            Route::get('lock/{id}', 'lock')->name('lock')->middleware('permission:edit-users');
            Route::post('opentk/{id}', 'opentk')->name('opentk')->middleware('permission:edit-users');

            Route::get('/create', 'create')->name('create')->middleware('permission:create-users');
            Route::post('/', 'store')->name('store')->middleware('permission:create-users');
            Route::get('/{id}/show', 'show')->name('show')->middleware('permission:view-users');
            Route::get('/{id}/edit', 'edit')->name('edit')->middleware('permission:edit-users');
            Route::put('/{id}', 'update')->name('update')->middleware('permission:edit-users');

            Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:delete-users');
            Route::get('/trash', 'trash')->name('trash')->middleware('permission:view-users');
            Route::post('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');

            // Biểu đồ thống kê
            Route::get('/{id}/chart_user', 'chart_user')->name('chart_user')->middleware('permission:view-statistics');
            Route::get('/charts', 'charts')->name('charts')->middleware('permission:view-statistics');
            Route::get('/getUserStats', 'getUserStats')->name('getUserStats');
            Route::get('/location-stats', 'getUserLocationStats')->name('locationStats');
            Route::get('/top-spenders', 'getTopSpenders')->name('topSpenders');
            Route::get('/order-status-stats/{id}', 'getOrderStatusStats')->name('getOrderStatusStats');
        });

        Route::prefix('mails')->name('mails.')->controller(MailController::class)->group(
            function () {
                Route::get('/contact', 'sendMail')->name('sendMail');
            }
        );
        Route::prefix('product_variants')->name('product_variants.')->controller(ProductVariantController::class)->group(
            function () {
                Route::get('/{id}', 'list')->name('list');
            }
        );
        Route::prefix('suppliers')->controller(SupplierController::class)->name('suppliers.')->group(
            function () {
                Route::get('add', 'getFormAdd')->name('getFormAdd')->middleware('permission:create-suppliers');
                Route::get('edit/{id}', 'getFormUpdate')->name('getFormUpdate')->middleware('permission:edit-suppliers');
                Route::get('/', 'list')->name('list')->middleware('permission:view-suppliers');
                Route::get('lockOrActive/{id}', 'lockOrActive')->name('lockOrActive')->middleware('permission:edit-suppliers');
                Route::post('add', 'add')->name('add')->middleware('permission:create-suppliers');
                Route::post('edit/{id}', 'edit')->name('edit')->middleware('permission:edit-suppliers');
            }
        );

        Route::prefix('imports')->controller(ImportController::class)->name('imports.')->group(
            function () {
                Route::get('add', 'getFormAdd')->name('getFormAdd')->middleware('permission:create-imports');
                Route::get('detail/{id}', 'detail')->name('detail')->middleware('permission:view-imports');
                Route::post('/upload', 'importExcel')->name('upload')->middleware('permission:create-imports');
                Route::get('edit/{id}', 'getFormEdit')->name('getFormEdit')->middleware('permission:edit-imports');
                Route::get('/list-approved', 'listApproved')->name('listApproved')->middleware('permission:view-imports');
                Route::get('/list-pending', 'listPending')->name('listPending')->middleware('permission:view-imports');
                Route::get('/list-rejected', 'listRejected')->name('listRejected')->middleware('permission:view-imports');
                Route::post('add', 'add')->name('add')->middleware('permission:create-imports');
                Route::post('edit/{id}', 'edit')->name('edit')->middleware('permission:edit-imports');
                Route::get('accept/{id}', 'accept')->name('accept')->middleware('role:admin');
                Route::get('reject/{id}', 'reject')->name('reject')->middleware('role:admin');
                Route::get('update-price/{id}', 'updatePrice')->name('updatePrice')->middleware('permission:edit-imports');
            }
        );


        Route::prefix('product_audits')->name('product_audits.')
            ->controller(ProductAuditController::class)->group(function () {
                Route::get('list', 'list')->name('list')->middleware('permission:view-products');
                Route::get('/', 'index')->name('index')->middleware('permission:view-products');
                Route::get('create', 'create')->name('create')->middleware('permission:create-products');
                Route::post('store', 'store')->name('store')->middleware('permission:create-products');
                Route::get('edit/{id}', 'edit')->name('edit')->middleware('permission:edit-products');
                Route::put('update/{id}', 'update')->name('update')->middleware('permission:edit-products');
                Route::get('destroy/{id}', 'destroy')->name('destroy')->middleware('permission:delete-products');
                Route::get('show/{id}', 'show')->name('show')->middleware('permission:view-products');
            });

        Route::prefix('vouchers')->name('vouchers.')->controller(VoucherController::class)->group(function () {
            Route::get('list',  'listVoucher')->name('listVoucher')->middleware('permission:view-vouchers');
            Route::get('add',  'addVoucher')->name('addVoucher')->middleware('permission:create-vouchers');
            Route::post('add',  'addPostVoucher')->name('addPostVoucher')->middleware('permission:create-vouchers');
            Route::get('edit/{id}',  'editVoucher')->name('editVoucher')->middleware('permission:edit-vouchers');
            Route::get('detail/{id}',  'detailVoucher')->name('detailVoucher')->middleware('permission:view-vouchers');
            Route::put('edit/{id}',  'editPutVoucher')->name('editPutVoucher')->middleware('permission:edit-vouchers');
            Route::delete('delete/{id}',  'deleteVoucher')->name('deleteVoucher')->middleware('permission:delete-vouchers');
        });

        // Roles management - Sử dụng role:admin để đảm bảo chỉ admin mới truy cập được
        Route::prefix('roles')->name('roles.')->middleware(['role:admin'])->controller(RoleController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}/detail', 'detail')->name('detail');
            Route::get('/{id}/permissions', 'showPermissions')->name('permissions');
            Route::put('/{id}/permissions', 'updatePermissions')->name('permissions.update');
        });

        // Route kiểm tra quyền hạn
        Route::prefix('permissions')->name('permissions.')->controller(PermissionController::class)->group(function () {
            Route::get('/check', 'checkPermission')->name('check');
            Route::get('/test/{permission}', 'testAccess')->name('test');
            Route::get('/refresh', 'refreshPermissions')->name('refresh')->middleware('role:admin');
        });
    }
);
Route::prefix('client')->name('client.')->group(
    function () {
        Route::get('/index', [HomeController::class, 'index'])->name('home');

        Route::prefix('users')->controller(ClientAuthController::class)->name('user.')->group(
            function () {
                Route::get('/my-account', 'myAccount')->name('myAccount');
                Route::post('create-address', 'createAddress')->name('addAddress');
                Route::post('update-address', 'updateAddress')->name('updateAddress');
                Route::post('delete-address', 'deleteAddress')->name('deleteAddress');
                Route::post('set-default-address', 'setDefaultAddress')->name('setDefaultAddress'); // New route
                Route::get('get-addresses', 'getAddresses')->name('getAddresses');
                Route::post('update', 'update')->name('update');
            }
        );
        Route::prefix('products')->name('products.')->group(
            function () {
                Route::get('detail/{id}', [ClientProductController::class, 'detail'])->name('detail');
                Route::post('/store-comment', [ClientProductController::class, 'storecomment'])->name('storecomment');
                Route::post('/store-comment/{id}', [ClientProductController::class, 'showProduct'])->name('showProduct');
                Route::get('related/{id}', [ClientProductController::class, 'related'])->name('related');
            }
        );

        Route::controller(ShopController::class)->group(function () {
            Route::get('shop', 'shop')->name('shop');
            Route::get('/shop/filter', [ShopController::class, 'filter'])->name('filter');
        });
        Route::controller(SearchController::class)->group(function () {
            Route::get('/search', [SearchController::class, 'search'])->name('search');
        });

        Route::prefix('carts')->controller(CartController::class)->name('carts.')->group(
            function () {
                Route::get('/get',  'getCart');
                Route::post('/add',  'addToCart');
                Route::post('/update',  'updateCart');
                Route::post('/remove',  'removeFromCart');
                Route::post('/clear',  'clearCart');
                Route::post('/save-to-db',  'saveSessionCartToDatabase');
                Route::get('view-cart', 'viewCart')->name('viewCart');
            }
        );

        Route::prefix('checkout')->controller(CheckoutController::class)->name('checkout.')->group(
            function () {
                Route::post('/', 'create')->name('create');
                Route::get('/show', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
            }
        );
        Route::prefix('orders')->controller(ClientOrderController::class)->name('orders.')->group(
            function () {
                Route::post('/store', 'store')->name('store');
                Route::get('/detail/{id}', 'detailOrder')->name('detail');
                Route::get('/check-order', 'check')->name('check');

                Route::get('/',  'orders')->name('list');
                Route::post('/{orderId}/retry-payment',  'retryPayment')->name('retryPayment');
                Route::post('/{orderId}/cancel', 'cancelOrder')->name('cancelOrder');
            }
        );
        Route::prefix('payment')->controller(ClientPaymentController::class)->name('payment.')->group(
            function () {
                Route::get('/', 'handleVnpayReturn')->name('handleVnpayReturn');
            }
        );



        Route::prefix('blog')->controller(BlogController::class)->name('blog.')->group(function () {
            Route::get('/index', [BlogController::class, 'index'])->name('index');
            Route::get('show/{id}', 'show')->name('show');
        });
        Route::prefix('contact')->controller(ContactController::class)->name('contact.')->group(function () {
            Route::get('/index', [ContactController::class, 'index'])->name('index');
            Route::post('/', 'store')->name('store');
        });
    }
);
