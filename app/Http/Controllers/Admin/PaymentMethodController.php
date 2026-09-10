<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of payment methods.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order')->get();

        return view('admin.payment_methods.index', [
            'title' => __('admin.payment_methods.title'),
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Show the form for creating a new payment method.
     */
    public function create()
    {
        return view('admin.payment_methods.create', ['title' => __('admin.payment_methods.add')]);
    }

    /**
     * Store a newly created payment method.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:payment_methods'],
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        PaymentMethod::create($data);

        return redirect()->route('admin.payment_methods.index')
            ->with('success', __('admin.payment_methods.created'));
    }

    /**
     * Show the form for editing a payment method.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment_methods.edit', [
            'title' => __('admin.payment_methods.edit'),
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Update the specified payment method.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:payment_methods,code,'.$paymentMethod->id],
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $paymentMethod->update($data);

        return redirect()->route('admin.payment_methods.index')
            ->with('success', __('admin.payment_methods.updated'));
    }

    /**
     * Remove the specified payment method.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->route('admin.payment_methods.index')
            ->with('success', __('admin.payment_methods.deleted'));
    }
}
