<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sibu Toy - Operations Hub')</title>
    <!-- Google Fonts & Font Awesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            /* Tema Warna Pastel Estetik (Playful, Manis, Modern) */
            --bg-body: #faf7fd;
            --sidebar-bg: #ffffff;
            --surface-card: #ffffff;
            --border-soft: #f1edfa;
            --text-primary: #2b2d42;
            --text-secondary: #6c757d;
            --text-muted: #adb5bd;

            /* Aksen Pastel Utama */
            --pastel-lavender-main: #b197fc;
            --pastel-lavender-bg: #f3f0ff;
            --pastel-lavender-text: #7048e8;

            --pastel-pink-main: #f783ac;
            --pastel-pink-bg: #fff0f6;
            --pastel-pink-text: #e64980;

            --pastel-mint-main: #63e6be;
            --pastel-mint-bg: #e6fcf5;
            --pastel-mint-text: #0ca678;

            --pastel-sky-main: #74c0fc;
            --pastel-sky-bg: #e7f5ff;
            --pastel-sky-text: #1c7ed6;

            --pastel-butter-main: #ffe066;
            --pastel-butter-bg: #fff9db;
            --pastel-butter-text: #f59f00;

            /* Radius & Shadow Sangat Halus dan Rounded */
            --radius-xs: 10px;
            --radius-sm: 14px;
            --radius-md: 20px;
            --radius-lg: 26px;
            --radius-xl: 32px;
            --shadow-subtle: 0 10px 30px -5px rgba(177, 151, 252, 0.1), 0 4px 12px -2px rgba(247, 131, 172, 0.05);
            --shadow-hover: 0 18px 40px -8px rgba(177, 151, 252, 0.22), 0 8px 18px -4px rgba(247, 131, 172, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background-color: var(--bg-body);
            background-image: 
                radial-gradient(at 0% 0%, rgba(243, 240, 255, 0.8) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(255, 240, 246, 0.7) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(230, 252, 245, 0.6) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* SIDEBAR STYLING */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1.5px solid var(--border-soft);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .sidebar-brand {
            padding: 28px 24px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-icon-box {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #e599f7 0%, #b197fc 100%);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            box-shadow: 0 8px 20px rgba(177, 151, 252, 0.35);
        }

        .brand-text h1 {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.4px;
            color: #2b2d42;
            line-height: 1.2;
        }

        .brand-text span {
            font-size: 11px;
            color: #9775fa;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 16px 26px 8px;
        }

        .nav-menu {
            list-style: none;
            padding: 0 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 18px;
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-item a i {
            font-size: 17px;
            width: 22px;
            text-align: center;
            transition: transform 0.25s ease;
        }

        .nav-item a:hover {
            background-color: #f8f0fc;
            color: #7048e8;
            transform: translateX(4px);
        }

        .nav-item a:hover i {
            transform: scale(1.15);
        }

        .nav-item.active a {
            background: linear-gradient(135deg, #f3f0ff 0%, #fff0f6 100%);
            color: #7048e8;
            border: 1px solid #e5dbff;
            box-shadow: 0 6px 16px rgba(177, 151, 252, 0.15);
        }

        .nav-item.active a i {
            color: #b197fc;
        }

        /* Profil User di Sidebar Bawah */
        .sidebar-footer {
            padding: 18px;
            border-top: 1.5px solid var(--border-soft);
            background: rgba(250, 247, 253, 0.7);
        }

        .user-profile-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            background: white;
            border: 1.5px solid var(--border-soft);
            box-shadow: 0 4px 14px rgba(177, 151, 252, 0.08);
        }

        .avatar-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fcc2d7 0%, #f783ac 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(247, 131, 172, 0.35);
        }

        .user-details {
            flex-grow: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            color: #7048e8;
            background: #f3f0ff;
            padding: 2px 9px;
            border-radius: 12px;
            text-transform: capitalize;
            margin-top: 3px;
        }

        .btn-logout-sidebar {
            background: #fff0f6;
            border: 1px solid #ffdeeb;
            color: #e64980;
            cursor: pointer;
            padding: 9px;
            border-radius: 12px;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-logout-sidebar:hover {
            color: white;
            background: #f783ac;
            transform: scale(1.08);
        }

        /* MAIN CONTENT AREA */
        .main-wrapper {
            margin-left: 280px;
            flex-grow: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 280px);
        }

        /* Top Navbar Header */
        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 18px 36px;
            border-bottom: 1.5px solid var(--border-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 6px;
            border-radius: 10px;
        }

        .page-header-info h2 {
            font-size: 23px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.4px;
        }

        .page-header-info p {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 3px;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-action-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #b197fc 0%, #f783ac 100%);
            color: white;
            font-size: 13.5px;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            box-shadow: 0 6px 18px rgba(247, 131, 172, 0.3);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
            border: none;
        }

        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(247, 131, 172, 0.42);
        }

        .btn-action-soft {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f3f0ff;
            color: #7048e8;
            font-size: 13.5px;
            font-weight: 700;
            padding: 10px 18px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
            border: 1px solid #e5dbff;
        }

        .btn-action-soft:hover {
            background: #e5dbff;
            transform: translateY(-2px);
        }

        /* PAGE BODY */
        .content-container {
            padding: 32px 36px 48px;
            display: flex;
            flex-direction: column;
            gap: 28px;
            max-width: 1400px;
            width: 100%;
        }

        /* FLASH ALERT MESSAGE */
        .flash-alert {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            border-radius: var(--radius-md);
            background: var(--pastel-sky-bg);
            border: 1.5px solid #a5d8ff;
            color: var(--pastel-sky-text);
            font-size: 13.5px;
            font-weight: 600;
            box-shadow: 0 6px 18px rgba(116, 192, 252, 0.15);
        }

        .flash-alert.success {
            background: var(--pastel-mint-bg);
            border-color: #96f2d7;
            color: var(--pastel-mint-text);
            box-shadow: 0 6px 18px rgba(99, 230, 190, 0.15);
        }

        .flash-alert.danger {
            background: var(--pastel-pink-bg);
            border-color: #ffc9c9;
            color: #e03131;
            box-shadow: 0 6px 18px rgba(247, 131, 172, 0.15);
        }

        /* CARD DAN FORM STYLING UMUM */
        .section-card {
            background: var(--surface-card);
            border-radius: var(--radius-xl);
            border: 1.5px solid var(--border-soft);
            box-shadow: var(--shadow-subtle);
            padding: 28px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table-modern {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            text-align: left;
        }

        .table-modern th {
            background: #faf7fd;
            color: #7048e8;
            font-weight: 800;
            padding: 14px 18px;
            border-bottom: 2px solid var(--border-soft);
            text-transform: uppercase;
            font-size: 11.5px;
            letter-spacing: 0.6px;
        }

        .table-modern td {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border-soft);
            color: var(--text-primary);
        }

        .table-modern tr:last-child td {
            border-bottom: none;
        }

        .table-modern tr:hover td {
            background: #fff9fc;
        }

        /* FORM INPUT STYLING */
        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-label {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px 18px;
            background: #faf7fd;
            border: 1.5px solid #ece4f9;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            color: var(--text-primary);
            outline: none;
            transition: all 0.25s ease;
        }

        .form-control:focus {
            background: white;
            border-color: #b197fc;
            box-shadow: 0 0 0 4px rgba(177, 151, 252, 0.22);
        }

        /* Search input group wrapper with icon */
        .search-input-group {
            position: relative;
            flex-grow: 1;
            display: flex;
            align-items: center;
        }

        .search-input-group i.search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 14.5px;
            pointer-events: none;
            z-index: 2;
        }

        .search-input-group .modern-search-input,
        .search-input-group .form-control {
            padding-left: 42px !important;
        }

        .btn-card-delete {
            background: #fff0f6;
            color: #e64980;
            border: 1px solid #ffdeeb;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-card-delete:hover {
            background: #f783ac;
            color: white;
            transform: scale(1.05);
        }

        /* CUSTOM PAGINATION STYLING */
        .pagination {
            display: flex;
            list-style: none;
            padding-left: 0;
            border-radius: var(--radius-md);
            gap: 6px;
            margin: 0;
            align-items: center;
        }

        .page-item .page-link {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 12px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            text-decoration: none;
            background-color: white;
            border: 1.5px solid var(--border-soft);
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .page-item .page-link:hover {
            background-color: #f3f0ff;
            color: #7048e8;
            border-color: #d0bfff;
            transform: translateY(-1px);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #b197fc 0%, #e599f7 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(177, 151, 252, 0.35);
        }

        .page-item.disabled .page-link {
            color: var(--text-muted);
            pointer-events: none;
            background-color: #f8fafc;
            border-color: var(--border-soft);
            opacity: 0.6;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
                box-shadow: 0 0 35px rgba(177, 151, 252, 0.3);
            }
            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }
            .mobile-menu-btn {
                display: block;
            }
            .top-navbar {
                padding: 14px 20px;
            }
            .content-container {
                padding: 20px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- SIDEBAR KIRI -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand / Logo Area -->
        <div class="sidebar-brand">
            <div class="brand-text">
                <h1>Sibu Toy</h1>
                <span>Collectibles & Fashion</span>
            </div>
        </div>

        @if(auth()->check() && auth()->user()->role !== 'customer')
            <!-- Menu Navigasi Utama (Hanya untuk Admin & Staff) -->
            <span class="nav-label">Navigasi Utama</span>
            <ul class="nav-menu">
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        
                        <span>Dashboard</span>
                    </a>
                </li>

                @if(auth()->user()->role === 'admin')
                    {{-- Menu Navigasi Lengkap Khusus Admin (Akses Penuh Seluruh Data Master) --}}
                    <li class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <a href="{{ route('categories.index') }}">
                            
                            <span>Kategori</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <a href="{{ route('products.index') }}">
                            
                            <span>Produk</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <a href="{{ route('suppliers.index') }}">
                            
                            <span>Pemasok</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                        <a href="{{ route('purchases.index') }}">
                            
                            <span>Transaksi Masuk</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <a href="{{ route('sales.index') }}">
                            
                            <span>Transaksi Keluar</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <a href="{{ route('settings.index') }}">
                            
                            <span>Pengaturan</span>
                        </a>
                    </li>
                @elseif(auth()->user()->role === 'staff')
                    {{-- Menu Navigasi Terbatas Khusus Staff (Dashboard, Katalog Produk, Transaksi Penjualan) --}}
                    <li class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <a href="{{ route('products.index') }}">
                            
                            <span>Katalog Produk</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <a href="{{ route('sales.index') }}">
                            
                            <span>Transaksi Penjualan</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <a href="{{ route('settings.index') }}">
                            
                            <span>Pengaturan</span>
                        </a>
                    </li>
                @endif
            </ul>
        @endif

        <!-- Profil User & Logout di Bagian Bawah Sidebar -->
        <div class="sidebar-footer">
            <div class="user-profile-card">
                <div class="avatar-circle">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="user-details">
                    <div class="user-name" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</div>
                    <span class="user-role-badge">
                        
                        {{ auth()->user()->role ?? 'Customer' }}
                    </span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar" title="Logout dari Akun">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- AREA KONTEN UTAMA -->
    <div class="main-wrapper">
        <!-- Top Sticky Header -->
        <header class="top-navbar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <button class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Buka Menu">
                    
                </button>
                <div class="page-header-info">
                    <h2>@yield('page_title', 'Dashboard Operasional')</h2>
                    <p>@yield('page_subtitle', 'Ringkasan performa inventaris, status stok, dan log transaksi real-time.')</p>
                </div>
            </div>

            <div class="top-actions">
                @yield('top_actions')
            </div>
        </header>

        <!-- Container Konten Dinamis -->
        <main class="content-container">

            @if(session('info'))
                <div class="flash-alert">
                    
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="flash-alert success">
                    
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="flash-alert danger">
                    
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="flash-alert danger">
                    
                    <div>
                        <ul style="margin-left: 18px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')

        </main>
    </div>

    <!-- Script Interaktif Toggle Sidebar -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('show');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
