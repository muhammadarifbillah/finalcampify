<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campify - Marketplace Outdoor</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <style>
        .sidebar-link {
            color: #374151;
            border-radius: 10px;
            padding: 10px 12px;
            transition: 0.2s;
        }

        .sidebar-link:hover {
            background: #f3f4f6;
            color: #10B981;
            padding-left: 16px;
        }

        .sidebar-link.active {
            background: #10B981;
            color: white !important;
            font-weight: 600;
        }

        .sidebar-sub {
            color: #6b7280;
            border-radius: 8px;
            padding: 8px 10px;
            font-size: 14px;
            transition: 0.2s;
        }

        .sidebar-sub:hover {
            background: #f3f4f6;
            color: #10B981;
            padding-left: 14px;
        }

        .sidebar-sub.active {
            color: #10B981;
            font-weight: 600;
        }
        .navbar-custom {
            background-color: #157347; /* hijau */
        }
        .navbar-custom .nav-link,
        .navbar-custom .navbar-brand {
            color: #fff !important;
        }
        .footer {
            background: #157347;
            color: white;
            padding: 20px 0;
            margin-top: 40px;
        }
        .footer a {
            color: #c7ffd8;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        body {
            background: #f7f9f7;
        }
    </style>
</head>

<body>

    {{-- NAVBAR --}}
    @include('SellerView.layouts.navbar_seller')

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

         @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>

    {{-- FOOTER --}}
    <!-- @include('SellerView.layouts.footer_seller') -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


