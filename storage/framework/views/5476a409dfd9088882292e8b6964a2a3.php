<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - UniMeal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- Left Panel -->
        <div class="w-1/2 flex flex-col justify-center items-center bg-white p-8">
            <h1 class="text-3xl font-bold mb-2">Login to your Account</h1>
            <p class="mb-6 text-gray-500">Hi, Welcome to UniMeal</p>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="text-red-500 mb-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form action="<?php echo e(route('login.post')); ?>" method="POST" class="w-full max-w-sm">
                <?php echo csrf_field(); ?>

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    value="<?php echo e(old('email')); ?>"
                    class="w-full mb-4 p-3 border rounded focus:outline-none focus:ring focus:border-blue-300"
                    required
                >

                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    class="w-full mb-4 p-3 border rounded focus:outline-none focus:ring focus:border-blue-300"
                    required
                >

                <div class="flex justify-between items-center mb-4 text-sm">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        Remember me
                    </label>
                    <a href="<?php echo e(route('password.request')); ?>" class="text-blue-500 hover:underline">
                        Forgot Password?
                    </a>
                </div>

                <button
                    type="submit"
                    class="w-full bg-pink-400 text-white py-2 rounded hover:bg-pink-500 transition"
                >
                    LOG IN
                </button>
            </form>

            <p class="mt-6 text-gray-500">
                Don’t have an account?
                <a href="<?php echo e(route('student.register.form')); ?>" class="text-blue-500 font-semibold hover:underline">
                    Create an account
                </a>
            </p>

            <!-- ✅ Updated Button Section with Matching Styles -->
            <div class="mt-4 flex space-x-4">
                <a href="<?php echo e(route('student.register.form')); ?>"
                   class="bg-pink-200 px-4 py-2 rounded hover:bg-pink-300 transition">
                    Register as Student
                </a>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="w-1/2 flex flex-col justify-center items-center bg-pink-100">
            <h2 class="text-3xl font-bold text-black mb-4">Skip the Line, Not the Meal!!!</h2>
            <img src="<?php echo e(asset('images/unimeal_logo2.png')); ?>" alt="UniMeal Logo" class="w-80">
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\websec\UniMeal\resources\views/auth/login.blade.php ENDPATH**/ ?>