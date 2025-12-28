<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    private function calculateCartSummary($cart, $shippingFee = 0.00)
    {
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $salesTax = round($subtotal * 0.065, 2);
        $total = round($subtotal + $salesTax + $shippingFee, 2);

        return compact('subtotal', 'salesTax', 'shippingFee', 'total');
    }

    public function form()
    {
        $cart = Session::get('cart', []);
        $shipping = Session::get('shipping', []);
        $shippingFee = Session::get('shipping_fee', 0.00);
        $summary = $this->calculateCartSummary($cart, $shippingFee);

        return view('checkout.index', array_merge([
            'cart' => $cart,
            'shipping' => $shipping,
        ], $summary));
    }

    public function process(Request $request)
    {
        //no cart quantity limits
        // $validated = $request->validate([
        //     'name' => 'required',
        //     'phone' => 'required',
        //     'address' => 'required',
        // ]);

        //add cart quantity limits
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'cart.*.quantity' => 'required|integer|min:1|max:100',
        ]);


        Session::put('shipping', $validated);
        return redirect()->route('checkout.delivery');
    }

    public function delivery()
    {
        $cart = Session::get('cart', []);
        $shipping = Session::get('shipping', []);
        $deliveryOption = Session::get('delivery_option', 'Pick Up');

        $shippingFee = match ($deliveryOption) {
            '15 - 20 Minutes' => 3.00,
            'Now' => 5.00,
            default => 0.00,
        };

        $summary = $this->calculateCartSummary($cart, $shippingFee);

        Session::put('shipping_fee', $shippingFee);
        Session::put('subtotal', $summary['subtotal']);
        Session::put('sales_tax', $summary['salesTax']);
        Session::put('order_total', $summary['total']);

        return view('checkout.delivery', array_merge([
            'cart' => $cart,
            'deliveryOption' => $deliveryOption,
            'shipping' => $shipping,
        ], $summary));
    }

    public function processDelivery(Request $request)
    {
        // $validated = $request->validate([
        //     'delivery_option' => 'required|string',
        //     'delivery_fee' => 'required|numeric', //user can manipulate this
        // ]);

        $validated = $request->validate([
        'delivery_option' => 'required|string|in:standard,express,pickup',
        ]);

        // Calculate fee server-side based on option
        $deliveryFees = [
            'standard' => 5.00,
            'express' => 10.00,
            'pickup' => 0.00,
        ];

        $deliveryFee = $deliveryFees[$validated['delivery_option']];
        Session::put('shipping_fee', $deliveryFee);

        $cart = Session::get('cart', []);
        // Session::put('delivery_option', $validated['delivery_option']);
        // Session::put('shipping_fee', $validated['delivery_fee']);

        $summary = $this->calculateCartSummary($cart, $deliveryFee);
        Session::put('subtotal', $summary['subtotal']);
        Session::put('sales_tax', $summary['salesTax']);
        Session::put('order_total', $summary['total']);

        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        // Get cart from session
        $cart = session('cart', []);

        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $salesTax = $subtotal * 0.065; // 6.5% sales tax
        $shippingFee = $subtotal > 50 ? 0 : 5; // Free shipping over RM50
        $orderTotal = $subtotal + $salesTax + $shippingFee;

        // Store in session for later use
        session([
            'subtotal' => $subtotal,
            'sales_tax' => $salesTax,
            'shipping_fee' => $shippingFee,
            'order_total' => $orderTotal
        ]);

        return view('checkout.payment', compact('cart', 'subtotal', 'salesTax', 'shippingFee', 'orderTotal'));
    }

    public function processPayment(Request $request)
    {
        $cart = Session::get('cart');
        $shipping = Session::get('shipping');

        Log::info('Auth student:', ['user' => Auth::guard('student')->user()]);
        Log::info('Cart:', ['cart' => $cart]);
        Log::info('Shipping:', ['shipping' => $shipping]);
        Log::info('Session:', Session::all());


        //no payment whitelist validation
        $validated = $request->validate([
            'payment_method' => 'required|string',
        ]);

        //add payment whitelist validation
        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,credit_card,bank_transfer,other',
        ]);


        $user = Auth::guard('student')->user();

        if (!$user || !$user->matric_no) {
        Log::error('Student user not authenticated or missing ID.');
        return redirect()->route('checkout.form')->with('error', 'You must be logged in.');
        }

        $cart = Session::get('cart', []);
        $shipping = Session::get('shipping', []);
        $deliveryOption = Session::get('delivery_option');
        $shippingFee = Session::get('shipping_fee', 0.00);
        $subtotal = Session::get('subtotal', 0.00);
        $salesTax = Session::get('sales_tax', 0.00);
        $orderTotal = Session::get('order_total', 0.00);

        if (empty($cart) || empty($shipping)) {
            return redirect()->route('checkout.form')->with('error', 'Missing order information.');
        }



        $order = Order::create([
            'student_id' => $user->matric_no,
            'total_amount' => $orderTotal,
            'status' => 'Pending',
            'address' => $shipping['address'],
            'delivery_option' => $deliveryOption,
            'payment_method' => $validated['payment_method'],
            'shipping_fee' => $shippingFee,
            'sales_tax' => $salesTax,
        ]);

        Shipping::create([
            'order_id' => $order->id,
            'name' => $shipping['name'],
            'phone' => $shipping['phone'],
            'address' => $shipping['address'],
        ]);



        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'name' => $item['name'],
                'price' => $item['price'],
                'image' => $item['image'],
                'quantity' => $item['quantity'],
            ]);
        }

        Session::forget([ 'cart', 'shipping', 'delivery_option', 'shipping_fee', 'subtotal', 'sales_tax', 'order_total' ]);

        return redirect()->route('checkout.receipt', ['order' => $order->id])->with('success', 'Payment processed!');
    }

    public function receipt($orderId)
    {
        // Type cast and validate ID
        $orderId = (int) $orderId;

        if ($orderId <= 0) {
            abort(404);
        }

        $user = Auth::guard('student')->user();
        $order = Order::with('orderItems', 'shipping')->findOrFail($orderId);

        if ($user->matric_no !== $order->student_id) {
            abort(403); // Prevent others from viewing this receipt
        }

        return view('checkout.receipt', compact('order'));
    }

    public function orderTracking()
    {


        return view('checkout.order_tracking');
    }
}
