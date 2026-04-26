<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Campify</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-green-800 text-white p-4 hidden md:block">
            <h1 class="text-xl font-bold mb-6">Campify Admin</h1>

            <nav class="space-y-2">
                <a href="/admin/dashboard" class="block hover:bg-green-700 p-2 rounded">Dashboard</a>
                <a href="/admin/users" class="block hover:bg-green-700 p-2 rounded">Users</a>
                <a href="/admin/products" class="block hover:bg-green-700 p-2 rounded">Produk</a>
                <a href="/admin/stores" class="block hover:bg-green-700 p-2 rounded">Toko</a>
                <a href="/admin/articles" class="block hover:bg-green-700 p-2 rounded">Artikel</a>
                <a href="/admin/couriers" class="block hover:bg-green-700 p-2 rounded">Kurir</a>
                <a href="/admin/chats" class="block hover:bg-green-700 p-2 rounded">Chat</a>
                <a href="/admin/chatbot" class="block hover:bg-green-700 p-2 rounded">Chatbot</a>
                <a href="/admin/monitoring" class="block hover:bg-green-700 p-2 rounded">Monitoring</a>
            </nav>
        </aside>

        <!-- CONTENT -->
        <div class="flex-1">

            <!-- TOPBAR -->
            <div class="bg-white p-4 shadow flex justify-between">
                <h2 class="font-bold text-green-800">Dashboard</h2>
                <span>Admin</span>
            </div>

            <main class="p-4">
                @yield('content')
            </main>

        </div>

    </div>

</body>

</html>