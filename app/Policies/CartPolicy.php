<?php

namespace App\Policies;

use App\Models\Student;
use Illuminate\Auth\Access\Response;

/**
 * Cart Authorization Policy
 *
 * Since cart is session-based (not database model), this policy
 * authorizes cart operations based on session ownership and user state.
 */
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
        // Verify student account is active (not suspended)
        // You can add a 'status' column to students table later
        // For now, all authenticated students can add to cart

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

    /**
     * Determine whether the student can clear their entire cart.
     */
    public function clear(Student $student): bool
    {
        // Students can always clear their own cart
        return true;
    }
}
