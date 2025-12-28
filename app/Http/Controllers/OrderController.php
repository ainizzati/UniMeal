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

        //before (missing type validation)
         //after --> add type cast and validate
        $id = (int) $id;

        if ($id <= 0) {
            abort(404);
        }

        $order = Order::with(['orderItems', 'shipping'])->findOrFail($id);

        // Authorize viewing this specific order (replaces manual check)
        Gate::forUser($student)->authorize('view', $order);

        return view('orders.track', compact('order'));
    }
}
