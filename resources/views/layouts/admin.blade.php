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
        ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'href' => route('admin.dashboard'), 'active' => Request::is('admin/dashboard')],
        ['label' => 'Users', 'icon' => 'users', 'href' => route('admin.users.index'), 'active' => Request::is('admin/users*')],
        ['label' => 'Products', 'icon' => 'package', 'href' => route('admin.products.list'), 'active' => Request::is('admin/products-list*') || Request::is('admin/products/*')],
        ['label' => 'Stores', 'icon' => 'store', 'href' => route('admin.stores.index'), 'active' => Request::is('admin/stores*')],
        ['label' => 'Orders', 'icon' => 'shopping-cart', 'href' => route('admin.orders.index'), 'active' => Request::is('admin/orders*')],
        ['label' => 'Articles', 'icon' => 'newspaper', 'href' => '/admin/articles', 'active' => Request::is('admin/articles*')],
        ['label' => 'Courier', 'icon' => 'truck', 'href' => '/admin/couriers', 'active' => Request::is('admin/couriers*')],
        ['label' => 'Chat', 'icon' => 'messages-square', 'href' => '/admin/chats', 'active' => Request::is('admin/chats*')],
        ['label' => 'Chatbot', 'icon' => 'bot', 'href' => '/admin/chatbot', 'active' => Request::is('admin/chatbot*')],
        ['label' => 'Monitoring', 'icon' => 'chart-no-axes-combined', 'href' => '/admin/monitoring', 'active' => Request::is('admin/monitoring*')],
        ['label' => 'Settings', 'icon' => 'settings', 'href' => route('admin.settings'), 'active' => Request::is('admin/settings*')],
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

            <nav class="admin-nav">
                @foreach($adminNav as $item)
                    <a href="{{ $item['href'] }}" class="admin-nav-link {{ $item['active'] ? 'is-active' : '' }}">
                        <i data-lucide="{{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
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
                    <button class="admin-icon-button" type="button" aria-label="Notifikasi">
                        <i data-lucide="bell"></i>
                    </button>
                    <a href="{{ route('admin.settings') }}" class="admin-icon-button" aria-label="Pengaturan">
                        <i data-lucide="settings"></i>
                    </a>
                    <div class="admin-profile">
                        <div class="hidden sm:block text-right">
                            <div class="admin-user-name">{{ $adminName }}</div>
                            <div class="admin-user-role">Campify Admin</div>
                        </div>
                        <div class="admin-avatar">{{ $initials ?: 'A' }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="admin-button admin-button-ghost">Logout</button>
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
