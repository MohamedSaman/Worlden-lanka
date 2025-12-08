<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Page Title' }}</title>

    <!-- jQuery first -->

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #1f2937;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, #9d1c20 0%, #7a1519 100%);
            color: #ffffff;
            position: fixed;
            border-right: none;
            overflow-y: auto;
            overflow-x: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1040;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .sidebar-header {
            padding: 20px 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-title img {
            max-width: 100%;
            height: auto;
            transition: all 0.3s ease;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            border-radius: 6px;
            margin: 2px 5px;
            display: flex;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: #fff;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateX(5px);
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        .nav-link.active {
            background: #ffffff;
            color: #9d1c20;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link.active i {
            color: #9d1c20;
        }

        .dropdown-toggle::after {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        /* Collapsed Sidebar */
        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed .nav-link span {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar.collapsed .sidebar-header {
            padding: 15px 10px;
        }

        .sidebar.collapsed .sidebar-title img {
            width: 50px;
            height: auto;
        }

        .sidebar.collapsed .dropdown-toggle::after {
            display: none;
        }

        /* Top Bar */
        .top-bar {
            height: 75px;
            background: #ffffff;
            border-bottom: 2px solid #e5e7eb;
            padding: 0 30px;
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 250px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .top-bar.collapsed {
            left: 80px;
            width: calc(100% - 80px);
        }

        .top-bar .btn {
            border: 2px solid #9d1c20;
            transition: all 0.3s ease;
        }

        .top-bar .btn:hover {
            background: #9d1c20;
            transform: scale(1.05);
        }

        .top-bar .btn:hover i {
            color: #fff !important;
        }

        /* Admin info */
        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .admin-info:hover {
            background: #fef2f2;
            border-color: #9d1c20;
        }

        .admin-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9d1c20 0%, #d34d51 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(157, 28, 32, 0.3);
        }

        .admin-name {
            font-weight: 600;
            color: #9d1c20;
            font-size: 0.95rem;
        }

        .company-title {
            flex: 1;
            text-align: center;
        }

        .company-title h2 {
            color: #9d1c20;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 2px;
            margin: 0;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .company-title p {
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
            margin: 0;
            letter-spacing: 0.5px;
        }

        /* Dropdown Menu Styles */
        .dropdown-menu {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 8px 0;
            margin-top: 10px;
            min-width: 200px;
        }

        .dropdown-item {
            padding: 8px 16px;
            display: flex;
            align-items: center;
            color: #233D7F;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #dcf1f8ff;
            color: #b5171a;
        }

        .dropdown-item i {
            font-size: 1rem;
            margin-right: 8px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            margin-top: 75px;
            padding: 25px 0 25px 25px;
            min-height: calc(100vh - 75px);
            width: calc(100% - 280px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8f9fa;
        }

        .main-content.collapsed {
            margin-left: 80px;
            width: calc(100% - 80px);
        }

        /* Card Styles */
        .stat-card,
        .widget-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #dee2e6;
            padding: 1.25rem;
            height: 100%;
        }

        /* Button Styles */
        .btn-primary {
            background-color: #00C8FF;
            border-color: #00C8FF;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #233D7F;
            border-color: #233D7F;
        }

        .btn-danger {
            background-color: #EF4444;
            border-color: #EF4444;
        }

        .btn-danger:hover {
            background-color: #233D7F;
            border-color: #233D7F;
        }

        .btn-secondary {
            background-color: #6B7280;
            border-color: #6B7280;
        }

        /* Table Styles */
        .table-hover tbody tr:hover {
            background-color: #e6f4ea;
        }

        /* Modal Styles */
        .modal-content {
            border: 2px solid #b5171a;
            border-radius: 10px;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
        }

        .modal-header {
            background-color: #b5171a;
            color: white;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
        }

        /* Responsive Styles */
        @media (max-width: 930px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                height: 100%;
                bottom: 0;
                top: 0;
                overflow-y: auto;
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
            }

            .top-bar {
                width: 100%;
                left: 0;
                padding: 0 15px;
            }

            .top-bar .company-title {
                display: none;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 15px;
                margin-top: 75px;
            }
        }

        @media (max-width: 576px) {
            .top-bar {
                height: 65px;
            }

            .company-title h2 {
                font-size: 24px;
            }

            .company-title p {
                font-size: 11px;
            }

            .main-content {
                margin-top: 65px;
            }
        }

        .tracking-tight {
            letter-spacing: -0.025em;
        }

        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-title">
                    <img src="{{ asset('images/plus.png') }}" alt="Logo" width="200px" height="100px">
                </div>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-bar-chart-line"></i> <span>Overview</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#inventorySubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="inventorySubmenu">
                        <i class="bi bi-box-seam"></i> <span>Inventory</span>
                    </a>
                    <div class="collapse" id="inventorySubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.products') ? 'active' : '' }}"
                                    href="{{ route('admin.products') }}">
                                    <i class="bi bi-box-fill"></i> <span>Product Details</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.categories') ? 'active' : '' }}"
                                    href="{{ route('admin.categories') }}">
                                    <i class="bi bi-collection"></i> <span>Product Category</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.product-reentry') ? 'active' : '' }}"
                                    href="{{ route('admin.product-reentry') }}">
                                    <i class="bi bi-collection"></i> <span>Product Return</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 {{ request()->routeIs('admin.manage-customer') ? 'active' : '' }}"
                        href="{{ route('admin.manage-customer') }}">
                        <i class="bi bi-people"></i> <span>Manage Customer</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#salesSubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="salesSubmenu">
                        <i class="bi bi-cart"></i> <span>Sales</span>
                    </a>
                    <div class="collapse" id="salesSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.customer-sale-details') ? 'active' : '' }}"
                                    href="{{ route('admin.customer-sale-details') }}">
                                    <i class="bi bi-people"></i> <span>Customer Sales</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.due-payments') ? 'active' : '' }}"
                                    href="{{ route('admin.due-payments') }}">
                                    <i class="bi bi-cash-coin"></i> <span>Due Payments</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.due-cheques') ? 'active' : '' }}"
                                    href="{{ route('admin.due-cheques') }}">
                                    <i class="bi bi-cash-coin"></i> <span>Cheque Details</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.due-cheques-return') ? 'active' : '' }}"
                                    href="{{ route('admin.due-cheques-return') }}">
                                    <i class="bi bi-cash-coin"></i> <span>Cheque Return</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.view-payments') ? 'active' : '' }}"
                                    href="{{ route('admin.view-payments') }}">
                                    <i class="bi bi-credit-card-2-back"></i> <span>View Payments</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.bill') ? 'active' : '' }}"
                                    href="{{ route('admin.bill') }}">
                                    <i class="bi bi-credit-card-2-back"></i> <span>View Bills</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.back-forward') ? 'active' : '' }}"
                                    href="{{ route('admin.back-forward') }}">
                                    <i class="bi bi-cash-coin"></i> <span>Brought Forward</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 {{ request()->routeIs('admin.create-purchase') ? 'active' : '' }}"
                        href="{{ route('admin.create-purchase') }}">
                        <i class="bi bi-cart-plus"></i> <span>Create Purchase</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 {{ request()->routeIs('admin.product-stocks') ? 'active' : '' }}"
                        href="{{ route('admin.product-stocks') }}">
                        <i class="bi bi-shield-lock"></i> <span>Product Stock</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.store-billing') ? 'active' : '' }}"
                        href="{{ route('admin.store-billing') }}">
                        <i class="bi bi-cash"></i> <span>Store Billing</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.manual-billing') ? 'active' : '' }}"
                        href="{{ route('admin.manual-billing') }}">
                        <i class="bi bi-pencil-square"></i> <span>Manual Billing</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.manual-sales') ? 'active' : '' }}"
                        href="{{ route('admin.manual-sales') }}">
                        <i class="bi bi-journal-text"></i> <span>Manual Sales</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 {{ request()->routeIs('admin.settings') ? 'active' : '' }}"
                        href="{{ route('admin.settings') }}">
                        <i class="bi bi-gear"></i> <span>Settings</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Top Navigation Bar -->
        <nav class="top-bar d-flex align-items-center">
            <!-- Sidebar Toggle -->
            <button id="sidebarToggler" class="btn btn-light rounded-circle me-3"
                style="width: 45px; height: 45px; padding: 0; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-list fs-4" style="color: #9d1c20;"></i>
            </button>

            <!-- Center Title -->
            <div class="company-title">
                <h2>PLUS</h2>
                <p>Importers of Garment Accessories & Machinery</p>
            </div>

            <!-- Right Dropdown -->
            <div class="ms-auto dropdown">
                <div class="admin-info dropdown-toggle" id="adminDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <div class="admin-avatar">A</div>
                    <div class="admin-name">Admin</div>

                </div>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-person me-2"></i>My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="mb-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>


        <!-- Main Content -->
        <main class="main-content">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Then Livewire/Alpine -->
    <script src="livewire.js?id=df3a17f2"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggler = document.getElementById('sidebarToggler');
            const sidebar = document.querySelector('.sidebar');
            const topBar = document.querySelector('.top-bar');
            const mainContent = document.querySelector('.main-content');

            function initializeSidebar() {
                const sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                if (sidebarCollapsed && window.innerWidth >= 768) {
                    sidebar.classList.add('collapsed');
                    topBar.classList.add('collapsed');
                    mainContent.classList.add('collapsed');
                }
                if (window.innerWidth < 930) {
                    sidebar.classList.remove('show');
                    topBar.classList.remove('collapsed');
                    mainContent.classList.remove('collapsed');
                }
            }

            function toggleSidebar(event) {
                if (event) {
                    event.stopPropagation();
                }
                if (window.innerWidth < 930) {
                    sidebar.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                    topBar.classList.toggle('collapsed');
                    mainContent.classList.toggle('collapsed');
                    localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
                }
            }

            if (sidebarToggler && sidebar) {
                initializeSidebar();
                sidebarToggler.addEventListener('click', toggleSidebar);
                document.addEventListener('click', function(event) {
                    if (window.innerWidth < 930 &&
                        sidebar.classList.contains('show') &&
                        !sidebar.contains(event.target) &&
                        !sidebarToggler.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                });
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 930) {
                        sidebar.classList.remove('show');
                        const sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                        if (sidebarCollapsed) {
                            sidebar.classList.add('collapsed');
                            topBar.classList.add('collapsed');
                            mainContent.classList.add('collapsed');
                        } else {
                            sidebar.classList.remove('collapsed');
                            topBar.classList.remove('collapsed');
                            mainContent.classList.remove('collapsed');
                        }
                    } else {
                        topBar.classList.remove('collapsed');
                        mainContent.classList.remove('collapsed');
                    }
                });
            }

            function setActiveMenuItem() {
                const currentPath = window.location.pathname;
                document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                document.querySelectorAll('.collapse').forEach(submenu => {
                    submenu.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                    toggle.setAttribute('aria-expanded', 'false');
                });

                let activeFound = false;
                document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                    const href = link.getAttribute('href');
                    if (href && href !== '#' && !href.startsWith('#')) {
                        const hrefPath = href.replace(/^(https?:\/\/[^\/]+)/, '').split('?')[0];
                        if (currentPath === hrefPath) {
                            link.classList.add('active');
                            activeFound = true;
                            const submenu = link.closest('.collapse');
                            if (submenu) {
                                submenu.classList.add('show');
                                const parentToggle = document.querySelector(`[href="#${submenu.id}"]`);
                                if (parentToggle) {
                                    parentToggle.classList.add('active');
                                    parentToggle.setAttribute('aria-expanded', 'true');
                                }
                            }
                        }
                    }
                });

                if (!activeFound) {
                    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                        const href = link.getAttribute('href');
                        if (href && href !== '#' && !href.startsWith('#')) {
                            const hrefPath = href.replace(/^(https?:\/\/[^\/]+)/, '').split('?')[0];
                            if (hrefPath !== '/' && currentPath.includes(hrefPath)) {
                                link.classList.add('active');
                                const submenu = link.closest('.collapse');
                                if (submenu) {
                                    submenu.classList.add('show');
                                    const parentToggle = document.querySelector(`[href="#${submenu.id}"]`);
                                    if (parentToggle) {
                                        parentToggle.classList.add('active');
                                        parentToggle.setAttribute('aria-expanded', 'true');
                                    }
                                }
                            }
                        }
                    });
                }
            }

            setActiveMenuItem();
            window.addEventListener('resize', adjustSidebarHeight);

            function adjustSidebarHeight() {
                const sidebar = document.querySelector('.sidebar');
                const windowHeight = window.innerHeight;
                if (sidebar) {
                    sidebar.style.height = windowHeight + 'px';
                    const sidebarContent = sidebar.querySelector('.nav.flex-column');
                    if (sidebarContent && sidebarContent.scrollHeight > windowHeight) {
                        sidebar.classList.add('scrollable');
                    } else {
                        sidebar.classList.remove('scrollable');
                    }
                }
            }

            adjustSidebarHeight();
        });

        // SweetAlert listener for Livewire events
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', (...args) => {
                // Livewire v3 passes dispatched payload as first arg; sometimes as array
                const payload = args && args.length ? args[0] : {};
                const data = Array.isArray(payload) ? payload[0] : payload;
                const {
                    icon,
                    title,
                    text
                } = data || {};
                if (typeof Swal !== 'undefined' && icon && title) {
                    Swal.fire({
                        icon: icon,
                        title: title,
                        text: text || '',
                        confirmButtonColor: '#d34d51ff'
                    });
                } else if (title) {
                    alert((icon ? '[' + icon + '] ' : '') + title + (text ? ': ' + text : ''));
                }
            });
        });
    </script>


    @stack('scripts')
</body>

</html>