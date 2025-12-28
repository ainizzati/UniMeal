<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($mahallah); ?> Cafeteria - UniMeal</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
    <style>
        .bg-soft-pink { background-color: #FFC0CB; }
        .text-soft-pink { color: #FFC0CB; }
    </style>
</head>
<body class="bg-gray-100">

    <!-- ✅ Header -->
    <header class="bg-soft-pink p-4 shadow flex justify-between items-center text-white">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('student.home')); ?>">
                <img src="<?php echo e(asset('images/unimeal_logo.png')); ?>" alt="UniMeal Logo" class="h-10">
            </a>
            <span class="text-xl font-semibold"><?php echo e($mahallah); ?> Cafeteria</span>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo e(route('student.home')); ?>" class="flex items-center gap-2 bg-white text-pink-600 hover:bg-pink-100 px-4 py-2 rounded shadow">
                <img src="<?php echo e(asset('images/backbutton.png')); ?>" alt="Back" class="h-5 w-5">
                Back to Home
            </a>
            <a href="<?php echo e(route('cart.show')); ?>" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 flex items-center">
                🛒 View Cart
            </a>
        </div>
    </header>

    <!-- ✅ Mahallah Logo -->
    <div class="flex justify-center my-4">
        <img src="<?php echo e(asset('images/' . $logo)); ?>" alt="<?php echo e($mahallah); ?> Logo" class="h-24 object-contain">
    </div>

    <!-- ✅ Search Form -->
    <div class="flex justify-center mb-6">
        <form method="GET" action="<?php echo e(route('cafeteria.show', ['mahallah' => strtolower($mahallah)])); ?>" class="flex gap-2 w-full max-w-xl">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search category (e.g. nasi, mee, drinks)"
                   class="flex-grow px-4 py-2 rounded border border-pink-300 focus:outline-none focus:ring-2 focus:ring-pink-500">
            <button type="submit" class="bg-soft-pink text-white px-4 py-2 rounded hover:bg-pink-400">
                🔍 Search
            </button>
        </form>
    </div>

    <!-- ✅ Menu Section -->
    <main class="px-8 py-4">
        <h2 class="text-2xl font-semibold mb-4 text-center text-pink-700">Menu</h2>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($menus) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white p-4 rounded shadow border border-pink-200">
                        <img src="<?php echo e(asset('images/' . $item['image'])); ?>" alt="<?php echo e($item['name']); ?>" class="h-40 w-full object-cover rounded mb-3">
                        <h3 class="text-lg font-bold text-gray-800"><?php echo e($item['name']); ?></h3>
                        <p class="text-sm text-gray-500 capitalize">Category: <?php echo e($item['category']); ?></p>
                        <p class="text-gray-700 mb-2">RM <?php echo e($item['price']); ?></p>

                        <form action="<?php echo e(route('cart.add')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="name" value="<?php echo e($item['name']); ?>">
                            <input type="hidden" name="price" value="<?php echo e($item['price']); ?>">
                            <input type="hidden" name="image" value="<?php echo e($item['image']); ?>">
                            <button type="submit" class="w-full flex justify-center items-center bg-soft-pink hover:bg-pink-400 text-white py-2 rounded transition duration-300 group">
                                <img src="<?php echo e(asset('images/addbutton.png')); ?>" alt="Add" class="h-5 mr-2 transform group-hover:scale-110 group-hover:rotate-6 transition duration-300">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center text-pink-700 mt-10">
                <p class="text-xl">😔 No menu items found for "<strong><?php echo e(request('search')); ?></strong>"</p>
                <a href="<?php echo e(route('cafeteria.show', ['mahallah' => strtolower($mahallah)])); ?>" class="mt-4 inline-block bg-pink-300 text-white px-4 py-2 rounded hover:bg-pink-400">
                    🔄 Reset Search
                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </main>

    <!-- ✅ Other Mahallahs -->
    <section class="mt-12 px-8">
        <h3 class="text-xl font-bold mb-4 text-pink-700">Visit Other Mahallah Cafeterias</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $otherMahallahs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $other): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(url('/cafeteria/' . strtolower($other))); ?>"
                   class="bg-white p-4 border border-pink-200 rounded hover:bg-pink-50 flex flex-col items-center transition duration-300 transform hover:scale-105">
                    <img src="<?php echo e(asset('images/' . strtolower($other) . '.png')); ?>" alt="<?php echo e(ucfirst($other)); ?> Logo" class="h-20 mb-2 object-contain">
                    <span class="font-semibold text-pink-800"><?php echo e(ucfirst($other)); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <!-- ✅ Posters -->
    <div class="mt-8">
        <img src="<?php echo e(asset('images/poster6.png')); ?>" alt="Poster 6" class="w-full mb-0">
        <img src="<?php echo e(asset('images/poster7.png')); ?>" alt="Poster 7" class="w-full mb-0">
        <img src="<?php echo e(asset('images/poster5.png')); ?>" alt="Poster 5" class="w-full">
    </div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\UniMeal\resources\views/student/food.blade.php ENDPATH**/ ?>