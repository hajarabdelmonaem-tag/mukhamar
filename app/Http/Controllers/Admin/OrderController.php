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
        request()->validate([
            'status' => ['required', 'in:pending,processing,in_transit,delivered,cancelled'],
        ]);

        $order->update(['status' => request('status')]);

        return back()->with('success', __('admin.orders.status_updated'));
    }
}
