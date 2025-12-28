<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - UniMeal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet" />
    <style>
        .bg-soft-pink { background-color: #FFC0CB; }
        .text-soft-pink { color: #FFC0CB; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded m-4">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<!-- ✅ HEADER -->
<header class="bg-soft-pink shadow p-4 flex justify-between items-center text-white">
    <a href="<?php echo e(route('student.home')); ?>">
        <img src="<?php echo e(asset('images/unimeal_logo.png')); ?>" alt="UniMeal Logo" class="h-10">
    </a>
    <div class="flex items-center space-x-4">
        <span>Welcome, <?php echo e(Auth::guard('student')->user()->name); ?></span>
        <a href="<?php echo e(route('orders.history')); ?>" class="bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-4 rounded">
            📦 Track My Orders
        </a>
        <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-4 rounded">
                🚪 Logout
            </button>
        </form>
    </div>
</header>

<!-- ✅ Receipt Section -->
<div class="max-w-5xl mx-auto py-10 px-6 bg-white shadow rounded-lg border border-pink-200">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-pink-700">Order ID: <?php echo e($order->id); ?></h2>
        <p class="text-gray-600">Order Date: <?php echo e($order->created_at->format('F d, Y')); ?></p>
        <p class="text-pink-600 font-semibold">Estimated delivery: <?php echo e($order->delivery_option ?? 'Pick Up'); ?></p>
    </div>

    <!-- ✅ Order Status -->
    <div class="flex items-center justify-between mb-10">
        <?php $statuses = ['Order Confirmed', 'Order Finished', 'Delivery', 'Delivered']; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="text-center flex-1">
                <div class="w-10 h-10 mx-auto rounded-full <?php echo e($index === 0 ? 'bg-pink-500' : 'bg-gray-300'); ?>"></div>
                <p class="text-sm mt-2 font-semibold <?php echo e($index === 0 ? 'text-pink-600' : 'text-gray-400'); ?>">
                    <?php echo e($step); ?>

                </p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- ✅ Items Ordered -->
    <div class="space-y-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between border-b pb-4">
                <div class="flex gap-4 items-center">
                    <img src="<?php echo e(asset('images/' . $item->image)); ?>" alt="<?php echo e($item->name); ?>" class="h-20 w-20 rounded object-cover border border-pink-200">
                    <div>
                        <h3 class="text-lg font-bold text-pink-700"><?php echo e($item->name); ?></h3>
                        <p class="text-gray-500 text-sm">Pizza • Drink • Fries</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-pink-600">RM<?php echo e(number_format($item->price, 2)); ?></p>
                    <p class="text-sm text-gray-500">Qty: <?php echo e($item->quantity); ?></p>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- ✅ Payment & Delivery Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
        <div>
            <h4 class="font-bold mb-2 text-pink-700">Payment</h4>
            <p class="text-sm text-gray-700">
                <?php echo e(strtoupper($order->payment_method)); ?><br>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_method === 'credit_card'): ?>
                    Visa **** 56
                <?php elseif($order->payment_method === 'cash'): ?>
                    Cash on Delivery
                <?php else: ?>
                    <?php echo e(ucfirst($order->payment_method)); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
        <div>
            <h4 class="font-bold mb-2 text-pink-700">Delivery</h4>
            <p class="text-sm text-gray-700">
                <?php echo e($order->shipping->address ?? 'Pick-up at counter'); ?>

            </p>
        </div>
    </div>

    <!-- ✅ Order Summary -->
    <div class="mt-10 border-t pt-6">
        <h4 class="font-bold mb-2 text-pink-700">Order Summary</h4>
        <div class="text-sm text-gray-700">
            <div class="flex justify-between mb-1">
                <span>Subtotal</span>
                <span>RM<?php echo e(number_format($order->orderItems->sum(fn($i) => $i->price * $i->quantity), 2)); ?></span>
            </div>
            <div class="flex justify-between mb-1">
                <span>Discount</span><span>RM0.00</span>
            </div>
            <div class="flex justify-between mb-1">
                <span>Delivery</span>
                <span><?php echo e($order->delivery_option === 'Pick Up' ? 'FREE' : 'RM' . number_format($order->shipping_fee ?? 0.00, 2)); ?></span>
            </div>
            <div class="flex justify-between mb-1">
                <span>Tax</span><span>RM<?php echo e(number_format($order->sales_tax, 2)); ?></span>
            </div>
            <div class="flex justify-between font-bold border-t pt-2 mt-2 text-pink-700">
                <span>Total</span><span>RM<?php echo e(number_format($order->total_amount, 2)); ?></span>
            </div>
        </div>
    </div>

    <!-- ✅ Help Section -->
    <div class="mt-10 text-sm text-gray-600">
        <h4 class="font-bold mb-2 text-pink-700">Need Help?</h4>
        <ul class="list-disc pl-6">
            <li><a href="#" class="text-pink-600 hover:underline">Order Issues</a></li>
            <li><a href="#" class="text-pink-600 hover:underline">Delivery Info</a></li>
            <li><a href="#" class="text-pink-600 hover:underline">Returns</a></li>
        </ul>
    </div>
</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\uni-meal\resources\views/orders/track.blade.php ENDPATH**/ ?>