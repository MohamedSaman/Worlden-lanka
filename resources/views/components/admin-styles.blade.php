@push('styles')
<style>
    /* ========================================
       Professional Admin UI - Color System
       Primary: #9d1c20 (Red)
       Secondary: #d34d51 (Light Red)
       ======================================== */

    :root {
        --primary: #9d1c20;
        --primary-dark: #7a1519;
        --primary-light: #d34d51;
        --secondary: #f8f9fa;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --border-color: #e5e7eb;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
    }

    /* Reset & Base */
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        font-size: 15px;
        color: var(--text-primary);
        background-color: #f8fafc;
        line-height: 1.6;
    }

    /* Typography */
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-weight: 600;
        line-height: 1.3;
        color: var(--text-primary);
    }

    .text-primary-custom {
        color: var(--primary) !important;
    }

    /* Modern Card Design */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        overflow: hidden;
    }

    .card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }

    /* Professional Header */
    .card-header-modern {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        padding: 1.5rem 2rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .card-header-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .card-header-modern .icon-wrapper {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .card-header-modern .icon-wrapper i {
        font-size: 28px;
        color: white;
    }

    .card-header-modern h3 {
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-header-modern p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.875rem;
        margin: 0;
    }

    /* Search Bar */
    .search-box-modern {
        position: relative;
        max-width: 500px;
    }

    .search-box-modern .input-group {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: white;
    }

    .search-box-modern .input-group-text {
        background: white;
        border: none;
        padding: 0.75rem 1rem;
        border-right: 1px solid var(--border-color);
    }

    .search-box-modern .form-control {
        border: none;
        padding: 0.75rem 1rem;
        font-size: 0.9375rem;
    }

    .search-box-modern .form-control:focus {
        box-shadow: none;
        outline: none;
    }

    /* Modern Buttons */
    .btn-modern {
        padding: 0.625rem 1.5rem;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.9375rem;
        border: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .btn-modern:active {
        transform: translateY(0);
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        color: white;
    }

    .btn-primary-modern:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        color: white;
    }

    .btn-secondary-modern {
        background: white;
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary-modern:hover {
        background: var(--secondary);
        border-color: var(--border-color);
    }

    /* Stats Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border-left: 4px solid var(--primary);
        height: 100%;
    }

    .stat-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-4px);
    }

    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 1rem;
    }

    .stat-card.primary .stat-icon {
        background: rgba(157, 28, 32, 0.1);
        color: var(--primary);
    }

    .stat-card.success .stat-icon {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .stat-card.warning .stat-icon {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .stat-card.info .stat-icon {
        background: rgba(59, 130, 246, 0.1);
        color: var(--info);
    }

    .stat-card .stat-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }

    /* Modern Table */
    .table-modern {
        background: white;
        border-radius: 12px;
        overflow: hidden;
    }

    .table-modern table {
        margin-bottom: 0;
    }

    .table-modern thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .table-modern thead th {
        border: none;
        padding: 1rem 1.25rem;
        font-weight: 600;
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--primary);
        white-space: nowrap;
    }

    .table-modern tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-top: 1px solid var(--border-color);
        font-size: 0.9375rem;
    }

    .table-modern tbody tr {
        transition: all 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Badges */
    .badge-modern {
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.8125rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge-primary-modern {
        background: rgba(157, 28, 32, 0.1);
        color: var(--primary);
    }

    .badge-success-modern {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .badge-warning-modern {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .badge-danger-modern {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .badge-info-modern {
        background: rgba(59, 130, 246, 0.1);
        color: var(--info);
    }

    /* Modern Modal */
    .modal-modern .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .modal-modern .modal-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        color: white;
        border: none;
        padding: 1.5rem 2rem;
    }

    .modal-modern .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .modal-modern .modal-body {
        padding: 2rem;
    }

    .modal-modern .modal-footer {
        background: var(--secondary);
        border: none;
        padding: 1.25rem 2rem;
    }

    /* Form Controls */
    .form-control-modern,
    .form-select-modern {
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.625rem 1rem;
        font-size: 0.9375rem;
        transition: all 0.2s ease;
    }

    .form-control-modern:focus,
    .form-select-modern:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(157, 28, 32, 0.1);
        outline: none;
    }

    .form-label-modern {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.9375rem;
    }

    /* Pagination */
    .pagination-modern .page-link {
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        padding: 0.5rem 0.875rem;
        margin: 0 0.125rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .pagination-modern .page-link:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .pagination-modern .page-item.active .page-link {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    /* Action Buttons Group */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s ease;
        padding: 0;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .btn-action.btn-view {
        background: rgba(59, 130, 246, 0.1);
        color: var(--info);
    }

    .btn-action.btn-view:hover {
        background: var(--info);
        color: white;
    }

    .btn-action.btn-edit {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .btn-action.btn-edit:hover {
        background: var(--warning);
        color: white;
    }

    .btn-action.btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .btn-action.btn-delete:hover {
        background: var(--danger);
        color: white;
    }

    /* Responsive Utilities */
    @media (max-width: 1199.98px) {
        .card-header-modern {
            padding: 1.25rem 1.5rem;
        }

        .card-header-modern h3 {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 991.98px) {
        .stat-card .stat-value {
            font-size: 1.75rem;
        }

        .table-modern thead th,
        .table-modern tbody td {
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
        }
    }

    @media (max-width: 767.98px) {
        .card-header-modern {
            padding: 1rem 1.25rem;
        }

        .card-header-modern .icon-wrapper {
            width: 48px;
            height: 48px;
        }

        .card-header-modern .icon-wrapper i {
            font-size: 24px;
        }

        .card-header-modern h3 {
            font-size: 1.125rem;
        }

        .card-header-modern p {
            font-size: 0.8125rem;
        }

        .btn-modern {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .search-box-modern {
            max-width: 100%;
        }

        .stat-card {
            padding: 1.25rem;
        }

        .stat-card .stat-value {
            font-size: 1.5rem;
        }

        .table-modern {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-modern table {
            min-width: 800px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }

        .modal-modern .modal-body {
            padding: 1.5rem;
        }
    }

    @media (max-width: 575.98px) {
        body {
            font-size: 14px;
        }

        .stat-card .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }

        .table-modern thead th,
        .table-modern tbody td {
            padding: 0.75rem 0.875rem;
            font-size: 0.8125rem;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-slide-in {
        animation: slideIn 0.4s ease-out;
    }

    /* Loading State */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark);
    }

    /* Utility Classes */
    .shadow-modern {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .shadow-modern-lg {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .rounded-modern {
        border-radius: 12px;
    }

    .rounded-modern-lg {
        border-radius: 16px;
    }

    .text-gradient {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
@endpush