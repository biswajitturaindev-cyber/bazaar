<!DOCTYPE html>
<html lang="en">

@include('admin.partials.head')

<body class="bg-[#f0f1f7] w-full min-h-screen relative">

    @include('admin.partials.nav')

    @include('admin.partials.sidebar')

    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40 lg:hidden"></div>

    <main id="main-content" class="ml-0 lg:ml-[280px] my-16 lg:px-6 sm:px-5 px-4 transition-all duration-300">

        <div class="flex pt-4 pb-6 items-center justify-between">
            <div>
                <h1 class="md:text-2xl text-md font-semibold text-orange-500">
                    @yield('title')
                </h1>

                <ul>
                    <li>
                        <a href="#" class="text-slate-400 hover:text-orange-400">
                            @yield('breadcrumb')
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        @yield('content')
        
        @include('admin.partials.footer')
    </main>

    <script src="{{ asset('admin_assets/js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>