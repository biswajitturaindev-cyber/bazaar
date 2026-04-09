<nav class="bg-white w-full h-16 px-3 sm:px-4 md:px-5 lg:px-6 flex justify-between items-center fixed z-50 shadow-sm">
    <div class="flex items-center lg:gap-20 gap-3">
        <!-- logo and toggle btn -->
        <div>
            <img src="{{ asset('admin_assets/images/logo.png') }}" class="h-12 lg:hidden block" alt="">
            <img src="{{ asset('admin_assets/images/logo_h.png') }}" class="h-12 hidden lg:block" alt="">
        </div>

        <div class="flex items-center gap-5">
            <i id="sidebar-control" class="ri-menu-line sm:text-3xl text-2xl cursor-pointer hover:bg-orange-600 hover:text-white hover:border-orange-600 border rounded-lg lg:px-2 lg:py-1 px-2"></i>
            <div class="hidden md:flex items-center gap-2 border focus:border-orange-500 px-3 py-2 rounded-lg">
                <i class="ri-search-line text-xl cursor-pointer hover:text-orange-600"></i>
                <input type="search" class="bg-transparent focus:outline-none" placeholder="Start typing to search..." />
            </div>
        </div>
    </div>

    <!-- notification and account -->
    <div class="flex items-center lg:gap-6 gap-2">
        <div>
            <select name="" id="" class="capitalize border rounded-md p-1 bg-slate-100 focus:outline-none">
                <option value="english">English</option>
                <option value="bangla">Bangla</option>
                <option value="arabic">Arabic</option>
            </select>
        </div>
        <div class="flex items-center lg:gap-6 gap-3">
            <i class="iconoir-bell text-xl cursor-pointer hover:text-orange-600"></i>
            <div class="flex items-center gap-2 cursor-pointer hover:text-orange-600">
                <img src="{{ asset('admin_assets/images/15.jpg') }}" class="h-8 w-8 object-cover rounded-lg" alt="">
                <span class="capitalize w-[60px] inline-block truncate" title="Administration">
                    Administration
                </span>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                @csrf
                <button type="submit">
                    <i class="ri-shut-down-line text-xl cursor-pointer hover:text-orange-600"></i>
                </button>
            </form>
        </div>
    </div>
</nav>