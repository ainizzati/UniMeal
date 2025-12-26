<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Vendor Registration - UniMeal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- Left Form Section -->
        <div class="w-1/2 flex flex-col justify-center items-center bg-white p-8">
            <h1 class="text-3xl font-bold mb-2">Create your Vendor Account</h1>
            <p class="mb-6 text-gray-500">Register to manage your cafeteria!</p>

            @if ($errors->any())
                <div class="mb-4 w-full max-w-sm bg-red-100 text-red-700 p-4 rounded">
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('cafeteria.register.submit') }}" method="POST" class="w-full max-w-sm">
                @csrf

                <input type="text" name="cafeteria_id" placeholder="Cafeteria ID" value="{{ old('cafeteria_id') }}"
                    class="w-full mb-4 p-3 border rounded" required>

                <input type="text" name="name" placeholder="Cafeteria Name" value="{{ old('name') }}"
                    class="w-full mb-4 p-3 border rounded" required>

                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                    class="w-full mb-4 p-3 border rounded" required>

                <div class="w-full mb-4">
                    <input type="password" name="password" placeholder="Password"
                        class="w-full p-3 border rounded" required>
                    <div class="mt-2 text-xs text-gray-600 bg-green-50 p-3 rounded border border-green-200">
                        <p class="font-semibold text-green-800 mb-1">Password Requirements:</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            <li>Minimum 10 characters</li>
                            <li>At least one uppercase letter</li>
                            <li>At least one lowercase letter</li>
                            <li>At least one number</li>
                            <li>At least one special character (!@#$%^&*)</li>
                        </ul>
                    </div>
                </div>

                <input type="password" name="password_confirmation" placeholder="Confirm Password"
                    class="w-full mb-4 p-3 border rounded" required>

                <label class="block mb-4 text-sm">
                    <input type="checkbox" name="terms" required class="mr-2" />
                    Accept <a href="#" class="text-blue-500 underline">terms and conditions</a>
                </label>

                <button type="submit"
                    class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition">
                    REGISTER
                </button>
            </form>

            <p class="mt-6 text-sm">
                Already have an account?
                <a href="{{ route('login') }}" class="text-blue-500 underline">Login now</a>
            </p>

            <div class="mt-4 flex space-x-4">
                <button class="bg-teal-400 px-4 py-2 rounded text-white cursor-default">Cafeteria</button>
                <a href="{{ route('student.register.form') }}"
                    class="bg-pink-200 px-4 py-2 rounded hover:bg-pink-300 transition">Student</a>
            </div>
        </div>

        <!-- Right Image Section -->
        <div class="w-1/2 flex flex-col justify-center items-center bg-green-100">
            <h2 class="text-3xl font-bold text-black mb-4">Serve Meals, Delight Students!</h2>
            <img src="{{ asset('images/unimeal_logo.png') }}" alt="UniMeal Logo" class="w-80" />
        </div>
    </div>
</body>
</html>
