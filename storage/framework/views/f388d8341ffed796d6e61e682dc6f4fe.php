<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - UniMeal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet" />
    <style>
        .bg-soft-pink { background-color: #FFC0CB; }
        #heroPoster { transition: opacity 0.5s ease-in-out; }
        .opacity-0 { opacity: 0; }
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
    <img src="<?php echo e(asset('images/unimeal_logo.png')); ?>" alt="UniMeal Logo" class="h-10">
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

<!-- ✅ Hero Poster Banner -->
<section class="relative w-full overflow-hidden mt-4">
    <img id="heroPoster" src="<?php echo e(asset('images/poster1.png')); ?>" alt="UniMeal Banner" class="w-full h-64 sm:h-96 object-cover transition-opacity duration-700 ease-in-out">

    <!-- Arrows -->
    <button onclick="prevSlide()" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white bg-pink-600 hover:bg-pink-700 p-2 rounded-full shadow z-10">&#10094;</button>
    <button onclick="nextSlide()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white bg-pink-600 hover:bg-pink-700 p-2 rounded-full shadow z-10">&#10095;</button>

    <!-- Dots -->
    <div class="absolute bottom-4 w-full flex justify-center gap-3">
        <div class="dot w-3 h-3 rounded-full bg-pink-300 hover:bg-pink-500 cursor-pointer transition" onclick="showSlide(0)" id="dot-0"></div>
        <div class="dot w-3 h-3 rounded-full bg-pink-300 hover:bg-pink-500 cursor-pointer transition" onclick="showSlide(1)" id="dot-1"></div>
        <div class="dot w-3 h-3 rounded-full bg-pink-300 hover:bg-pink-500 cursor-pointer transition" onclick="showSlide(2)" id="dot-2"></div>
    </div>
</section>

<!-- ✅ Popular Categories -->
<section class="mb-10 mt-6">
    <h2 class="text-2xl font-semibold mb-4 text-center text-pink-700">UniMeal Popular Categories 🤩</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 px-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Drinks', 'Pizza', 'Mee', 'Nasi', 'Soup']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex flex-col items-center bg-white p-4 rounded shadow border border-gray-200 transform hover:scale-105 hover:bg-pink-100 transition duration-300">
                <div class="w-20 h-20 mb-2">
                    <img src="<?php echo e(asset('images/categories/' . strtolower($category) . '.jpeg')); ?>"
                         alt="<?php echo e($category); ?>"
                         class="w-full h-full object-cover rounded">
                </div>
                <span class="text-lg font-bold text-pink-700"><?php echo e($category); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>

<!-- ✅ Mahallah Section -->
<main class="p-6">
    <h2 class="text-2xl font-semibold mb-4 text-center">Select Your Mahallah Cafeteria</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Siddiq','Aminah','Ruqayyah','Halimah','Hafsa','Bilal']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mahallah): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(url('/cafeteria/' . strtolower($mahallah))); ?>"
               class="block bg-white p-4 rounded shadow border border-gray-200 text-center transform hover:scale-105 hover:bg-pink-50 transition duration-300">
                <img src="<?php echo e(asset('images/' . strtolower($mahallah) . '.png')); ?>"
                     alt="<?php echo e($mahallah); ?> Logo"
                     class="mx-auto w-24 h-24 object-cover rounded mb-3">
                <h3 class="text-lg font-bold text-pink-700"><?php echo e($mahallah); ?> Cafeteria</h3>
                <p class="text-sm text-gray-600">Order your meals here</p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</main>

<!-- ✅ Bottom Posters -->
<div class="w-full mt-6">
    <img src="<?php echo e(asset('images/poster3.png')); ?>" class="w-full object-cover" alt="Poster 3">
    <img src="<?php echo e(asset('images/poster5.png')); ?>" class="w-full object-cover" alt="Poster 5">
</div>

<!-- ✅ JS for Hero Slideshow -->
<script>
    const heroPosters = [
        "<?php echo e(asset('images/poster1.png')); ?>",
        "<?php echo e(asset('images/poster2.png')); ?>",
        "<?php echo e(asset('images/poster8.png')); ?>"
    ];

    let current = 0;
    let timer;

    function showSlide(index) {
        clearInterval(timer);
        const img = document.getElementById('heroPoster');
        img.classList.add('opacity-0');
        setTimeout(() => {
            img.src = heroPosters[index];
            img.classList.remove('opacity-0');
            current = index;
            highlightDot();
            startSlideshow();
        }, 400);
    }

    function nextSlide() {
        showSlide((current + 1) % heroPosters.length);
    }

    function prevSlide() {
        showSlide((current - 1 + heroPosters.length) % heroPosters.length);
    }

    function highlightDot() {
        heroPosters.forEach((_, i) => {
            const dot = document.getElementById('dot-' + i);
            dot.classList.toggle('bg-pink-600', i === current);
            dot.classList.toggle('bg-pink-300', i !== current);
        });
    }

    function startSlideshow() {
        timer = setInterval(() => nextSlide(), 5000);
    }

    document.addEventListener("DOMContentLoaded", () => {
        highlightDot();
        startSlideshow();
    });
</script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\websec\UniMeal\resources\views/student/home.blade.php ENDPATH**/ ?>