<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product_variant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;

class CartService
{
    public function getCart(): Collection|array
    {
        return Auth::check()
            ? $this->getDatabaseCart(Auth::id())
            : $this->getSessionCart();
    }

    public function store()
    {
        $cart = Cart::create(
            [
                'id_user' => Auth::id(),
            ]
        );
    }

    public function addToCart(int $variantId, int $quantity = 1): array
    {
        $variant = Product_variant::findOrFail($variantId);

        if ($variant->quantity < $quantity) {
            return [
                'error' => 'Sản phẩm không đủ số lượng trong kho. Còn lại: ' . $variant->quantity
            ];
        }

        return Auth::check()
            ? $this->addToDatabaseCart(Auth::id(), $variant, $quantity)
            : $this->addToSessionCart($variant, $quantity);
    }

    public function updateCart(int $variantId, int $quantity): array
    {
        if ($quantity < 1) {
            return ['error' => 'Số lượng phải lớn hơn 0'];
        }

        $variant = Product_variant::findOrFail($variantId);

        if ($variant->quantity < $quantity) {
            return [
                'error' => 'Số lượng yêu cầu vượt quá tồn kho. Còn lại: ' . $variant->quantity
            ];
        }

        return Auth::check()
            ? $this->updateDatabaseCart(Auth::id(), $variantId, $quantity)
            : $this->updateSessionCart($variantId, $quantity);
    }

    public function removeFromCart(int $variantId): array
    {
        return Auth::check()
            ? $this->removeFromDatabaseCart(Auth::id(), $variantId)
            : $this->removeFromSessionCart($variantId);
    }

    public function clearCart(): array
    {
        return Auth::check()
            ? $this->clearDatabaseCart(Auth::id())
            : $this->clearSessionCart();
    }

    public function syncCartOnLogin(int $userId): void
    {
        $sessionCart = $this->getSessionCart();
        if (empty($sessionCart)) return;

        DB::transaction(function () use ($userId, $sessionCart) {
            foreach ($sessionCart as $item) {
                $this->addToDatabaseCart($userId, Product_variant::find($item['id_variant']), $item['quantity']);
            }
            $this->clearSessionCart();
        });
    }

    private function getDatabaseCart(int $userId): Collection
    {
        return Cart::firstOrCreate(['id_user' => $userId])
            ->details()
            ->with(['variant.images', 'variant.product', 'variant.attributeValues'])
            ->whereHas('variant', function ($query) {
                $query->where('status', 'active') // Chỉ lấy biến thể active
                    ->whereNull('deleted_at'); // Không bị xóa mềm
            })
            ->whereHas('variant.product', function ($query) {
                $query->where('status', 'active') // Chỉ lấy sản phẩm active
                    ->whereNull('deleted_at'); // Không bị xóa mềm
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id_variant' => $item->id_variant,
                    'sku' => $item->variant->sku,
                    'price' => $item->variant->price,
                    'quantity' => $item->quantity,
                    'image' => $item->variant->images->url ?? null,
                    'name' => $item->variant->product->name,
                    'values' => $item->variant->attributeValues->map(function ($attr) {
                        return [
                            'attribute_name' => $attr->attribute_name,
                            'value' => $attr->value,
                        ];
                    }),
                ];
            });
    }

    private function addToDatabaseCart(int $userId, Product_variant $variant, int $quantity): array
    {
        return DB::transaction(function () use ($userId, $variant, $quantity) {
            $cart = Cart::firstOrCreate(['id_user' => $userId]);

            $cartDetail = $cart->details()->where('id_variant', $variant->id)->first();
            $currentQuantity = $cartDetail ? $cartDetail->quantity : 0;
            $newQuantity = $currentQuantity + $quantity;

            if ($variant->quantity < $newQuantity) {
                return [
                    'error' => 'Số lượng yêu cầu vượt quá tồn kho. Còn lại: ' . $variant->quantity
                ];
            }

            if ($cartDetail) {
                $cart->details()->where('id_variant', $variant->id)->increment('quantity', $quantity);
            } else {
                $cart->details()->create([
                    'id_variant' => $variant->id,
                    'quantity' => $quantity,
                ]);
            }

            return ['success' => 'Đã thêm vào giỏ hàng'];
        });
    }

    private function updateDatabaseCart(int $userId, int $variantId, int $quantity): array
    {
        Cart::where('id_user', $userId)
            ->first()
            ->details()
            ->where('id_variant', $variantId)
            ->update(['quantity' => $quantity]);

        return ['success' => 'Cập nhật giỏ hàng thành công'];
    }

    private function removeFromDatabaseCart(int $userId, int $variantId): array
    {
        Cart::where('id_user', $userId)
            ->first()
            ->details()
            ->where('id_variant', $variantId)
            ->delete();

        return ['success' => 'Đã xóa sản phẩm'];
    }

    private function clearDatabaseCart(int $userId): array
    {
        Cart::where('id_user', $userId)->first()->details()->delete();
        return ['success' => 'Đã xóa giỏ hàng'];
    }

    private function getSessionCart(): array
    {
        $cart = Session::get('cart', []);
        $validCart = [];

        foreach ($cart as $variantId => $item) {
            $variant = Product_variant::with('product')->find($variantId);
            if (
                $variant && $variant->status === 'active' && !$variant->trashed() &&
                $variant->product && $variant->product->status === 'active' && !$variant->product->trashed()
            ) {
                $validCart[$variantId] = $item;
            }
        }

        return $validCart;
    }

    private function addToSessionCart(Product_variant $variant, int $quantity): array
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$variant->id])) {
            $cart[$variant->id]['quantity'] += $quantity;
        } else {
            $cart[$variant->id] = $this->formatCartItem($variant, $quantity);
        }

        Session::put('cart', $cart);
        return ['success' => 'Đã thêm vào giỏ hàng'];
    }

    private function updateSessionCart(int $variantId, int $quantity): array
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$variantId])) {
            $cart[$variantId]['quantity'] = $quantity;
            Session::put('cart', $cart);
            return ['success' => 'Cập nhật giỏ hàng thành công'];
        }
        return ['error' => 'Sản phẩm không tồn tại'];
    }

    private function removeFromSessionCart(int $variantId): array
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$variantId])) {
            unset($cart[$variantId]);
            Session::put('cart', $cart);
            return ['success' => 'Đã xóa sản phẩm'];
        }
        return ['error' => 'Sản phẩm không tồn tại'];
    }

    private function clearSessionCart(): array
    {
        Session::forget('cart');
        return ['success' => 'Đã xóa giỏ hàng'];
    }

    private function formatCartItem(Product_variant $variant, int $quantity): array
    {
        return [
            'id_variant' => $variant->id,
            'sku' => $variant->sku,
            'price' => $variant->price,
            'quantity' => $quantity,
            'image' => $variant->image_url,
            'name' => $variant->product->name
        ];
    }
}
