<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Campify Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>

@php
    $adminNav = [
        [
            'label'  => 'Dashboard',
            'icon'   => 'layout-dashboard',
            'active' => Request::is('admin/dashboard') || Request::is('admin/orders*') || Request::is('admin/returns*'),
            'submenu' => [
                [
                    'label'  => 'Dashboard Utama',
                    'icon'   => 'home',
                    'href'   => route('admin.dashboard'),
                    'active' => Request::is('admin/dashboard'),
                ],
                [
                    'label'  => 'Dashboard Pembelian',
                    'icon'   => 'shopping-bag',
                    'href'   => route('admin.orders.index', ['type' => 'buy']),
                    'active' => Request::is('admin/orders*') && Request::get('type') === 'buy',
                ],
                [
                    'label'  => 'Dashboard Penyewaan',
                    'icon'   => 'calendar-days',
                    'href'   => route('admin.orders.index', ['type' => 'rent']),
                    'active' => Request::is('admin/orders*') && Request::get('type') === 'rent',
                ],
                [
                    'label'  => 'Dashboard Pengembalian',
                    'icon'   => 'rotate-ccw',
                    'active' => Request::is('admin/returns*'),
                    'children' => [
                        ['label' => 'Pengembalian Pembelian', 'href' => route('admin.returns.jual_beli'), 'active' => Request::is('admin/returns/jual-beli')],
                        ['label' => 'Pengembalian Sewa',      'href' => route('admin.returns.sewa'),      'active' => Request::is('admin/returns/sewa')],
                    ],
                ],
            ],
        ],
        ['label' => 'Users',      'icon' => 'users',                 'href' => route('admin.users.index'),    'active' => Request::is('admin/users*')],
        ['label' => 'Products',   'icon' => 'package',               'href' => route('admin.products.list'), 'active' => Request::is('admin/products-list*') || Request::is('admin/products/*')],
        ['label' => 'Stores',     'icon' => 'store',                 'href' => route('admin.stores.index'),  'active' => Request::is('admin/stores*')],
        ['label' => 'Articles',   'icon' => 'newspaper',             'href' => '/admin/articles',            'active' => Request::is('admin/articles*')],
        ['label' => 'Courier',    'icon' => 'truck',                 'href' => '/admin/couriers',            'active' => Request::is('admin/couriers*')],
        ['label' => 'Chat',       'icon' => 'messages-square',       'href' => '/admin/chats',               'active' => Request::is('admin/chats*')],
        ['label' => 'Chatbot',    'icon' => 'bot',                   'href' => '/admin/chatbot',             'active' => Request::is('admin/chatbot*')],
        ['label' => 'Monitoring', 'icon' => 'chart-no-axes-combined','href' => '/admin/monitoring',          'active' => Request::is('admin/monitoring*')],
        ['label' => 'Settings',   'icon' => 'settings',              'href' => route('admin.settings'),      'active' => Request::is('admin/settings*')],
    ];
    $adminName = auth()->user()->name ?? 'Admin';
    $initials = collect(explode(' ', $adminName))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->join('');
@endphp

<body class="admin-page">
    <div id="adminLoading" class="admin-loading hidden">
        <div class="admin-loading-bar"></div>
    </div>

    <div class="admin-shell">
        <div id="adminBackdrop" class="admin-backdrop hidden"></div>

        <aside id="adminSidebar" class="admin-sidebar">
            <div class="admin-brand">
                <div>
                    <div class="admin-brand-title">Campify</div>
                    <div class="admin-brand-subtitle">Marketplace Admin</div>
                </div>
            </div>

            <style>
                /* Level 1 dropdown */
                .nav-group.is-open > .nav-group-submenu { display: block; }
                .nav-group.is-open > button > .chevron { transform: rotate(180deg); }
                .nav-group-submenu { display: none; padding-left: 1rem; margin-top: 0.25rem; margin-bottom: 0.25rem; border-left: 2px solid #dde8df; margin-left: 1.25rem; }
                /* Level 2 nested dropdown */
                .nav-nested.is-open > .nav-nested-sub { display: block; }
                .nav-nested.is-open > button > .chevron2 { transform: rotate(180deg); }
                .nav-nested-sub { display: none; padding-left: 0.75rem; margin-top: 0.125rem; border-left: 2px solid #dde8df; margin-left: 0.75rem; }
                .nav-nested-sub a { font-size: 0.78rem; padding: 0.35rem 0.75rem; display: flex; align-items: center; gap: 0.375rem; border-radius: 0.375rem; color: #66746d; transition: background 0.15s, color 0.15s; margin-bottom: 2px; }
                .nav-nested-sub a:hover, .nav-nested-sub a.is-active { background: #e0ebe4; color: #007a52; font-weight: 700; }
                .nav-sub-btn { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 0.5rem 0.75rem; font-size: 0.82rem; border-radius: 0.375rem; color: #24342e; transition: background 0.15s, color 0.15s; cursor: pointer; text-align: left; }
                .nav-sub-btn:hover, .nav-sub-btn.is-active { background: #e0ebe4; color: #007a52; font-weight: 700; }
                .nav-sub-btn .chevron2 { transition: transform 0.2s; width: 12px; height: 12px; flex-shrink: 0; }
            </style>

            <nav class="admin-nav">
                @foreach($adminNav as $item)
                    @if(isset($item['submenu']))
                        <div class="nav-group admin-nav-group {{ $item['active'] ? 'is-open' : '' }}">
                            <button type="button"
                                class="admin-nav-link w-full text-left flex items-center justify-between {{ $item['active'] ? 'is-active !bg-[#064e3b] !text-white' : '' }}"
                                onclick="this.parentElement.classList.toggle('is-open')">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="{{ $item['icon'] }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                </div>
                                <i data-lucide="chevron-down" class="chevron transition-transform duration-200" style="width:16px;height:16px;"></i>
                            </button>
                            <div class="nav-group-submenu">
                                @foreach($item['submenu'] as $sub)
                                    @if(isset($sub['children']))
                                        {{-- Nested level 2 --}}
                                        <div class="nav-nested {{ $sub['active'] ? 'is-open' : '' }}">
                                            <button type="button"
                                                class="nav-sub-btn {{ $sub['active'] ? 'is-active' : '' }}"
                                                onclick="this.parentElement.classList.toggle('is-open')">
                                                <span class="flex items-center gap-2">
                                                    @if(isset($sub['icon']))
                                                        <i data-lucide="{{ $sub['icon'] }}" style="width:14px;height:14px;"></i>
                                                    @endif
                                                    {{ $sub['label'] }}
                                                </span>
                                                <i data-lucide="chevron-down" class="chevron2"></i>
                                            </button>
                                            <div class="nav-nested-sub">
                                                @foreach($sub['children'] as $child)
                                                    <a href="{{ $child['href'] }}" class="{{ $child['active'] ? 'is-active' : '' }}">
                                                        <i data-lucide="dot" style="width:10px;height:10px;"></i>
                                                        {{ $child['label'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        @if(isset($sub['href']))
                                            <a href="{{ $sub['href'] }}"
                                               class="nav-sub-btn {{ $sub['active'] ? 'is-active !bg-transparent !text-[#059669]' : '' }}">
                                                <span class="flex items-center gap-2">
                                                    @if(isset($sub['icon']))
                                                        <i data-lucide="{{ $sub['icon'] }}" style="width:14px;height:14px;"></i>
                                                    @endif
                                                    {{ $sub['label'] }}
                                                </span>
                                            </a>
                                        @else
                                            <div class="nav-sub-btn {{ $sub['active'] ? 'is-active' : '' }}">
                                                <span class="flex items-center gap-2">
                                                    @if(isset($sub['icon']))
                                                        <i data-lucide="{{ $sub['icon'] }}" style="width:14px;height:14px;"></i>
                                                    @endif
                                                    {{ $sub['label'] }}
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item['href'] }}" class="admin-nav-link {{ $item['active'] ? 'is-active' : '' }}">
                            <i data-lucide="{{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="admin-sidebar-user">
                <div class="admin-avatar">{{ $initials ?: 'A' }}</div>
                <div>
                    <div class="admin-user-name">{{ $adminName }}</div>
                    <div class="admin-user-role">Super Admin</div>
                </div>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <button id="adminMenuButton" class="admin-icon-button md:hidden" type="button" aria-label="Buka menu">
                    <i data-lucide="menu"></i>
                </button>

                <form method="GET" action="{{ url()->current() }}" class="admin-search">
                    <i data-lucide="search"></i>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari data admin..." />
                </form>

                <div class="admin-topbar-actions">
                    <button class="admin-icon-button relative" type="button" aria-label="Notifikasi">
                        <i data-lucide="bell"></i>
                        <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                    <button class="admin-icon-button" aria-label="Bantuan">
                        <i data-lucide="help-circle"></i>
                    </button>
                    <div class="h-6 w-px bg-slate-300 mx-2 hidden sm:block"></div>
                    <div class="admin-profile flex items-center gap-3 border-none pl-0">
                        <div class="hidden sm:block text-right">
                            <div class="text-[13px] font-black text-slate-800">Administrator</div>
                            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest">CAMPIFY ADMIN</div>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-[#3b82f6] text-white flex items-center justify-center font-black text-md">{{ $initials ?: 'A' }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="ml-2">
                        @csrf
                        <button type="submit" class="px-5 py-2 rounded-lg border-2 border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">Logout</button>
                    </form>
                </div>
            </header>

            @if(session('success') || session('error') || $errors->any())
                <div class="admin-toast-wrap">
                    @if(session('success'))
                        <div class="admin-toast admin-toast-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="admin-toast admin-toast-danger">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="admin-toast admin-toast-danger">{{ $errors->first() }}</div>
                    @endif
                </div>
            @endif

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
    <script>
        lucide.createIcons();

        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('adminBackdrop');
        const menuButton = document.getElementById('adminMenuButton');
        const loading = document.getElementById('adminLoading');

        function toggleSidebar(show) {
            sidebar.classList.toggle('is-open', show);
            backdrop.classList.toggle('hidden', !show);
        }

        menuButton?.addEventListener('click', () => toggleSidebar(true));
        backdrop?.addEventListener('click', () => toggleSidebar(false));

        document.querySelectorAll('a[href]:not([target]), form').forEach((node) => {
            node.addEventListener(node.tagName === 'FORM' ? 'submit' : 'click', () => {
                loading?.classList.remove('hidden');
            });
        });

        setTimeout(() => {
            document.querySelectorAll('.admin-toast').forEach((toast) => toast.remove());
        }, 4200);
    </script>
</body>

</html>
