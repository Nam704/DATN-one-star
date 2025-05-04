window.GlobalUtils = {
    // Lấy base URL từ window.location hoặc biến môi trường
    baseUrl: window.location.origin || "http://127.0.0.1:8000",

    // Định dạng giá tiền (dùng chung)
    formatPrice: (amount, currency = "VND") => {
        const parsedAmount = parseFloat(amount);
        if (isNaN(parsedAmount)) {
            console.warn("Invalid amount for formatting:", amount);
            return "0 ₫";
        }
        return new Intl.NumberFormat("vi-VN", {
            style: "currency",
            currency: currency,
        }).format(parsedAmount);
    },

    // Hiển thị thông báo (dùng chung)
    showNotification: (message, options = {}) => {
        const defaultOptions = {
            duration: 3000, // Mặc định 3 giây
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            stopOnFocus: true, // Dừng đếm thời gian khi hover
            close: true, // Mặc định hiển thị nút đóng
            persistent: false, // Mặc định không hiển thị vĩnh viễn
        };

        // Ghi đè các tùy chọn mặc định bằng options truyền vào
        const toastOptions = {
            ...defaultOptions,
            ...options,
            text: message,
        };

        // Nếu persistent = true thì set duration = 0 (hiển thị mãi)
        if (toastOptions.persistent) {
            toastOptions.duration = 0;
        }

        // Tạo toast
        const toast = Toastify(toastOptions);

        // Hiển thị toast
        toast.showToast();

        // Thêm sự kiện click cho nút đóng nếu có
        if (toastOptions.close) {
            const closeButton =
                toast.toastElement.querySelector(".toast-close");
            if (closeButton) {
                closeButton.addEventListener("click", () => {
                    toast.hideToast();
                });
            }
        }
    },

    // API gọi giỏ hàng (dùng chung)
    getCart(callback) {
        axios
            .get(`${GlobalUtils.baseUrl}/client/carts/get`)
            .then((response) => {
                if (response.data.success) {
                    callback(response.data.data);
                } else {
                    GlobalUtils.showNotification(response.data.message, {
                        backgroundColor: "#ff4444",
                    });
                }
            })
            .catch((error) => {
                console.error("Error fetching cart:", error);
                GlobalUtils.showNotification("Không thể tải giỏ hàng", {
                    backgroundColor: "#ff4444",
                });
            });
    },

    // Thêm vào giỏ hàng (dùng chung)
    addToCart(variantId, quantity = 1, callback) {
        axios
            .post(`${GlobalUtils.baseUrl}/client/carts/add`, {
                id_variant: variantId,
                quantity,
            })
            .then((response) => {
                if (response.data.success) {
                    callback(response.data.cart);
                    GlobalUtils.showNotification(response.data.message);
                } else {
                    GlobalUtils.showNotification(response.data.message, {
                        backgroundColor: "#ff4444",
                    });
                }
            })
            .catch((error) => {
                console.error("Error adding to cart:", error);
                GlobalUtils.showNotification("Không thể thêm vào giỏ hàng", {
                    backgroundColor: "#ff4444",
                });
            });
    },

    // Cập nhật số lượng (dùng chung)
    updateCart(variantId, quantity, callback) {
        axios
            .post(`${GlobalUtils.baseUrl}/client/carts/update`, {
                variantId,
                quantity,
            })
            .then((response) => {
                if (response.data.success) {
                    callback(response.data.cart);
                    GlobalUtils.showNotification(response.data.message);
                } else {
                    GlobalUtils.showNotification(response.data.message, {
                        backgroundColor: "#ff4444",
                    });
                }
            })
            .catch((error) => {
                console.error("Error updating cart:", error);
                GlobalUtils.showNotification("Không thể cập nhật giỏ hàng", {
                    backgroundColor: "#ff4444",
                });
            });
    },

    // Xóa sản phẩm (dùng chung)
    removeFromCart(variantId, callback) {
        axios
            .post(`${GlobalUtils.baseUrl}/client/carts/remove`, { variantId })
            .then((response) => {
                if (response.data.success) {
                    callback(response.data.cart);
                    GlobalUtils.showNotification(response.data.message);
                } else {
                    GlobalUtils.showNotification(response.data.message, {
                        backgroundColor: "#ff4444",
                    });
                }
            })
            .catch((error) => {
                console.error("Error removing from cart:", error);
                GlobalUtils.showNotification("Không thể xóa sản phẩm", {
                    backgroundColor: "#ff4444",
                });
            });
    },

    // Xóa toàn bộ giỏ hàng (dùng chung)
    clearCart(callback) {
        axios
            .post(`${GlobalUtils.baseUrl}/client/carts/clear`)
            .then((response) => {
                if (response.data.success) {
                    callback([]);
                    GlobalUtils.showNotification(response.data.message);
                } else {
                    GlobalUtils.showNotification(response.data.message, {
                        backgroundColor: "#ff4444",
                    });
                }
            })
            .catch((error) => {
                console.error("Error clearing cart:", error);
                GlobalUtils.showNotification("Không thể xóa giỏ hàng", {
                    backgroundColor: "#ff4444",
                });
            });
    },

    // Cập nhật UI cho mini-cart (đã sửa lại)
    // Cập nhật UI cho mini-cart
    updateCartUI(cartItems) {
        console.log("cartitem", cartItems);
        const $cart_quantity = $(".cart_quantity");
        const $cartContainer = $(".cart_items");
        $cartContainer.empty();

        if (!Array.isArray(cartItems) || cartItems.length === 0) {
            $cartContainer.append(
                "<p class='text-center'>Your cart is empty.</p>"
            );
            $cart_quantity.text("0");
            $(".cart-total").text(GlobalUtils.formatPrice(0));
            return;
        }
        $cart_quantity.text(cartItems.length);
        cartItems.forEach((item) => {
            if (!item.image || !item.name || !item.sku) {
                console.warn(
                    "Missing required data for item:",
                    item.sku || "Unknown SKU"
                );
                return;
            }

            const formattedPrice = GlobalUtils.formatPrice(item.price);
            // Giả sử item.id là ID của sản phẩm (product ID), thay vì id_variant nếu cần
            const productDetailUrl = `${GlobalUtils.baseUrl}/client/products/detail/${item.id_product}`;

            const cartItem = `
            <div class="cart_item">
                <div class="cart_img">
                    <a href="${productDetailUrl}"><img src="${item.image}" alt="${item.name}"></a>
                </div>
                <div class="cart_info">
                    <a href="${productDetailUrl}">${item.name}</a>
                    <span class="sku">SKU: ${item.sku}</span>
                    <span class="quantity">Qty: ${item.quantity}</span>
                    <span class="price_cart">${formattedPrice}</span>
                </div>
                <div class="cart_remove">
                    <a href="#" class="delete_item" data-id="${item.id_variant}">
                        <i class="ion-android-close"></i>
                    </a>
                </div>
            </div>
        `;
            $cartContainer.append(cartItem);
        });

        // Tính toán và cập nhật Sub total và Total
        const total = cartItems.reduce(
            (sum, item) => sum + parseFloat(item.price) * item.quantity,
            0
        );

        $(".cart-total").text(GlobalUtils.formatPrice(total));
    },
};
