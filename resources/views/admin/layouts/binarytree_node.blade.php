<li>
    <div class="relative inline-block">
        <a href="{{ $node['id'] ? route('admin.binary.tree', ['user' => encrypt($node['id'])]) : '#' }}" class="w-10 h-10 !p-2 !flex items-center justify-center rounded-full border-2 {{ $node['position'] === 0 ? 'border-orange-500' : ($node['position'] === 1 ? 'border-blue-500' : 'border-green-500') }} mx-auto">
            @php
            $img = match(true) {
                !$node['id'] => 'logo-available.png',
                $node['u_status'] == 0 => 'logo-register.png',
                $node['u_status'] == 1 => 'logo-gold.png',
                $node['u_status'] == 2 => 'logo-dream.png',
                $node['u_status'] == 3 => 'logo-blocked.png',
                default => 'logo-available.png'
            };
            @endphp
            <img src="{{ asset('member_assets/images/tree-icon/'.$img) }}" class="w-full object-contain " />
        </a>
        <p class="text-xs font-semibold mt-1">
            {{ $node['name'] ?? '' }}
        </p>

        <p class="text-[10px] text-gray-400">
            {{ $node['username'] ?? '' }}
        </p>
        
        <button onclick="openModal({{ $node['id'] }})" 
                class="absolute -top-2 -right-2 text-orange-400 text-lg">
            <i class="ri-information-line"></i>
        </button>
        
    </div>

    @if(!empty($node['children']))
        <ul>
            @foreach($node['children'] as $child)
                @include('admin.layouts.binarytree_node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>