<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Reshera Group</title>

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body class="bg-[#f0f1f7] min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

        <!-- Logo -->
        <div class="text-center mb-6">
            <img src="{{ asset('assets/images/logo_h.png') }}" class="h-12 mx-auto mb-2" alt="">
            <h2 class="text-2xl font-bold text-gray-700">Admin Login</h2>
            <p class="text-gray-400 text-sm">Welcome back! Please login</p>
        </div>

        <!-- Error Message -->
        @if(session('error'))
            <div class="bg-red-100 text-red-600 p-2 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="bg-red-100 text-red-600 p-2 rounded mb-4 text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label class="text-sm text-gray-600">Username</label>
                <div class="flex items-center border rounded-lg px-3 py-2 focus-within:border-orange-500">
                    <i class="ri-mail-line text-gray-400 mr-2"></i>
                    <input type="text" name="username" value="{{ old('username') }}"
                        class="w-full outline-none bg-transparent text-sm"
                        placeholder="Enter your email" required>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label class="text-sm text-gray-600">Password</label>
                <div class="flex items-center border rounded-lg px-3 py-2 focus-within:border-orange-500">
                    <i class="ri-lock-line text-gray-400 mr-2"></i>
                    <input type="password" name="password"
                        class="w-full outline-none bg-transparent text-sm"
                        placeholder="Enter your password" required>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex justify-between items-center text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="accent-orange-500">
                    Remember Me
                </label>

                <a href="#" class="text-orange-500 hover:underline">Forgot Password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg transition">
                Login
            </button>
        </form>

        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-400">
            © {{ date('Y') }} Reshera Group
        </div>
    </div>

</body>
</html>