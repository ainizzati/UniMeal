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
        // after --> Add validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:9999.99',
            'image' => 'required|string|max:500',
        ]);

        $cart = Session::get('cart', []);

        // Convert price to float safely
        $price = floatval(preg_replace('/[^0-9.]/', '', $request->price));

        // Check if item already exists in cart
        $existingIndex = null;
        foreach ($cart as $index => $item) {
            if ($item['name'] === $request->name) {
                $existingIndex = $index;
                break;
            }
        }

        if (!is_null($existingIndex)) {
            // If item exists, increase quantity
            $cart[$existingIndex]['quantity'] += 1;
        } else {
            // Otherwise, add new item
            //before
            // $cart[] = [
            //     'name' => $request->name, //no validation
            //     'price' => $price,
            //     'image' => $request->image, //no validation
            //     'quantity' => 1,
            // ];

            //after
            $cart[] = [
                'name' => $validated['name'],
                'price' => $validated['price'],
                'image' => $validated['image'],
                'quantity' => 1,
            ];
        }

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
        
        // ✅ Add this check
        if ($index < 0 || $index >= count($cart)) {
            abort(404, 'Invalid cart item');
        }

        if (!isset($cart[$index])) {
            abort(404, 'Cart item not found');
        }
        
        unset($cart[$index]);
        Session::put('cart', array_values($cart));
        return redirect()->route('cart.show')->with('success', 'Item removed from cart.');
    }

    // ✅ Update item quantity
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

        // Validate and type-cast index
        $index = (int) $index;

        if ($index < 0) {
            abort(400);
        }

        $cart = Session::get('cart', []);

        if (!isset($cart[$index])) {
            abort(404);
        }

        // Use validated action
        if ($validated['action'] === 'increase') {
            $cart[$index]['quantity'] += 1;
        } elseif ($validated['action'] === 'decrease' && $cart[$index]['quantity'] > 1) {
            $cart[$index]['quantity'] -= 1;
        }

        Session::put('cart', $cart);

        return redirect()->route('cart.show');
    }
}
