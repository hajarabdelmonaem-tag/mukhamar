<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        return view('admin.dashboard.index', [
            'title' => __('admin.dashboard'),
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalUsers' => User::count(),
            'totalCategories' => Category::count(),
            'totalRevenue' => Order::where('payment_status', 'paid')->sum('total'),
            'latestOrders' => Order::with('user')->latest()->take(5)->get(),
        ]);
    }
}
