<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Page Title' }}</title>

    <!-- jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Then Livewire/Alpine -->
    <script src="livewire.js?id=df3a17f2"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            background-color: #fff;
            color: #1f2937;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #233D7F 0%, #1e3266 100%);
            color: #ffffff;
            position: fixed;
            border-right: none;
            transition: all 0.3s ease;
            z-index: 1040;
        }

        .sidebar-header {
            padding: 10px;
            font-size: 1.4rem;
            font-weight: 700;
            text-align: left;
            letter-spacing: -0.02em;
            color: #fff;
        }

        .nav-link {
            color: #d1d5db;
            padding: 12px 20px;
            border-radius: 10px;
            margin: 4px 5px;
            display: flex;
            align-items: center;
            transition: all 0.25s;
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 1.2rem;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .nav-link.active {
            background: #ffffff;
            color: #233D7F;
            font-weight: 600;
        }

        .nav-link.active i {
            color: #233D7F;
        }

        /* Top Bar */
        .top-bar {
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 0 25px;
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .top-bar .btn {
            border: none;
        }

        /* Admin info */
        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 10px;
            border-radius: 30px;
            transition: all 0.3s;
        }

        .admin-info:hover {
            background: #f3f4f6;
        }

        .admin-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #233D7F;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .admin-name {
            font-weight: 500;
            color: #111827;
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
            color: #00C8FF;
        }

        .dropdown-item i {
            font-size: 1rem;
            margin-right: 8px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 270px;
            margin-top: 60px;
            padding: 20px 0;
            min-height: calc(100vh - 60px);
            width: calc(100% - 250px);
            transition: all 0.3s ease;
        }

        .main-content.collapsed {
            margin-left: 70px;
            width: calc(100% - 70px);
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
            border: 2px solid #233D7F;
            border-radius: 10px;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
        }

        .modal-header {
            background-color: #233D7F;
            color: white;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
        }

        /* Responsive Styles */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 250px;
                height: 100%;
                bottom: 0;
                top: 0;
                overflow-y: auto;
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            }

            .top-bar {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
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
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-bar-chart-line"></i> <span>Overview</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#inventorySubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="inventorySubmenu">
                        <i class="bi bi-box-seam"></i> <span>Inventory</span>
                    </a>
                    <div class="collapse" id="inventorySubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.products') ? 'active' : '' }}" href="{{ route('admin.products') }}">
                                    <i class="bi bi-box-fill"></i> <span>Product Details</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.categories') ? 'active' : '' }}" href="{{ route('admin.categories') }}">
                                    <i class="bi bi-collection"></i> <span>Product Category</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 {{ request()->routeIs('admin.manage-customer') ? 'active' : '' }}" href="{{ route('admin.manage-customer') }}">
                        <i class="bi bi-people"></i> <span>Manage Customer</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#salesSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="salesSubmenu">
                        <i class="bi bi-cart"></i> <span>Sales</span>
                    </a>
                    <div class="collapse" id="salesSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.customer-sale-details') ? 'active' : '' }}" href="{{ route('admin.customer-sale-details') }}">
                                    <i class="bi bi-people"></i> <span>Customer Sales</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.due-payments') ? 'active' : '' }}" href="{{ route('admin.due-payments') }}">
                                    <i class="bi bi-cash-coin"></i> <span>Due Payments</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.due-cheques') ? 'active' : '' }}" href="{{ route('admin.due-cheques') }}">
                                    <i class="bi bi-cash-coin"></i> <span>Cheque Details</span>
                                </a>
                            </li>
                               <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.due-cheques-return') ? 'active' : '' }}" href="{{ route('admin.due-cheques-return') }}">
                                    <i class="bi bi-cash-coin"></i> <span>Cheque Return</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.view-payments') ? 'active' : '' }}" href="{{ route('admin.view-payments') }}">
                                    <i class="bi bi-credit-card-2-back"></i> <span>View Payments</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 {{ request()->routeIs('admin.product-stocks') ? 'active' : '' }}" href="{{ route('admin.product-stocks') }}">
                        <i class="bi bi-shield-lock"></i> <span>Product Stock</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.store-billing') ? 'active' : '' }}" href="{{ route('admin.store-billing') }}">
                        <i class="bi bi-cash"></i> <span>Store Billing</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Top Navigation Bar -->
        <nav class="top-bar d-flex align-items-center px-3">
            <!-- Sidebar Toggle -->
            <button id="sidebarToggler" class="btn btn-light rounded-pill me-3 transition-all hover:shadow"
                style="border-color: #233D7F;">
                <i class="bi bi-list fs-5" style="color: #233D7F;"></i>
            </button>

            <!-- Center Title -->
            <div class="flex-grow-1 text-center fw-bold my-2" style="color:#233D7F; font-size:18px;">
                <h2 style="padding: 0;margin:0"><b>PLUS</b></h2>
                <p style="padding: 0;margin:0; font-size:16px;">Importers Of Garment Accessories & Machinery</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
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
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('show');
                    topBar.classList.remove('collapsed');
                    mainContent.classList.remove('collapsed');
                }
            }

            function toggleSidebar(event) {
                if (event) {
                    event.stopPropagation();
                }
                if (window.innerWidth < 768) {
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
                    if (window.innerWidth < 768 &&
                        sidebar.classList.contains('show') &&
                        !sidebar.contains(event.target) &&
                        !sidebarToggler.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                });
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) {
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
    </script>


    @stack('scripts')
</body>

</html>