# Authorization Enhancements - UniMeal System

## Overview
This document details the authorization enhancements implemented for the UniMeal system, focusing on **resource-based policies** and **time-based access control** using Laravel's authorization features.

---

## Table of Contents
1. [Order Authorization Policy](#1-order-authorization-policy-resource-based)
2. [Cart Authorization Policy](#2-cart-authorization-policy-resource-based)
3. [Time-Based Order Restrictions](#3-time-based-order-restrictions-attribute-based)
4. [Policy & Gate Registration](#policy--gate-registration)
5. [Code Implementation](#code-implementation)
6. [Testing Guide](#testing-the-authorization-enhancements)
7. [Blade Directives](#blade-directives-for-authorization)
8. [Configuration Options](#configuration-options)

---

## AUTHORIZATION ENHANCEMENTS IMPLEMENTED

### 1. Order Authorization Policy (Resource-Based)

**Implementation Method**: Laravel Policy for Order resource authorization

**Files Created**:
- `app/Policies/OrderPolicy.php` - Order authorization policy

**Files Modified**:
- `app/Http/Controllers/OrderController.php` - Added policy checks
- `app/Http/Controllers/CheckoutController.php` - Added receipt authorization
- `app/Providers/AuthServiceProvider.php` - Registered policy

**Purpose**:
Ensure students can only view and manage their own orders. Prevents unauthorized access to other students' order information and enforces proper order status transitions.

**Policy Methods**:

#### 1.1 `viewAny(Student $student)`
- **Purpose**: Check if student can view order list
- **Logic**: All authenticated students can view their own order history
- **Returns**: `true`

#### 1.2 `view(Student $student, Order $order)`
- **Purpose**: Check if student can view specific order details
- **Logic**: Verifies `order->student_id === student->matric_no`
- **Returns**: `Response::allow()` or `Response::deny('You do not have permission to view this order.')`

#### 1.3 `cancel(Student $student, Order $order)`
- **Purpose**: Check if student can cancel order
- **Logic**:
  - Verifies order ownership
  - Only allows cancellation for orders with status 'pending' or 'preparing'
  - Denies for 'ready', 'completed', or 'cancelled' statuses
- **Returns**: `Response::allow()` or detailed denial message with current status

#### 1.4 `viewReceipt(Student $student, Order $order)`
- **Purpose**: Check if student can view order receipt
- **Logic**: Same as `view()` - must own the order
- **Returns**: `Response::allow()` or `Response::deny('You do not have permission to view this receipt.')`

**Security Benefits**:
- ✅ **Order ownership enforcement**: Students can only access their own orders
- ✅ **Receipt protection**: Prevents viewing other students' receipts
- ✅ **Status-based cancellation**: Only pending/preparing orders can be cancelled
- ✅ **Centralized authorization**: All order access logic in one policy
- ✅ **Clear error messages**: Users know exactly why access is denied

**Replaced Manual Checks**:
- Removed `if ($order->student_id !== Auth::guard('student')->id())` from OrderController
- Removed `if ($user->matric_no !== $order->student_id)` from CheckoutController
- Now uses consistent policy-based authorization throughout

---

### 2. Cart Authorization Policy (Resource-Based)

**Implementation Method**: Laravel Policy for session-based cart authorization

**Files Created**:
- `app/Policies/CartPolicy.php` - Cart authorization policy

**Files Modified**:
- `app/Http/Controllers/CartController.php` - Added policy checks
- `app/Providers/AuthServiceProvider.php` - Registered policy
- `app/Http/Controllers/CheckoutController.php` - Added checkout authorization

**Purpose**:
Ensure only the cart owner can modify their cart and prevent session hijacking or cart manipulation.

**Policy Methods**:

#### 2.1 `view(Student $student)`
- **Purpose**: Check if student can view cart
- **Logic**: All authenticated students can view their cart
- **Returns**: `true`

#### 2.2 `add(Student $student)`
- **Purpose**: Check if student can add items to cart
- **Logic**: All authenticated students can add items
- **Returns**: `Response::allow()`

#### 2.3 `update(Student $student)`
- **Purpose**: Check if student can update cart items
- **Logic**:
  - Verifies cart belongs to current student via `cart_owner` session
  - Claims ownership if no owner set
- **Returns**: `Response::allow()` or `Response::deny('You cannot modify another user\'s cart.')`

#### 2.4 `remove(Student $student)`
- **Purpose**: Check if student can remove items from cart
- **Logic**: Same as `update()` - must own the cart
- **Returns**: `Response::allow()` or denial message

#### 2.5 `checkout(Student $student)`
- **Purpose**: Check if student can proceed to checkout
- **Logic**:
  - Verifies cart ownership
  - Checks cart is not empty
- **Returns**: `Response::allow()` or appropriate denial message

**Security Benefits**:
- ✅ **Prevents cart hijacking**: Only cart owner can modify
- ✅ **Session ownership tracking**: Cart tied to matric_no
- ✅ **Empty cart validation**: Prevents checkout with empty cart
- ✅ **Centralized authorization**: All cart logic in one policy
- ✅ **Audit trail ready**: Can log policy denials

---

### 3. Time-Based Order Restrictions (Attribute-Based)

**Implementation Method**: Laravel Gates for time-based access control

**Files Modified**:
- `app/Providers/AuthServiceProvider.php` - Defined gates
- `app/Http/Controllers/CheckoutController.php` - Applied gates
- `config/app.php` - Changed timezone to Asia/Kuala_Lumpur

**Purpose**:
Enforce business hours and prevent orders during closed periods, holidays, or restricted times.

**Gates Defined**:

#### 3.1 `order-during-business-hours`
**Operating Hours**: **8:00 AM - 3:00 AM daily** (19 hours/day)
- **Open**: 8 AM to 3 AM next day (hours: 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2)
- **Closed**: 3 AM to 8 AM (hours: 3, 4, 5, 6, 7) - Kitchen preparation time

**Returns**: `true` if current hour is NOT in closed hours (3-7 AM), `false` during closed hours

#### 3.2 `order-on-weekday` (Optional)
**Purpose**: Optional restriction to prevent weekend ordering
**Status**: Currently commented out in `can-place-order` gate
**Returns**: `true` for Monday-Friday, `false` for Saturday-Sunday

#### 3.3 `order-not-on-holiday`
**Purpose**: Prevent ordering during university holidays
**Returns**: `true` if not a holiday, `false` if holiday

#### 3.4 `can-place-order` (Combined Gate)
**Purpose**: Master gate that combines all time-based restrictions

**Security Benefits**:
- ✅ **Enforces business hours**: No orders outside allowed times
- ✅ **Holiday protection**: Prevents orders on holidays
- ✅ **Audit logging**: Logs unauthorized attempts
- ✅ **User feedback**: Clear error messages with popup alerts
- ✅ **Flexible configuration**: Easy to modify hours/holidays

**Error Messages**:
```
"Sorry, we are currently CLOSED. Orders can only be placed during our operating hours: 8:00 AM - 3:00 AM daily. Please come back during operating hours!"
```

---

## Policy & Gate Registration

**File**: `app/Providers/AuthServiceProvider.php`

**Policy Mappings**:
```php
protected $policies = [
    \App\Http\Controllers\CartController::class => CartPolicy::class,
    Order::class => OrderPolicy::class,
];
```

**Boot Method**:
```php
public function boot(): void
{
    // Register policies
    $this->registerPolicies();

    // Define Time-Based Order Restrictions Gates
    $this->defineOrderingGates();
}
```

**Registered in**: `bootstrap/providers.php`

---

## Code Implementation

### OrderPolicy.php (Complete Code)

```php
<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Student;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the student can view any orders.
     */
    public function viewAny(Student $student): bool
    {
        // All authenticated students can view their own order list
        return true;
    }

    /**
     * Determine whether the student can view the order.
     */
    public function view(Student $student, Order $order): Response
    {
        // Verify order belongs to the authenticated student
        if ($order->student_id === $student->matric_no) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this order.');
    }

    /**
     * Determine whether the student can cancel the order.
     */
    public function cancel(Student $student, Order $order): Response
    {
        // Verify order ownership
        if ($order->student_id !== $student->matric_no) {
            return Response::deny('You do not have permission to cancel this order.');
        }

        // Check order status - only pending or preparing orders can be cancelled
        $cancellableStatuses = ['pending', 'preparing'];

        if (!in_array($order->status, $cancellableStatuses)) {
            return Response::deny(
                'Orders can only be cancelled when they are pending or preparing. ' .
                'This order is currently: ' . $order->status
            );
        }

        return Response::allow();
    }

    /**
     * Determine whether the student can view the order receipt.
     */
    public function viewReceipt(Student $student, Order $order): Response
    {
        // Same logic as view - must own the order
        if ($order->student_id === $student->matric_no) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this receipt.');
    }
}
```

### CartPolicy.php (Complete Code)

```php
<?php

namespace App\Policies;

use App\Models\Student;
use Illuminate\Auth\Access\Response;

class CartPolicy
{
    /**
     * Determine whether the student can view their cart.
     */
    public function view(Student $student): bool
    {
        // Only authenticated students can view cart
        return true;
    }

    /**
     * Determine whether the student can add items to cart.
     */
    public function add(Student $student): Response
    {
        // All authenticated students can add to cart
        return Response::allow();
    }

    /**
     * Determine whether the student can update cart items.
     */
    public function update(Student $student): Response
    {
        // Ensure the cart session belongs to this student
        $cartOwner = session('cart_owner');

        // If no owner set, claim ownership
        if (!$cartOwner) {
            session(['cart_owner' => $student->matric_no]);
            return Response::allow();
        }

        // Verify cart belongs to current student
        if ($cartOwner === $student->matric_no) {
            return Response::allow();
        }

        return Response::deny('You cannot modify another user\'s cart.');
    }

    /**
     * Determine whether the student can remove items from cart.
     */
    public function remove(Student $student): Response
    {
        // Same logic as update - must own the cart
        $cartOwner = session('cart_owner');

        if (!$cartOwner || $cartOwner === $student->matric_no) {
            return Response::allow();
        }

        return Response::deny('You cannot modify another user\'s cart.');
    }

    /**
     * Determine whether the student can proceed to checkout.
     */
    public function checkout(Student $student): Response
    {
        // Verify cart ownership
        $cartOwner = session('cart_owner');

        if ($cartOwner && $cartOwner !== $student->matric_no) {
            return Response::deny('You cannot checkout another user\'s cart.');
        }

        // Verify cart is not empty
        $cart = session('cart', []);
        if (empty($cart)) {
            return Response::deny('Your cart is empty.');
        }

        return Response::allow();
    }
}
```

### AuthServiceProvider.php - Time Gates (Complete Code)

```php
protected function defineOrderingGates(): void
{
    /**
     * Gate: order-during-business-hours
     * Operating Hours: 8:00 AM - 3:00 AM daily
     */
    Gate::define('order-during-business-hours', function (Student $student) {
        $currentHour = now()->hour;

        // Operating hours: 8 AM - 3 AM (8, 9, 10...23, 0, 1, 2)
        // Closed: 3 AM - 8 AM (3, 4, 5, 6, 7)
        $closedHours = [3, 4, 5, 6, 7];

        // Allow orders if NOT in closed hours
        return !in_array($currentHour, $closedHours);
    });

    /**
     * Gate: order-on-weekday (Optional)
     */
    Gate::define('order-on-weekday', function (Student $student) {
        $dayOfWeek = now()->dayOfWeek;

        // 0 = Sunday, 6 = Saturday
        // Allow Monday (1) through Friday (5)
        return $dayOfWeek >= 1 && $dayOfWeek <= 5;
    });

    /**
     * Gate: order-not-on-holiday
     */
    Gate::define('order-not-on-holiday', function (Student $student) {
        // Example: Block ordering on specific dates
        $holidays = [
            '2025-12-25', // Christmas
            '2026-01-01', // New Year
            // Add more dates or fetch from database
        ];

        $today = now()->format('Y-m-d');

        return !in_array($today, $holidays);
    });

    /**
     * Gate: can-place-order
     * Combined gate that checks all time-based restrictions
     */
    Gate::define('can-place-order', function (Student $student) {
        // Check all time-based restrictions
        return Gate::check('order-during-business-hours')
            && Gate::check('order-not-on-holiday');
            // Uncomment if you want weekday restriction:
            // && Gate::check('order-on-weekday');
    });
}
```

### OrderController.php (With Authorization)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Show order history in student dashboard.
     */
    public function history()
    {
        // Get authenticated student
        $student = Auth::guard('student')->user();

        // Authorize viewing order list
        Gate::forUser($student)->authorize('viewAny', Order::class);

        $orders = Order::where('student_id', $student->matric_no)
            ->with(['orderItems', 'shipping'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.history', compact('orders'));
    }

    /**
     * Track a single order's details and status.
     */
    public function track($id)
    {
        // Get authenticated student
        $student = Auth::guard('student')->user();

        // Type cast and validate
        $id = (int) $id;

        if ($id <= 0) {
            abort(404);
        }

        $order = Order::with(['orderItems', 'shipping'])->findOrFail($id);

        // Authorize viewing this specific order
        Gate::forUser($student)->authorize('view', $order);

        return view('orders.track', compact('order'));
    }
}
```

### CartController.php (With Authorization)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CartController extends Controller
{
    // Add item to cart
    public function add(Request $request)
    {
        // Get authenticated student
        $student = Auth::guard('student')->user();

        // Authorize cart add operation
        Gate::forUser($student)->authorize('add', CartController::class);

        // Set cart ownership on first add
        if (!session('cart_owner')) {
            session(['cart_owner' => $student->matric_no]);
        }

        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:9999.99',
            'image' => 'required|string|max:500',
        ]);

        $cart = Session::get('cart', []);

        // Add item logic...
        $cart[] = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'image' => $validated['image'],
            'quantity' => 1,
        ];

        Session::put('cart', $cart);
        return redirect()->back()->with('success', 'Item added to cart.');
    }

    // Show cart items
    public function show()
    {
        // Get authenticated student
        $student = Auth::guard('student')->user();

        // Authorize cart view operation
        Gate::forUser($student)->authorize('view', CartController::class);

        $cart = Session::get('cart', []);
        return view('student.cart', compact('cart'));
    }

    // Remove item from cart
    public function remove($index)
    {
        // Get authenticated student
        $student = Auth::guard('student')->user();

        // Authorize cart remove operation
        Gate::forUser($student)->authorize('remove', CartController::class);

        $index = (int) $index;
        $cart = Session::get('cart', []);

        // Validate index
        if ($index < 0 || $index >= count($cart)) {
            abort(404, 'Invalid cart item');
        }

        unset($cart[$index]);
        Session::put('cart', array_values($cart));
        return redirect()->route('cart.show')->with('success', 'Item removed from cart.');
    }

    // Update item quantity
    public function update(Request $request, $index)
    {
        // Get authenticated student
        $student = Auth::guard('student')->user();

        // Authorize cart update operation
        Gate::forUser($student)->authorize('update', CartController::class);

        // Validate action parameter
        $validated = $request->validate([
            'action' => 'required|string|in:increase,decrease',
        ]);

        // Update quantity logic...
        $cart = Session::get('cart', []);

        if ($validated['action'] === 'increase') {
            $cart[$index]['quantity'] += 1;
        } elseif ($validated['action'] === 'decrease' && $cart[$index]['quantity'] > 1) {
            $cart[$index]['quantity'] -= 1;
        }

        Session::put('cart', $cart);
        return redirect()->route('cart.show');
    }
}
```

### CheckoutController.php (With Time Restrictions)

```php
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

public function processPayment(Request $request)
{
    // CHECK TIME-BASED RESTRICTIONS
    $user = Auth::guard('student')->user();

    if ($user && !$user->can('can-place-order')) {
        Log::warning('Order placement attempted outside operating hours', [
            'user' => $user->matric_no,
            'time' => now()->format('Y-m-d H:i:s')
        ]);

        return redirect()->route('student.home')->with('error',
            'Sorry, we are currently CLOSED. Orders can only be placed during our operating hours: 8:00 AM - 3:00 AM daily. Please come back during operating hours!');
    }

    // AUTHORIZE CART CHECKOUT
    Gate::forUser($user)->authorize('checkout', \App\Http\Controllers\CartController::class);

    // Continue with payment processing...
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

    // Authorize viewing receipt
    Gate::forUser($student)->authorize('viewReceipt', $order);

    return view('checkout.receipt', compact('order'));
}
```

### Popup Alert Implementation (student/home.blade.php)

```blade
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded m-4 relative" role="alert">
        <strong class="font-bold">⚠️ Cafeteria Closed</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    <script>
        // Show popup alert for error messages
        alert('⚠️ CAFETERIA CLOSED\n\n{{ session('error') }}');
    </script>
@endif
```

### Universal Error Handler (layouts/app.blade.php)

```blade
<!-- Error Messages -->
@if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 m-4" role="alert">
        <p class="font-bold">⚠️ Error</p>
        <p>{{ session('error') }}</p>
    </div>
    <script>
        alert('⚠️ CAFETERIA CLOSED\n\n{{ session('error') }}');
    </script>
@endif

<!-- Success Messages -->
@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-4" role="alert">
        <p class="font-bold">✅ Success</p>
        <p>{{ session('success') }}</p>
    </div>
@endif
```

---

## Testing the Authorization Enhancements

### Manual Testing Checklist

#### 1. Order Authorization Testing

**Test Order List Viewing**:
- [ ] Login as Student A
- [ ] Access order history page
- [ ] Verify only Student A's orders are shown
- [ ] Verify `viewAny` policy allows access

**Test Order Detail Viewing**:
- [ ] Login as Student A
- [ ] Note an order ID belonging to Student A
- [ ] Try to view order details (should succeed)
- [ ] Try to view order belonging to Student B by manually changing URL (should fail with 403)
- [ ] Verify error message: "You do not have permission to view this order."

**Test Receipt Authorization**:
- [ ] Login as Student A
- [ ] View receipt for Student A's order (should succeed)
- [ ] Try to access receipt URL for Student B's order (should fail with 403)
- [ ] Verify error message: "You do not have permission to view this receipt."

#### 2. Cart Authorization Testing

**Test Cart Ownership**:
- [ ] Login as Student A
- [ ] Add items to cart
- [ ] Verify `cart_owner` session is set to Student A's matric_no
- [ ] Try to update cart (should succeed)
- [ ] Try to remove items (should succeed)

**Test Empty Cart Validation**:
- [ ] Login as student
- [ ] Clear cart completely
- [ ] Try to checkout
- [ ] Verify checkout is blocked

**Test Cart View Authorization**:
- [ ] Login as student
- [ ] Access cart page (should succeed)
- [ ] Logout
- [ ] Try to access cart page (should redirect to login)

#### 3. Time-Based Restriction Testing

**Test Operating Hours Restriction**:
- [ ] Set system time to 4:00 AM (closed hours)
- [ ] Login as student
- [ ] Try to access checkout
- [ ] Verify popup alert appears
- [ ] Verify error banner appears
- [ ] Verify error message: "Sorry, we are currently CLOSED..."
- [ ] Set system time to 10:00 AM (open hours)
- [ ] Try checkout again (should succeed)

**Test Each Time Period**:
- [ ] 8:00 AM - 2:59 AM: Should allow orders ✅
- [ ] 3:00 AM - 7:59 AM: Should block orders ❌ (kitchen prep time)
- [ ] Midnight to 2:59 AM: Should allow orders ✅ (late night service)
- [ ] Test boundary: 2:59 AM (allowed) vs 3:00 AM (blocked)
- [ ] Test boundary: 7:59 AM (blocked) vs 8:00 AM (allowed)

**Test Holiday Restriction**:
- [ ] Add today's date to holidays array in AuthServiceProvider
- [ ] Try to place order
- [ ] Verify order is blocked
- [ ] Remove date from holidays
- [ ] Verify order is allowed

**Test Combined Gate**:
- [ ] During operating hours + not holiday = Allow ✅
- [ ] During operating hours + holiday = Block ❌
- [ ] Outside operating hours + not holiday = Block ❌
- [ ] Outside operating hours + holiday = Block ❌

#### 4. Checkout Authorization Flow

**Test Complete Flow**:
- [ ] During allowed hours, login and add items to cart
- [ ] Access checkout form (should pass time check)
- [ ] Fill shipping info
- [ ] Select delivery option
- [ ] Proceed to payment (should pass time check again)
- [ ] Complete payment (should create order)

**Test Mid-Checkout Time Restriction**:
- [ ] Start checkout at 2:55 AM (near closing)
- [ ] Spend time filling forms
- [ ] Try to complete payment at 3:05 AM (after closing)
- [ ] Verify order is blocked with proper error message and popup

---

## Blade Directives for Authorization

You can use these directives in your Blade templates:

### Order Authorization Directives

```blade
{{-- Check if user can view order --}}
@can('view', $order)
    <a href="{{ route('order.track', $order->id) }}">View Order Details</a>
@endcan

{{-- Check if user can cancel order --}}
@can('cancel', $order)
    <form method="POST" action="{{ route('order.cancel', $order->id) }}">
        @csrf
        <button type="submit">Cancel Order</button>
    </form>
@else
    <p class="text-gray-500">This order cannot be cancelled (Status: {{ $order->status }})</p>
@endcan

{{-- Check if user can view receipt --}}
@can('viewReceipt', $order)
    <a href="{{ route('checkout.receipt', $order->id) }}">Download Receipt</a>
@endcan
```

### Cart Authorization Directives

```blade
{{-- Check if user can add to cart --}}
@can('add', App\Http\Controllers\CartController::class)
    <button>Add to Cart</button>
@endcan

{{-- Check if user can checkout --}}
@can('checkout', App\Http\Controllers\CartController::class)
    <a href="{{ route('checkout.form') }}">Proceed to Checkout</a>
@else
    <p>Your cart is empty or unavailable</p>
@endcan
```

### Time-Based Authorization Directives

```blade
{{-- Check if orders can be placed --}}
@if(Auth::guard('student')->user()->can('can-place-order'))
    <button class="btn-primary">Place Order Now</button>
@else
    <div class="alert alert-warning">
        ⚠️ Sorry, we are currently closed.
        Operating hours: 8:00 AM - 3:00 AM daily
    </div>
@endif

{{-- Check business hours only --}}
@if(Gate::check('order-during-business-hours'))
    <span class="text-green-600">🟢 Open Now</span>
@else
    <span class="text-red-600">🔴 Closed (Kitchen Prep: 3 AM - 8 AM)</span>
@endif
```

---

## Configuration Options

### Customizing Operating Hours

Edit `app/Providers/AuthServiceProvider.php`:

```php
// Current: 8 AM - 3 AM (closed 3-7 AM)
$closedHours = [3, 4, 5, 6, 7];

// Example: 24/7 ordering (no closed hours)
$closedHours = [];

// Example: Traditional hours 8 AM - 10 PM (closed 10 PM - 8 AM)
$closedHours = [22, 23, 0, 1, 2, 3, 4, 5, 6, 7];

// Example: 6 AM - midnight (closed midnight - 6 AM)
$closedHours = [0, 1, 2, 3, 4, 5];
```

### Adding Holidays

**Option 1: Hardcode in AuthServiceProvider**
```php
$holidays = [
    '2025-12-25', // Christmas
    '2026-01-01', // New Year
    '2026-05-01', // Labour Day
    '2026-08-31', // Merdeka Day
];
```

**Option 2: Database Table (Recommended for Production)**
```php
Gate::define('order-not-on-holiday', function (Student $student) {
    $today = now()->format('Y-m-d');

    // Fetch from database
    $isHoliday = DB::table('holidays')
        ->where('date', $today)
        ->where('is_active', true)
        ->exists();

    return !$isHoliday;
});
```

### Changing Timezone

Edit `config/app.php`:

```php
// Change from UTC to your local timezone
'timezone' => 'Asia/Kuala_Lumpur',  // Malaysia
// 'timezone' => 'Asia/Singapore',  // Singapore
// 'timezone' => 'Asia/Jakarta',    // Indonesia
// 'timezone' => 'Asia/Bangkok',    // Thailand
```

**Important**: After changing timezone, clear config cache:
```bash
php artisan config:clear
php artisan config:cache
```

---

## Security Principles Applied

1. **Principle of Least Privilege**: Students can only access their own resources
2. **Defense in Depth**: Multiple layers of authorization (policies + gates + validation)
3. **Fail Secure**: Denies access by default, requires explicit authorization
4. **Separation of Duties**: Different authorization methods for different concerns
5. **Audit Logging**: Time restriction attempts are logged for security monitoring
6. **Clear Error Messages**: Users know exactly why access was denied
7. **Type Safety**: All IDs are type-cast and validated before use

---

## Summary of Files Modified

### New Files Created:
1. `app/Policies/OrderPolicy.php`
2. `app/Policies/CartPolicy.php`

### Files Modified:
1. `app/Http/Controllers/OrderController.php`
2. `app/Http/Controllers/CartController.php`
3. `app/Http/Controllers/CheckoutController.php`
4. `app/Providers/AuthServiceProvider.php`
5. `resources/views/student/home.blade.php`
6. `resources/views/layouts/app.blade.php`
7. `config/app.php`

### Configuration Changes:
- Timezone changed from UTC to Asia/Kuala_Lumpur
- Policy mappings registered
- Gates defined for time-based restrictions

---

## Troubleshooting

### Issue: "Call to undefined method authorize()"
**Solution**: Use `Gate::forUser($student)->authorize()` instead of `$this->authorize()` when working with custom guards.

### Issue: Time restrictions not working
**Solution**:
1. Clear config cache: `php artisan config:clear`
2. Verify timezone in `config/app.php`
3. Check current time: `php artisan tinker --execute="echo now()->hour;"`

### Issue: Popup not appearing
**Solution**:
1. Clear browser cache (Ctrl + Shift + Delete)
2. Hard refresh (Ctrl + F5)
3. Clear view cache: `php artisan view:clear`

### Issue: Policy not being recognized
**Solution**:
1. Ensure policy is registered in AuthServiceProvider
2. Clear cache: `php artisan optimize:clear`
3. Verify namespace and class names match exactly

---

## Next Steps

1. ✅ Test all authorization scenarios
2. ✅ Verify time restrictions work correctly
3. ✅ Test with multiple student accounts
4. ⏳ Consider implementing order cancellation feature
5. ⏳ Add database table for holidays
6. ⏳ Implement role-based access for cafeteria staff

---

**Documentation Last Updated**: December 28, 2025
**Laravel Version**: 12.x
**PHP Version**: 8.2+
