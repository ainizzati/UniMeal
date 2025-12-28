<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Show order history in student dashboard.
     */
    public function history()
    {
        $orders = Order::where('student_id', Auth::guard('student')->id())
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
        //before (missing type validation)
         //after --> add type cast and validate
        $id = (int) $id;
        
        if ($id <= 0) {
            abort(404);
        }
        
        $order = Order::with(['orderItems', 'shipping'])->findOrFail($id);

        if ($order->student_id !== Auth::guard('student')->id()) {
            abort(403); // Protect access
        }

        return view('orders.track', compact('order'));
    }
}
