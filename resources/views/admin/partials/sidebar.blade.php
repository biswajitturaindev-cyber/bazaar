<aside id="sidebar"
    class="bg-slate-700 fixed top-16 bottom-0 left-0 w-[280px] lg:w-[280px] p-4 lg:p-5 space-y-3 overflow-y-auto z-50 transition-all duration-300 ease-in-out -translate-x-full lg:translate-x-0">
    <div>
        <a href="{{ route('admin.dashboard') }}"
            class="flex gap-3 items-center w-full text-slate-200 hover:text-orange-400 group">
            <div class="w-10 h-10 shrink-0 bg-slate-600 rounded-md flex justify-center items-center">
                <i class="iconoir-dashboard-dots"></i>
            </div>
            <span class="capitalize menu-text">Dashboard</span>
        </a>
    </div>


    <div>
        <a href="#"
            class="dropdown-btn flex justify-between items-center text-slate-200 hover:text-orange-400 group">
            <div class="flex gap-3 items-center">
                <div class="w-10 h-10 shrink-0 bg-slate-600 rounded-md flex justify-center items-center">
                    <i class="ri-layout-grid-line transition-transform menu-text"></i>
                </div>
                <span class="capitalize menu-text">Business Category Master</span>
            </div>
            <i class="ri-arrow-right-s-line transition-transform dropdown-arrow menu-text"></i>
        </a>
        <ul
            class="list-none w-5/6 mx-auto space-y-2 overflow-hidden max-h-0 opacity-0 transition-all duration-500 ease-in-out dropdown-content block">
            <li><a href="{{ route('business-categories.index') }}" class="text-slate-400 text-sm hover:text-white">-
                    Business Category List</a></li>
            <li><a href="{{ route('business-sub-categories.index') }}" class="text-slate-400 text-sm hover:text-white">-
                    Business Sub Category List</a></li>
        </ul>
    </div>


    <div>
        <a href="#"
            class="dropdown-btn flex justify-between items-center text-slate-200 hover:text-orange-400 group">
            <div class="flex gap-3 items-center">
                <div class="w-10 h-10 shrink-0 bg-slate-600 rounded-md flex justify-center items-center">
                    <i class="ri-apps-line transition-transform menu-text"></i>
                </div>
                <span class="capitalize menu-text">Product Section</span>
            </div>
            <i class="ri-arrow-right-s-line transition-transform dropdown-arrow menu-text"></i>
        </a>
        <ul
            class="list-none w-5/6 mx-auto space-y-2 overflow-hidden max-h-0 opacity-0 transition-all duration-500 ease-in-out dropdown-content block">
            <li><a href="{{ route('business-category-mapping.index') }}"
                    class="text-slate-400 text-sm hover:text-white">- Business Category Mapping</a></li>
            <li><a href="{{ route('admin.product.category.list') }}" class="text-slate-400 text-sm hover:text-white">-
                    Product Category List</a></li>
            <li><a href="{{ route('admin.product.sub.category.list') }}"
                    class="text-slate-400 text-sm hover:text-white">- Sub Category List</a></li>
            <li><a href="{{ route('admin.product.sub.category.item.list') }}"
                    class="text-slate-400 text-sm hover:text-white">- Sub Sub Category List</a></li>
            <li><a href="{{ route('admin.product.list') }}" class="text-slate-400 text-sm hover:text-white">- Product
                    List</a></li>
        </ul>
    </div>

    <div>
        <a href="#"
            class="dropdown-btn flex justify-between items-center text-slate-200 hover:text-orange-400 group">
            <div class="flex gap-3 items-center">
                <div class="w-10 h-10 shrink-0 bg-slate-600 rounded-md flex justify-center items-center">
                    <i class="ri-price-tag-3-line transition-transform menu-text"></i>
                </div>
                <span class="capitalize menu-text">Attribute and Attribute Variation Section</span>
            </div>
            <i class="ri-arrow-right-s-line transition-transform dropdown-arrow menu-text"></i>
        </a>
        <ul
            class="list-none w-5/6 mx-auto space-y-2 overflow-hidden max-h-0 opacity-0 transition-all duration-500 ease-in-out dropdown-content block">
            <li><a href="{{ route('business-category-mapping.index') }}"
                    class="text-slate-400 text-sm hover:text-white">- Attribute Variation</a></li>
            <li><a href="{{ route('admin.product.category.list') }}" class="text-slate-400 text-sm hover:text-white">-
                    Attribute Variation List</a></li>
        </ul>
    </div>


    <div>
        <a href="#"
            class="dropdown-btn flex justify-between items-center text-slate-200 hover:text-orange-400 group">
            <div class="flex gap-3 items-center">
                <div class="w-10 h-10 shrink-0 bg-slate-600 rounded-md flex justify-center items-center">
                    <i class="ri-settings-3-line transition-transform dropdown-arrow menu-text"></i>
                </div>
                <span class="capitalize menu-text">Settings</span>
            </div>
            <i class="ri-arrow-right-s-line transition-transform dropdown-arrow menu-text"></i>
        </a>
        <ul
            class="list-none w-5/6 mx-auto space-y-2 overflow-hidden max-h-0 opacity-0 transition-all duration-500 ease-in-out dropdown-content block">

            <!--<li><a href="{{ route('change.password') }}" class="text-slate-400 text-sm hover:text-white">- Change Password</a></li>-->
        </ul>
    </div>
</aside>
