<nav class="bg-white w-full h-16 px-4 flex justify-between items-center fixed z-50 shadow-sm">
    <div class="flex items-center gap-4">
        <img src="{{ asset('assets/images/logo_h.png') }}" class="h-10" alt="">
    </div>

    <div class="flex items-center gap-4">
        <span>Hi, {{ auth('admin')->user()->name ?? 'Admin' }}</span>
        
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="text-red-500">Logout</button>
        </form>
    </div>
</nav>