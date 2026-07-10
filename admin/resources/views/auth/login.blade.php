<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>Reshera Group | Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>

<body>


    <section class="bg-slate-800 bg-[url(assets/images/bg.png)] bg-opacity-80 bg-bottom bg-no-repeat bg-contain flex items-center justify-center min-h-dvh">
        <!-- <img src="assets/images/logo/gradient-pp.svg" class="absolute pointer-events-none -top-20 w-full" alt=""> -->

        <!-- Mobile Container -->
        <div>

            <!-- Login Card -->
            <div class="bg-[#1c1c20]/60 backdrop-blur-xl rounded-3xl px-6 sm:px-10 py-8 border border-white/20 mx-3 shadow-[0_8px_32px_rgba(0,0,0,0.4)]">

                <div class="flex justify-center -mt-24 mb-6">
                    <!-- Rotating Wrapper -->
                    <div class="relative h-28 w-28 flex items-center justify-center">

                        <!-- Rotating Gradient Border -->
                        <div
                            class="absolute inset-0 rounded-full bg-gradient-to-tr from-orange-600 to-orange-50 animate-spin-slow">
                        </div>

                        <!-- Inner White Circle -->
                        <div class="relative bg-white rounded-full p-3 h-24 w-24 
                            flex items-center justify-center">
                            <img src="{{asset('assets/images/logo/logo.png')}}" class="h-full object-contain" alt="">
                        </div>

                    </div>

                </div>
                <!-- Icon -->
                <!-- <div class="flex justify-center mb-5">
                    <div
                        class="w-14 h-14 bg-[#FF6600] rounded-full flex items-center justify-center shadow-lg shadow-[#FF6600]/40">
                        <i class="ri-user-3-line text-white text-xl"></i>
                    </div>
                </div> -->

                <!-- Heading -->
                <div class="text-center mb-6">
                    <h2 class="text-white text-xl font-semibold">Admin Login</h2>
                    <p class="text-gray-400 text-sm mt-1">
                        Enter your credentials to continue
                    </p>
                </div>

                <!-- Form -->
                <form class="space-y-5" method="POST" action="{{route('admin.login')}}">
                    @csrf

                    <!-- Email -->
                    <!-- <div>
                        <label class="text-gray-400 text-sm">Username</label>
                        <input type="text" placeholder="Enter username" name="username"
                            class="w-full mt-2 bg-[rgb(34,34,43)]/50 border border-gray-500 focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/30 outline-none rounded-2xl px-5 py-3 text-white placeholder-gray-500 transition">
                    </div> -->

                    <div>
                        <label class="text-gray-400 text-sm">Username</label>
                        <input type="text" placeholder="Enter username" name="username"
                            value="{{ old('username') }}"
                            class="w-full mt-2 bg-[rgb(34,34,43)]/50 border border-gray-500 focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/30 outline-none rounded-2xl px-5 py-3 text-white placeholder-gray-500 transition">

                        @error('username')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <!-- <div>
                        <label class="text-gray-400 text-sm">Password</label>
                        <div class="relative mt-2">
                            <input type="password" id="password" name="password" placeholder="••••••••••"
                                class="w-full bg-[rgb(34,34,43)]/50 border border-gray-500 focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/30 outline-none rounded-2xl px-5 py-3 text-white placeholder-gray-500 transition">
                            <i class="ri-eye-off-line absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 cursor-pointer"
                                id="eyeIcon"></i>
                        </div>
                    </div> -->

                    <div>
                        <label class="text-gray-400 text-sm">Password</label>
                        <div class="relative mt-2">
                            <input type="password" id="password" name="password" placeholder="••••••••••"
                                class="w-full bg-[rgb(34,34,43)]/50 border border-gray-500 focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/30 outline-none rounded-2xl px-5 py-3 text-white placeholder-gray-500 transition">
                            <i class="ri-eye-off-line absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 cursor-pointer"
                                id="eyeIcon"></i>
                        </div>

                        @error('password')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Swipe Style Button -->
                    <button type="submit"
                        class="w-full mt-4 bg-gradient-to-r from-[#FF6600] to-orange-500 hover:opacity-90 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2">
                        Login
                        <i class="ri-arrow-right-line"></i>
                    </button>

                </form>

            </div>

        </div>

    </section>

    <script>
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");

        eyeIcon.addEventListener("click", function() {

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("ri-eye-off-line");
                eyeIcon.classList.add("ri-eye-line");
                eyeIcon.classList.add("text-orange-500");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("ri-eye-line");
                eyeIcon.classList.add("ri-eye-off-line");
                eyeIcon.classList.remove("text-orange-500");
            }

        });
    </script>
</body>

</html>