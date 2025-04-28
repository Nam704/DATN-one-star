<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;



Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('chat', function (User $user) {


    return ['id' => $user->id, 'name' => $user->name];
});
Broadcast::channel('private-notifications', function ($user) {
    if ($user->isAdmin() || $user->isEmployee()) {
        return true;
    }
});
Broadcast::channel('public', function () {
    return true;
});
Broadcast::channel('admin', function ($user) {
    if ($user->isAdmin()) {
        return true;
    }
});

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('orders.{orderId}', function (User $user, int $orderId) {
    $order = Order::find($orderId);
    return $order && $user->id === $order->id_user;
});
Broadcast::channel('employee', function ($user) {
    if ($user->isEmployee()) {
        return true;
    }
});
