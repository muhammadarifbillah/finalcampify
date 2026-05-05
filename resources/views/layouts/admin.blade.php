<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Campify</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">

    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside class="hidden md:flex md:w-64 flex-col bg-emerald-700 text-white p-5">

            <!-- LOGO -->
            <div class="flex items-center gap-3 mb-8">
                <img src="{{ asset('logocampify.png') }}" class="w-10 h-10 object-contain">
                <span class="text-xl font-bold">Campify</span>
            </div>

            <!-- MENU -->
            <nav class="space-y-1 text-sm">

                <a href="/admin/dashboard"
                    class="block px-4 py-2 rounded-xl transition 
                {{ Request::is('admin/dashboard') ? 'bg-white text-emerald-700 font-semibold' : 'hover:bg-emerald-600' }}">
                    Dashboard
                </a>

                <a href="/admin/users" class="block px-4 py-2 rounded-xl transition 
                {{ Request::is('admin/users') ? 'bg-white text-emerald-700 font-semibold' : 'hover:bg-emerald-600' }}">
                    Users
                </a>

                <a href="/admin/products"
                    class="block px-4 py-2 rounded-xl transition 
                {{ Request::is('admin/products') ? 'bg-white text-emerald-700 font-semibold' : 'hover:bg-emerald-600' }}">
                    Produk
                </a>

                <a href="/admin/stores"
                    class="block px-4 py-2 rounded-xl transition 
                {{ Request::is('admin/stores') ? 'bg-white text-emerald-700 font-semibold' : 'hover:bg-emerald-600' }}">
                    Toko
                </a>

                <a href="/admin/orders"
                    class="block px-4 py-2 rounded-xl transition 
                {{ Request::is('admin/orders') ? 'bg-white text-emerald-700 font-semibold' : 'hover:bg-emerald-600' }}">
                    Orders
                </a>

                <a href="/admin/articles"
                    class="block px-4 py-2 rounded-xl transition 
                {{ Request::is('admin/articles') ? 'bg-white text-emerald-700 font-semibold' : 'hover:bg-emerald-600' }}">
                    Artikel
                </a>

                <a href="/admin/couriers"
                    class="block px-4 py-2 rounded-xl transition 
                {{ Request::is('admin/couriers') ? 'bg-white text-emerald-700 font-semibold' : 'hover:bg-emerald-600' }}">
                    Kurir
                </a>

                <a href="/admin/chats" class="block px-4 py-2 rounded-xl transition 
                {{ Request::is('admin/chats') ? 'bg-white text-emerald-700 font-semibold' : 'hover:bg-emerald-600' }}">
                    Chat
                </a>

                <a href="/admin/chatbot"
                    class="block px-4 py-2 rounded-xl transition 
                {{ Request::is('admin/chatbot') ? 'bg-white text-emerald-700 font-semibold' : 'hover:bg-emerald-600' }}">
                    Chatbot
                </a>

                <a href="/admin/monitoring"
                    class="block px-4 py-2 rounded-xl transition 
                {{ Request::is('admin/monitoring') ? 'bg-white text-emerald-700 font-semibold' : 'hover:bg-emerald-600' }}">
                    Monitoring
                </a>

            </nav>

        </aside>

        <!-- MAIN -->
        <div class="flex-1 flex flex-col">

            <!-- TOPBAR -->
            <header class="bg-white border-b px-6 py-4 flex justify-between items-center">

                <!-- LOGO MINI -->
                <div class="flex items-center gap-2">

                    <span class="font-semibold text-lg">Campify Admin</span>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- CONTENT -->
            <main class="p-6">
                @yield('content')
            </main>

        </div>

    </div>

</body>

</html>
