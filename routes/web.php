<?php

use App\Http\Controllers\Client\CartControllerSession;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductController as AppProductController;
use App\Http\Controllers\Client\MyAccountController;
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
use App\Http\Controllers\Client\AuthController  as ClientAuthController;;

use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\PaymentController as ClientPaymentController;
use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\Web\VoucherController;
use App\Models\Voucher;


Route::get('/', function () {
    return view('admin.index');
});


// Route::get('/client/index', function () {
//     return view('client.index');
// });
Route::get('/detail-product', function () {
    return view('admin.product.detailBase');
});
Route::get('excel/read', [ExcelController::class, 'index']);





Route::get('/make-password', function () {
    return Hash::make('1234');
});
Route::prefix('auth/')->name('auth.')->group(
    function () {
        Route::controller(GoogleController::class)->group(function () {
            Route::get('google', 'redirectToGoogle')->name('google');
            Route::get('google/callback', 'handleGoogleCallback');
        });
        Route::controller(AuthController::class)->group(function () {
            Route::get('login', 'getFormLogin')->name('getFormLogin');
            Route::post('login', 'login')->name('login');
            Route::get('register', 'getFormRegister')->name('getFormRegister');
            Route::post('register', 'register')->name('register');
            Route::get('logout', 'logout')->name('logout');
            Route::get('forgot-password', 'getFormForgotPassword')->name('getFormForgotPassword');
            Route::post('forgot-password', 'sendPasswordResetEmail')->name('sendPasswordResetEmail');

            Route::get('reset-password/{id}/{token}', 'getfromResetPassword')->name('getfromResetPassword');
            Route::post('reset-password', 'resetPassword')->name('resetPassword');
            Route::get('getProfileAdmin', 'getProfileAdmin')->name('getProfileAdmin');
        });
    }
);
Route::prefix('chat')->name('chat.')->controller(ChatController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    // Route::get('/', 'ChatController@index')->name('index');
    // Route::post('/send', 'ChatController@sendMessage')->name('send');
});



Route::prefix('admin')->name('admin.')->middleware(['role:admin,employee'])->group(
    function () {
        Route::prefix('orders')->name("orders.")->controller(OrderController::class)->group(function () {
            Route::get('list', 'list')->name('list');
            Route::post('update-list', 'update');
            Route::post('/update-status/{orderId}', 'updateStatus')->name('updateStatus');
            Route::get('detail/{id}', 'detail')->name('detail');
            Route::post('accept-all', 'acceptAll')->name('acceptAll');
        });
        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');
            Route::get('order-status', [DashboardController::class, 'orderStatusStatistics'])->name('orderStatus');
        });

        Route::prefix('statistics')->controller(StatisticController::class)->name('statistics.')->group(function () {
            Route::get('product-statistic', 'productStatistics')->name('productStatistics');

            Route::get('topSaleProducts', 'topSaleProducts')->name('topSaleProducts');
            Route::get('productSold', 'productSold')->name('productSold');
            Route::get('categoryStatistics', 'categoryStatistics')->name('categoryStatistics');
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
            Route::get('/daily-statistics', 'dailyStatistics')->name('dailyStatistics');
            Route::get('/weekly-statistics', 'weeklyStatistics')->name('weeklyStatistics');
            Route::get('/monthly-statistics', 'monthlyStatistics')->name('monthlyStatistics');
            Route::get('/yearly-statistics', 'yearlyStatistics')->name('yearlyStatistics');
            Route::get('/dashboard-statistics', 'dashboardStatistics')->name('dashboardStatistics');
        });

        Route::prefix('categories')->name('categories.')->controller(CategoryController::class)->group(function () {
            Route::get('list-category',  'listCategory')->name('listCategory');
            Route::get('add-category',  'addCategory')->name('addCategory');
            Route::post('add-category',  'addPostCategory')->name('addPostCategory');
            Route::get('edit-category/{id}',  'editCategory')->name('editCategory');
            Route::put('edit-category/{id}',  'editPutCategory')->name('editPutCategory');
            Route::delete('delete-category/{id}',  'deleteCategory')->name('deleteCategory');
        });

        Route::prefix('attributes')->controller(AttributeController::class)->name('attributes.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::get('/trash', 'trash')->name('trash');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');
            Route::post('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Brands Routes
        Route::prefix('brands')->controller(BrandController::class)->name('brands.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::get('/trash', 'trash')->name('trash');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');
            Route::post('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Blogs
        Route::prefix('blogs')->controller(AppBlogController::class)->name('blogs.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');

            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::get('/trash', 'trash')->name('trash');
            Route::post('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');
        });

        // Contact
        Route::prefix('contacts')->controller(AppContactController::class)->name('contacts.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');

            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::get('/trash', 'trash')->name('trash');
            Route::post('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');
        });

        Route::prefix('products')->controller(ProductController::class)->name('products.')->group(function () {
            Route::get('/create',  'create')->name('create'); // Hiển thị form thêm sản phẩm
            Route::post('/store',  'store')->name('store');
            Route::get('/',  'list')->name('list');
            Route::get('/edit/{id}',  'edit')->name('edit');
            Route::post('/update/{id}',  'update')->name('update');
            Route::get('get-creat-product-sample-file', 'exportCreateExcel')->name('exportCreateExcel');
            Route::post('import-product', 'import')->name('importProduct');
            Route::get('detail/{id}', 'detail')->name('detail');
            Route::get('stas/{id}', 'stas')->name('stas');
            Route::get('product-variant-detail/{productId}/{variantId}', 'variantDetails')->name('product-variant-detail');
        });


        // Users
        Route::prefix('users')->controller(UserContronler::class)->name('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');

            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::get('/trash', 'trash')->name('trash');
            Route::post('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');

            // Biểu đồ thống kê
            Route::get('/{id}/chart_user', 'chart_user')->name('chart_user');
            Route::get('/charts', 'charts')->name('charts');
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
                Route::get('add', 'getFormAdd')->name('getFormAdd');
                Route::get('edit/{id}', 'getFormUpdate')->name('getFormUpdate');
                Route::get('/', 'list')->name('list');
                Route::get('lockOrActive/{id}', 'lockOrActive')->name('lockOrActive');
                Route::post('add', 'add')->name('add');
                Route::post('edit/{id}', 'edit')->name('edit');
            }
        );

        Route::prefix('imports')->controller(ImportController::class)->name('imports.')->group(
            function () {
                Route::get('add', 'getFormAdd')->name('getFormAdd');
                Route::get('detail/{id}', 'detail')->name('detail');
                Route::post('/upload', 'importExcel')->name('upload');

                Route::get('edit/{id}', 'getFormEdit')->name('getFormEdit');
                Route::get('/list-approved', 'listApproved')->name('listApproved');
                Route::get('/list-pending', 'listPending')->name('listPending');
                Route::get('/list-rejected', 'listRejected')->name('listRejected');

                // Route::get('lockOrActive/{id}', 'lockOrActive')->name('lockOrActive');
                Route::post('add', 'add')->name('add');
                Route::post('edit/{id}', 'edit')->name('edit');
                Route::get('accept/{id}', 'accept')->name('accept')->middleware('role:admin');
                Route::get('reject/{id}', 'reject')->name('reject')->middleware('role:admin');

                Route::get('update-price/{id}', 'updatePrice')->name('updatePrice');
            }
        );

        Route::prefix('images')->name('images.')->controller(ImageController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('update/{id}', 'update')->name('update');
            Route::get('destroy/{id}', 'destroy')->name('destroy');
            Route::get('show/{id}', 'show')->name('show');
        });
        Route::prefix('product_audits')->name('product_audits.')
            ->controller(ProductAuditController::class)->group(function () {
                Route::get('list', 'list')->name('list');
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::put('update/{id}', 'update')->name('update');
                Route::get('destroy/{id}', 'destroy')->name('destroy');
                Route::get('show/{id}', 'show')->name('show');
            });

        Route::prefix('vouchers')->name('vouchers.')->controller(VoucherController::class)->group(function () {
            Route::get('list',  'listVoucher')->name('listVoucher');
            Route::get('add',  'addVoucher')->name('addVoucher');
            Route::post('add',  'addPostVoucher')->name('addPostVoucher');
            Route::get('edit/{id}',  'editVoucher')->name('editVoucher');
            Route::put('edit/{id}',  'editPutVoucher')->name('editPutVoucher');
            Route::delete('delete/{id}',  'deleteVoucher')->name('deleteVoucher');
        });
    }
);
Route::prefix('client')->name('client.')->group(
    function () {
        Route::prefix('users')->controller(ClientAuthController::class)->name('user.')->group(
            function () {
                Route::get('/my-account', 'myAccount')->name('myAccount');
                Route::post('create-address', 'createAddress')->name('addAddress');
                Route::post('update', 'update')->name('update');
            }
        );
        Route::prefix('products')->name('products.')->group(
            function () {
                Route::get('detail/{id}', [ClientProductController::class, 'detail'])->name('detail');
                Route::get('related/{id}', [ClientProductController::class, 'related'])->name('related');
            }
        );

        Route::get('/index', [HomeController::class, 'index'])->name('home');
        Route::controller(ShopController::class)->group(function () {
            Route::get('shop', 'shop')->name('shop');
            Route::get('/shop/filter', [ShopController::class, 'filter'])->name('filter');
        });
        Route::controller(SearchController::class)->group(function () {
            Route::get('/search', [SearchController::class, 'search'])->name('search');
        });

        Route::prefix('carts')->controller(CartControllerSession::class)->name('carts.')->group(
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
                Route::post('/payment', 'payment')->name('payment');
            }
        );
        Route::prefix('orders')->controller(ClientOrderController::class)->name('orders.')->group(
            function () {
                Route::post('/store', 'store')->name('store');
                Route::get('/detail/{id}', 'detail')->name('detail');
                Route::get('/check-order', 'check')->name('check');
                Route::post('/cancel', 'cancel')->name('cancel');
            }
        );
        Route::prefix('payment')->controller(ClientPaymentController::class)->name('payment.')->group(
            function () {
                Route::get('/', 'handleVnpayReturn')->name('handleVnpayReturn');
            }
        );


        Route::prefix('my-account')->controller(MyAccountController::class)->name('my-account.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/{id}', 'update')->name('update'); // sua thong tin
        });
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
