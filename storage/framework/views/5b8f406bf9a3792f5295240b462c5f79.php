<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - UniMeal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet" />
    <style>
        .bg-soft-pink { background-color: #FFC0CB; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded m-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- ✅ HEADER with Logout and Track Orders -->
    <header class="bg-soft-pink shadow p-4 flex justify-between items-center text-white">
        <a href="<?php echo e(route('student.home')); ?>">
            <img src="<?php echo e(asset('images/unimeal_logo.png')); ?>" alt="UniMeal Logo" class="h-10">
        </a>
        <div class="flex items-center space-x-4">
            <span>Welcome, <?php echo e(Auth::guard('student')->user()->name); ?></span>

            <!-- ✅ Track Orders Button -->
            <a href="<?php echo e(route('orders.history')); ?>"
               class="bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-4 rounded">
                📦 Track My Orders
            </a>

            <!-- ✅ Logout Form -->
            <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="hover:underline">Logout</button>
            </form>
        </div>
    </header>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto py-10 px-6 bg-white shadow rounded border border-pink-200">
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-pink-600">🎉 Order Confirmed!</h1>
        <p class="text-gray-600">Thank you! Here’s your order summary.</p>
    </div>

    <div class="mb-4">
        <h2 class="text-xl font-semibold text-gray-700">Order Details</h2>
        <ul class="text-gray-600 mt-2 space-y-1">
            <li><strong>Order ID:</strong> #<?php echo e($order->id); ?></li>
            <li><strong>Status:</strong> <?php echo e($order->status); ?></li>
            <li><strong>Delivery Option:</strong> <?php echo e($order->delivery_option); ?></li>
            <li><strong>Payment Method:</strong> <?php echo e(ucfirst($order->payment_method)); ?></li>
        </ul>
    </div>

    <div class="mb-4">
        <h2 class="text-xl font-semibold text-gray-700">Shipping Information</h2>
        <ul class="text-gray-600 mt-2 space-y-1">
            <li><strong>Name:</strong> <?php echo e($order->shipping->name); ?></li>
            <li><strong>Phone:</strong> <?php echo e($order->shipping->phone); ?></li>
            <li><strong>Address:</strong> <?php echo e($order->shipping->address); ?></li>
        </ul>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-700">Items Ordered</h2>
        <div class="mt-2 border-t">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between py-3 border-b">
                    <div>
                        <p class="text-gray-800 font-semibold"><?php echo e($item->name); ?></p>
                        <p class="text-gray-500 text-sm">Quantity: <?php echo e($item->quantity); ?></p>
                    </div>
                    <p class="text-pink-600 font-bold">RM<?php echo e(number_format($item->price * $item->quantity, 2)); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-700">Summary</h2>
        <ul class="text-gray-600 mt-2 space-y-1">
            <li><strong>Subtotal:</strong> RM<?php echo e(number_format($order->orderItems->sum(fn($i) => $i->price * $i->quantity), 2)); ?></li>
            <li><strong>Delivery Fee:</strong> RM<?php echo e(number_format($order->shipping_fee, 2)); ?></li>
            <li><strong>Sales Tax:</strong> RM<?php echo e(number_format($order->sales_tax, 2)); ?></li>
            <li><strong>Total:</strong> RM<?php echo e(number_format($order->total_amount, 2)); ?></li>
        </ul>
    </div>

    <div class="flex justify-center gap-4 mt-8">
        <a href="" class="px-6 py-3 bg-black text-white rounded hover:bg-gray-800">🏠 Back to Home</a>
        <a href="<?php echo e(route('order.track', $order->id)); ?>" class="px-6 py-3 bg-pink-500 text-white rounded hover:bg-pink-600">🔍 Track My Order</a>
    </div>
</div>

<?php /**PATH C:\Users\Syazira Naim\UniMeal\resources\views/checkout/receipt.blade.php ENDPATH**/ ?>