<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
        // Check if ordering is allowed during current time
        $user = Auth::guard('student')->user();

        if ($user && !Auth::guard('student')->user()->can('can-place-order')) {
            return redirect()->route('student.home')->with('error',
                'Sorry, we are currently CLOSED. Orders can only be placed during our operating hours: 8:00 AM - 3:00 AM daily. Please come back during operating hours!');
        }

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
            'name' => 'required|string|max:255|regex:/^[^<>]*$/',
            //add phone regex validation
            'phone' => 'required|string|max:20|regex:/^[^<>]*$/',
                    
            //add address max length validation
            'address' => 'required|string|max:500|regex:/^[^<>]*$/',
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
            'delivery_option' => 'required|string|in:Pick Up,15 - 20 Minutes,Now',
        ]);

        // Calculate fee server-side based on option
        $deliveryFees = [
            'Pick Up' => 0.00,
            '15 - 20 Minutes' => 3.00,
            'Now' => 5.00,
        ];

        $deliveryFee = $deliveryFees[$validated['delivery_option']];
        Session::put('shipping_fee', $deliveryFee);
        Session::put('delivery_option', $validated['delivery_option']);

        $cart = Session::get('cart', []);

        $summary = $this->calculateCartSummary($cart, $deliveryFee);
        Session::put('subtotal', $summary['subtotal']);
        Session::put('sales_tax', $summary['salesTax']);
        Session::put('order_total', $summary['total']);

        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        // Get values from session (already calculated in processDelivery)
        $cart = Session::get('cart', []);
        $deliveryOption = Session::get('delivery_option');
        $shippingFee = Session::get('shipping_fee', 0.00);
        $subtotal = Session::get('subtotal', 0.00);
        $salesTax = Session::get('sales_tax', 0.00);
        $orderTotal = Session::get('order_total', 0.00);

        // Use values already stored in session, don't recalculate
        return view('checkout.payment', compact('cart', 'subtotal', 'salesTax', 'shippingFee', 'orderTotal', 'deliveryOption'));
    }

    public function processPayment(Request $request)
    {
        // 1. CHECK TIME-BASED RESTRICTIONS
        $user = Auth::guard('student')->user();

        if ($user && !$user->can('can-place-order')) {
            Log::warning('Order placement attempted outside operating hours', [
                'user' => $user->matric_no,
                'time' => now()->format('Y-m-d H:i:s')
            ]);

            return redirect()->route('student.home')->with('error',
                'Sorry, we are currently CLOSED. Orders can only be placed during our operating hours: 8:00 AM - 3:00 AM daily. Please come back during operating hours!');
        }

        // 2. VALIDATE PAYMENT METHOD ONLY
        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,credit_card,bank_transfer,other',
        ]);

        // 3. VALIDATE USER

        if (!$user || !$user->matric_no) {
            Log::error('Unauthorized payment attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return redirect()->route('checkout.form')->with('error', 'You must be logged in.');
        }

        // 4. AUTHORIZE CART CHECKOUT
        Gate::forUser($user)->authorize('checkout', \App\Http\Controllers\CartController::class);

        // 5. GET DATA FROM SESSION
        $cart = Session::get('cart', []);
        $shipping = Session::get('shipping', []);
        $deliveryOption = Session::get('delivery_option');

        // 6. VALIDATE REQUIRED DATA
        if (empty($cart)) {
            Log::warning('Payment attempted with empty cart', ['user' => $user->matric_no]);
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        if (empty($shipping) || !isset($shipping['address'])) {
            Log::warning('Payment attempted without shipping info', ['user' => $user->matric_no]);
            return redirect()->route('checkout.form')->with('error', 'Shipping information is missing.');
        }

        if (empty($deliveryOption)) {
            Log::warning('Payment attempted without delivery option', ['user' => $user->matric_no]);
            return redirect()->route('checkout.delivery')->with('error', 'Please select a delivery option.');
        }

        // 5. RECALCULATE EVERYTHING SERVER-SIDE (don't trust session!)
        $deliveryFees = [
            'Pick Up' => 0.00,
            '15 - 20 Minutes' => 3.00,
            'Now' => 5.00,
        ];

        // Validate delivery option
        if (!array_key_exists($deliveryOption, $deliveryFees)) {
            Log::error('Invalid delivery option in payment', [
                'user' => $user->matric_no,
                'option' => $deliveryOption
            ]);
            return redirect()->route('checkout.delivery')->with('error', 'Invalid delivery option.');
        }

        $shippingFee = $deliveryFees[$deliveryOption];
        
        // Recalculate cart totals and validate items
        $subtotal = 0;
        foreach ($cart as $item) {
            // Validate each item structure
            if (!isset($item['price']) || !isset($item['quantity']) || !isset($item['name'])) {
                Log::error('Invalid cart item structure', [
                    'user' => $user->matric_no,
                    'item' => $item
                ]);
                return redirect()->route('cart.show')->with('error', 'Invalid cart data. Please review your cart.');
            }
            
            // Validate positive numbers
            if ($item['price'] <= 0 || $item['quantity'] < 1) {
                Log::error('Invalid cart item values', [
                    'user' => $user->matric_no,
                    'item' => $item
                ]);
                return redirect()->route('cart.show')->with('error', 'Invalid item prices or quantities.');
            }

            // Validate reasonable quantity (prevent abuse)
            if ($item['quantity'] > 100) {
                Log::warning('Excessive quantity in cart', [
                    'user' => $user->matric_no,
                    'item' => $item
                ]);
                return redirect()->route('cart.show')->with('error', 'Quantity per item cannot exceed 100.');
            }
            
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Calculate tax and total
        $salesTax = round($subtotal * 0.065, 2);
        $orderTotal = round($subtotal + $salesTax + $shippingFee, 2);

        // 6. VALIDATE AMOUNTS ARE REASONABLE
        if ($orderTotal <= 0 || $orderTotal > 10000) {
            Log::error('Suspicious order total', [
                'user' => $user->matric_no,
                'total' => $orderTotal,
                'subtotal' => $subtotal,
                'tax' => $salesTax,
                'shipping' => $shippingFee
            ]);
            return redirect()->route('cart.show')->with('error', 'Order total is invalid. Please contact support.');
        }

        // 7. CREATE ORDER WITH TRANSACTION (for data consistency)
        try {
            DB::beginTransaction();

            $order = Order::create([
                'student_id' => $user->matric_no,
                'total_amount' => $orderTotal, // Server-calculated
                'status' => 'Pending',
                'address' => $shipping['address'],
                'delivery_option' => $deliveryOption,
                'payment_method' => $validated['payment_method'],
                'shipping_fee' => $shippingFee, // Server-calculated
                'sales_tax' => $salesTax, // Server-calculated
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
                    'image' => $item['image'] ?? null,
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'user' => $user->matric_no,
                'total' => $orderTotal
            ]);

            // Clear session only after successful order
            Session::forget(['cart', 'shipping', 'delivery_option', 'shipping_fee', 'subtotal', 'sales_tax', 'order_total']);

            return redirect()->route('checkout.receipt', ['order' => $order->id])
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order creation failed', [
                'user' => $user->matric_no,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('checkout.form')
                ->with('error', 'Failed to process order. Please try again or contact support.');
        }
    }

    public function receipt($orderId)
    {
        // Get authenticated student
        $student = Auth::guard('student')->user();

        // Type cast and validate ID
        $orderId = (int) $orderId;

        if ($orderId <= 0) {
            abort(404);
        }

        $order = Order::with('orderItems', 'shipping')->findOrFail($orderId);

        // Authorize viewing receipt (replaces manual check)
        Gate::forUser($student)->authorize('viewReceipt', $order);

        return view('checkout.receipt', compact('order'));
    }

    public function orderTracking()
    {


        return view('checkout.order_tracking');
    }
}
