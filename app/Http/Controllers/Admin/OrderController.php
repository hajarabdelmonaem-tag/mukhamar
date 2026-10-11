<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        $orders = Order::with('user')
            ->latest()
            ->when(request('status'), fn ($q) => $q->where('status', request('status')))
            ->paginate(15);

        return view('admin.orders.index', [
            'title' => __('admin.orders.title'),
            'orders' => $orders,
            'currentStatus' => request('status'),
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load('user', 'address', 'coupon', 'items');

        return view('admin.orders.show', [
            'title' => __('admin.table.order_no').' '.$order->order_no,
            'order' => $order,
        ]);
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Order $order)
    {
        $validated = request()->validate([
            'status' => ['required', 'in:pending,confirmed,processing,in_transit,delivered,cancelled'],
        ]);

        $status = $validated['status'];

        $timestampColumn = match ($status) {
            Order::STATUS_CONFIRMED => 'confirmed_at',
            Order::STATUS_IN_TRANSIT => 'shipped_at',
            Order::STATUS_DELIVERED => 'delivered_at',
            Order::STATUS_CANCELLED => 'cancelled_at',
            default => null,
        };

        $attributes = ['status' => $status];

        if ($timestampColumn !== null && $order->{$timestampColumn} === null) {
            $attributes[$timestampColumn] = now();
        }

        $order->update($attributes);

        return back()->with('success', __('admin.orders.status_updated'));
    }
}
