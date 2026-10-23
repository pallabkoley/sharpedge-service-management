<nav class="sidebar" id="sidebar">
<div class="logo-container">
    <a href="{{ url('/client/dashboard') }}" class="sharpedge-logo d-flex align-items-center">
        
        <div class="logo-icon" style="display:flex; align-items:center; justify-content:center; margin-right:8px;">
            <img src="{{ asset('storage/SRP_LOGO/WhatsApp Image 2026-04-13 at 18.39.41.jpeg') }}" 
                 alt="Logo" 
                 style="height:46px; width:auto;">
        </div>
        
        <div class="logo-text">Sharp<span>Edge</span></div>
    </a>
</div>
    <ul class="nav flex-column">
        @php
            $navItems = [
                ['url' => '/client/dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
                ['url' => '/client/orders',    'icon' => 'bi-briefcase',    'label' => 'My Services'],
                
                ['url' => '/client/invoices',  'icon' => 'bi-receipt',      'label' => 'Invoices'],
                ['url' => '/client/tickets',   'icon' => 'bi-headset',      'label' => 'Support Tickets'],
            ];
        @endphp

        @foreach($navItems as $item)
            <li class="nav-item">
                <a href="{{ url($item['url']) }}"
                   class="nav-link {{ request()->is(ltrim($item['url'], '/')) ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>