<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Student;
use Illuminate\Auth\Access\Response;

/**
 * Order Authorization Policy
 *
 * Ensures students can only view and manage their own orders.
 * Prevents unauthorized access to other students' order information.
 */
class OrderPolicy
{
    /**
     * Determine whether the student can view any orders.
     *
     * Students can view their own orders list.
     */
    public function viewAny(Student $student): bool
    {
        // All authenticated students can view their own order list
        return true;
    }

    /**
     * Determine whether the student can view the order.
     *
     * Only the order owner can view order details.
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
     * Determine whether the student can create orders.
     *
     * All authenticated students can create orders.
     */
    public function create(Student $student): bool
    {
        // All authenticated students can create orders
        // Additional checks (time-based, etc.) handled by gates
        return true;
    }

    /**
     * Determine whether the student can cancel the order.
     *
     * Students can only cancel their own orders that are in pending or preparing status.
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
     *
     * Only the order owner can view their receipt.
     */
    public function viewReceipt(Student $student, Order $order): Response
    {
        // Same logic as view - must own the order
        if ($order->student_id === $student->matric_no) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this receipt.');
    }

    /**
     * Determine whether the student can update the order.
     *
     * Students cannot update orders after creation.
     * Only cafeterias/admins can update order status.
     */
    public function update(Student $student, Order $order): Response
    {
        // Students cannot update orders
        // This would be handled by cafeteria staff
        return Response::deny('Students cannot modify orders after placement.');
    }

    /**
     * Determine whether the student can delete the order.
     *
     * Students cannot delete orders.
     * They can only cancel pending/preparing orders.
     */
    public function delete(Student $student, Order $order): Response
    {
        // Students cannot delete orders
        // Use cancel() instead for pending orders
        return Response::deny('Orders cannot be deleted. You can cancel pending orders instead.');
    }

    /**
     * Determine whether the student can restore the order.
     */
    public function restore(Student $student, Order $order): bool
    {
        // Not applicable for this system
        return false;
    }

    /**
     * Determine whether the student can permanently delete the order.
     */
    public function forceDelete(Student $student, Order $order): bool
    {
        // Not applicable for this system
        return false;
    }
}
