<!-- resources/views/orders/history.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History - UniMeal</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
    <style>
        .status-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 100%;
        }
        .status-icon {
            height: 32px;
            width: 32px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
        }
        .status-active {
            background-color: #ec4899;
            color: white;
        }
        .status-inactive {
            background-color: #e5e7eb;
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-soft-pink p-4 shadow flex justify-between items-center text-white">
        <div class="flex items-center gap-3">
            <img src="<?php echo e(asset('images/unimeal_logo.png')); ?>" alt="UniMeal Logo" class="h-10">
            <span class="text-xl font-semibold">Order History</span>
        </div>
        <a href="<?php echo e(route('student.home')); ?>" class="flex items-center gap-2 bg-black hover:bg-gray-800 text-white px-4 py-2 rounded shadow">
            🏠 Home
        </a>
    </header>

    <main class="p-6 max-w-5xl mx-auto">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded shadow-md p-6 mb-6 border-l-4 border-pink-400">
                <div class="flex justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-pink-700">Order #<?php echo e($order->id); ?></h2>
                        <p class="text-sm text-gray-600">Total: RM<?php echo e(number_format($order->total_amount, 2)); ?></p>
                        <p class="text-sm text-gray-600">Date: <?php echo e($order->created_at->format('d M Y, h:i A')); ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Delivery: <?php echo e($order->delivery_option); ?></p>
                        <p class="text-sm text-gray-700">Payment: <?php echo e(ucfirst($order->payment_method)); ?></p>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <?php
                        $statusSteps = ['Confirmed', 'In Progress', 'Delivered'];
                        $currentIndex = array_search($order->status, $statusSteps);
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statusSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="status-step">
                            <div class="status-icon <?php echo e($index <= $currentIndex ? 'status-active' : 'status-inactive'); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index <= $currentIndex): ?>
                                    <i class="fas fa-check"></i>
                                <?php else: ?>
                                    <i class="fas fa-circle"></i>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <span class="mt-1 text-sm font-medium <?php echo e($index <= $currentIndex ? 'text-pink-700' : 'text-gray-400'); ?>"><?php echo e($step); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index < count($statusSteps) - 1): ?>
                            <div class="flex-grow border-t-2 <?php echo e($index < $currentIndex ? 'border-pink-400' : 'border-gray-300'); ?> mx-2"></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-10">
                <h3 class="text-lg text-gray-600">You have not placed any orders yet.</h3>
                <a href="<?php echo e(route('student.home')); ?>" class="inline-block mt-4 bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">
                    Start Ordering
                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </main>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\uni-meal\resources\views/orders/history.blade.php ENDPATH**/ ?>