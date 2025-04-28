import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/utilities/global.js",
                "resources/js/client/cartDetail.js",
                "resources/js/client/productDetail.js",
                "resources/js/client/orderDetail.js",

                "resources/js/client/checkout.js",
                "resources/js/admin.order.js",
                "resources/js/admin/listOrder.js",
                "resources/js/admin/notification.js",

                "resources/js/public.js",
                "resources/js/private.js",
                "resources/js/admin.js",
                "resources/js/employee.js",
                "resources/js/user.js",
                "resources/js/client.js",

                "resources/js/clientDetail.js",
                "resources/js/orderClientDetail.js",
                "resources/js/chat.js",
                "resources/js/address.js",
            ],
            refresh: true,
        }),
    ],
});
