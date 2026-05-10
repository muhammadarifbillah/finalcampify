<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campify</title>
    @vite('resources/css/app.css')
    @yield('extra_css')
</head>
<body>

    {{-- Navbar --}}
    @include('layouts.navbar_pembeli')

    {{-- Content --}}
    <main class="{{ request()->routeIs('home') ? '' : 'pt-24' }}">
        @yield('content')
    </main>

    {{-- Radical Notification System (Top Center) --}}
    @if(session('success') || session('error'))
    <div id="radical-toast" style="position: fixed; top: 100px; left: 50%; transform: translateX(-50%); z-index: 999999; width: 90%; max-width: 500px; animation: slideDown 0.5s ease-out forwards;">
        <div style="background: {{ session('success') ? '#10b981' : '#f43f5e' }}; color: white; padding: 20px 30px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: space-between; gap: 15px; border: 2px solid rgba(255,255,255,0.2);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="font-size: 24px;">{{ session('success') ? '✅' : '❌' }}</span>
                <div style="display: flex; flex-direction: column;">
                    <span style="font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">{{ session('success') ? 'Berhasil' : 'Peringatan' }}</span>
                    <span style="font-size: 15px; font-weight: 500; opacity: 0.9;">{{ session('success') ?? session('error') }}</span>
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" style="background: rgba(0,0,0,0.1); border: none; color: white; cursor: pointer; padding: 5px 10px; border-radius: 10px; font-weight: bold;">✕</button>
        </div>
    </div>
    <style>
        @keyframes slideDown {
            from { top: -100px; opacity: 0; }
            to { top: 100px; opacity: 1; }
        }
    </style>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('radical-toast');
            if(toast) {
                toast.style.transition = 'all 0.5s ease';
                toast.style.top = '-100px';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }
        }, 5000);
    </script>
    @endif

    {{-- Footer --}}
    @include('layouts.footer_pembeli')

    @yield('extra_js')
</body>
</html>