<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{userId}', function ($user, $userId) {
    return (int) $user->user_id === (int) $userId;
});

Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = \App\Models\Order::find($orderId);
    
    if (!$order) return false;

    return (int) $user->id === (int) $order->user_id || $user->is_admin == 1; 
});
