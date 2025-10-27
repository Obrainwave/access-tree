<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AccessTree') }} - Admin Dashboard</title>

    <!-- Favicon -->
    @php
        $favicon = config('accesstree.admin_favicon', null);
        if ($favicon) {
            $faviconPath = asset($favicon);
        } else {
            // Generate a dynamic colored favicon based on app name
            $faviconPath =
                'data:image/svg+xml,' .
                urlencode(
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23667eea"/><text x="50" y="70" font-size="60" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-weight="bold">' .
                        strtoupper(substr(config('app.name', 'A'), 0, 1)) .
                        '</text></svg>',
                );
        }
    @endphp
    <link rel="icon" type="image/svg+xml" href="{{ $faviconPath }}">
    <link rel="shortcut icon" href="{{ $faviconPath }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter" rel="stylesheet">

    @php
        $styling = config('accesstree.styling', []);
        $framework = $styling['framework'] ?? 'bootstrap';
        $theme = $styling['theme'] ?? 'modern';
        $darkMode = $styling['dark_mode'] ?? false;
        $animations = $styling['animations'] ?? true;
    @endphp

    @if ($framework === 'bootstrap')
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @elseif($framework === 'tailwind')
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#667eea',
                            secondary: '#764ba2',
                        }
                    }
                }
            }
        </script>
    @endif

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @if ($theme === 'modern')
        <!-- Modern Dashboard CSS -->
        @if (file_exists(public_path('css/modern-dashboard.css')))
            <link href="{{ asset('css/modern-dashboard.css?v=' . time()) }}" rel="stylesheet">
        @else
            <!-- Fallback: Inline modern styles -->
            <style>
                /* Modern Dashboard Fallback Styles */
                .welcome-card {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-radius: 16px;
                    padding: 2rem;
                    color: white;
                    margin-bottom: 2rem;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                }

                .welcome-title {
                    font-size: 2.5rem;
                    font-weight: 700;
                    margin-bottom: 0.5rem;
                }

                .welcome-subtitle {
                    font-size: 1.1rem;
                    opacity: 0.9;
                }

                .stat-card {
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    border: 1px solid rgba(0, 0, 0, 0.05);
                    transition: all 0.3s ease;
                    overflow: hidden;
                }

                .stat-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
                }

                .stat-card-body {
                    padding: 1.5rem;
                }

                .stat-card-content {
                    display: flex;
                    align-items: center;
                    margin-bottom: 1rem;
                }

                .stat-card-icon {
                    width: 60px;
                    height: 60px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 1rem;
                    color: white;
                    font-size: 1.5rem;
                }

                .stat-card-primary .stat-card-icon {
                    background: linear-gradient(135deg, #667eea, #764ba2);
                }

                .stat-card-success .stat-card-icon {
                    background: linear-gradient(135deg, #56ab2f, #a8e6cf);
                }

                .stat-card-info .stat-card-icon {
                    background: linear-gradient(135deg, #36d1dc, #5b86e5);
                }

                .stat-card-warning .stat-card-icon {
                    background: linear-gradient(135deg, #f093fb, #f5576c);
                }

                .stat-card-number {
                    font-size: 2.5rem;
                    font-weight: 700;
                    color: #2d3748;
                    margin: 0;
                    line-height: 1;
                }

                .stat-card-label {
                    color: #718096;
                    font-size: 0.9rem;
                    margin: 0.5rem 0 0 0;
                    font-weight: 500;
                }

                .stat-card-footer {
                    margin-top: 1rem;
                    padding-top: 1rem;
                    border-top: 1px solid rgba(0, 0, 0, 0.05);
                }

                .stat-card-trend {
                    font-size: 0.875rem;
                    color: #667eea;
                    display: flex;
                    align-items: center;
                    gap: 0.25rem;
                }

                .action-card,
                .status-card,
                .activity-card {
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    border: 1px solid rgba(0, 0, 0, 0.05);
                    overflow: hidden;
                }

                .action-card-header,
                .status-card-header,
                .activity-card-header {
                    padding: 1.5rem 1.5rem 1rem 1.5rem;
                    border-bottom: 1px solid #e2e8f0;
                }

                .action-card-title,
                .status-card-title,
                .activity-card-title {
                    font-size: 1.25rem;
                    font-weight: 600;
                    color: #2d3748;
                    margin: 0;
                    display: flex;
                    align-items: center;
                }

                .action-card-body,
                .status-card-body,
                .activity-card-body {
                    padding: 1.5rem;
                }
            </style>
        @endif
    @endif

    @if ($styling['custom_css'] ?? false)
        <!-- Custom CSS -->
        <style>
            {!! $styling['custom_css'] !!}
        </style>
    @endif

    @if ($darkMode)
        <style>
            :root {
                --bs-body-bg: #1a202c;
                --bs-body-color: #e2e8f0;
            }
        </style>
    @endif

    <style>
        /* Modern Admin Layout Styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            transition: background 0.3s ease;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            transition: background 0.3s ease;
        }

        /* Modern Sidebar */
        .modern-sidebar {
            width: 280px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .modern-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .modern-sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .modern-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .modern-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .sidebar-header {
            padding: 2rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand:hover {
            color: white;
            text-decoration: none;
        }

        .sidebar-brand i {
            font-size: 1.75rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem;
            border-radius: 12px;
        }

        .sidebar-nav {
            padding: 0 1rem;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.75rem;
            padding: 0 1rem;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
            text-decoration: none;
        }

        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        /* Sticky Header */
        .sticky-header {
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            height: 70px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
        }

        .breadcrumb-item {
            color: #718096;
        }

        .breadcrumb-item.active {
            color: #2d3748;
            font-weight: 500;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }

        .user-menu {
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        /* Theme Toggle */
        .theme-toggle {
            margin-right: 1rem;
        }

        .theme-toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .theme-toggle-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background: #1a202c;
            color: #e2e8f0;
        }

        body.dark-mode .admin-layout {
            background: #1a202c;
        }

        body.dark-mode .main-content {
            background: #1a202c;
        }

        body.dark-mode .sticky-header {
            background: rgba(26, 32, 44, 0.95);
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .modern-sidebar {
            background: linear-gradient(180deg, #4a5568 0%, #2d3748 100%);
            transition: background 0.3s ease;
        }

        body.dark-mode .page-title,
        body.dark-mode .breadcrumb-item.active {
            color: #e2e8f0;
        }

        body.dark-mode .card {
            background: #2d3748;
            color: #e2e8f0;
        }

        body.dark-mode .card-header {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background: #4a5568;
            border-color: #718096;
            color: #e2e8f0;
        }

        body.dark-mode .form-control:focus,
        body.dark-mode .form-select:focus {
            background: #4a5568;
            border-color: #667eea;
            color: #e2e8f0;
        }

        body.dark-mode .form-check-label {
            color: #e2e8f0;
        }

        body.dark-mode .table {
            color: #e2e8f0;
        }

        body.dark-mode .btn-secondary {
            background: #4a5568;
            border-color: #718096;
            color: #e2e8f0;
        }

        body.dark-mode .btn-secondary:hover {
            background: #718096;
            color: white;
        }

        body.dark-mode .breadcrumb-item a {
            color: #a0aec0;
        }

        body.dark-mode .breadcrumb-item a:hover {
            color: #e2e8f0;
        }

        body.dark-mode .alert {
            background: #2d3748;
            border-color: rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }

        body.dark-mode .search-section,
        body.dark-mode .page-header,
        body.dark-mode .welcome-card {
            background: #2d3748;
            border-color: rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .stat-card,
        body.dark-mode .action-card,
        body.dark-mode .status-card,
        body.dark-mode .activity-card {
            background: #2d3748;
            border-color: rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .stat-card-label,
        body.dark-mode .stat-card-trend {
            color: #a0aec0;
        }

        body.dark-mode .action-card-title,
        body.dark-mode .status-card-title,
        body.dark-mode .activity-card-title {
            color: #e2e8f0;
        }

        body.dark-mode .activity-title,
        body.dark-mode .activity-time {
            color: #e2e8f0;
        }

        body.dark-mode .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        body.dark-mode .header-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        body.dark-mode .mobile-menu-btn {
            color: #e2e8f0;
        }

        body.dark-mode .modern-table-card {
            background: #2d3748;
            border-color: rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .table-header {
            background: linear-gradient(135deg, #2d3748, #1a202c);
            color: #e2e8f0;
        }

        body.dark-mode .table-row:hover {
            background: #2d3748;
        }

        body.dark-mode .table-cell {
            color: #e2e8f0;
        }

        body.dark-mode .text-cell {
            color: #e2e8f0;
        }

        body.dark-mode th {
            color: #e2e8f0;
        }

        body.dark-mode td {
            color: #e2e8f0;
        }

        body.dark-mode td .text-cell {
            color: #e2e8f0;
        }

        body.dark-mode .stat-card-number {
            color: #e2e8f0;
        }

        body.dark-mode .activity-item {
            background: #2d3748;
            color: #e2e8f0;
        }

        body.dark-mode .activity-item .activity-title,
        body.dark-mode .activity-item .activity-time {
            color: #e2e8f0;
        }

        /* Stat card dark mode improvements */
        body.dark-mode .stat-card {
            background: #2d3748 !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        body.dark-mode .stat-card a {
            color: #e2e8f0 !important;
            text-decoration: none;
        }

        body.dark-mode .stat-card-icon {
            background: #4a5568 !important;
        }

        body.dark-mode .stat-card-primary .stat-card-icon {
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
        }

        body.dark-mode .stat-card-success .stat-card-icon {
            background: linear-gradient(135deg, #56ab2f, #a8e6cf) !important;
        }

        body.dark-mode .stat-card-info .stat-card-icon {
            background: linear-gradient(135deg, #36d1dc, #5b86e5) !important;
        }

        body.dark-mode .stat-card-warning .stat-card-icon {
            background: linear-gradient(135deg, #f093fb, #f5576c) !important;
        }

        /* Additional card text styling for dark mode */
        body.dark-mode .stat-card-label {
            color: #a0aec0 !important;
        }

        body.dark-mode .stat-card-number {
            color: #e2e8f0 !important;
        }

        body.dark-mode .card-body,
        body.dark-mode .card-title,
        body.dark-mode h3,
        body.dark-mode h4,
        body.dark-mode h5,
        body.dark-mode h6 {
            color: #e2e8f0 !important;
        }

        body.dark-mode .badge {
            background: #4a5568 !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode .text-muted {
            color: #a0aec0 !important;
        }

        /* Form card dark mode */
        body.dark-mode .card,
        body.dark-mode .form-card {
            background: #2d3748 !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        body.dark-mode .card-body {
            background: #2d3748 !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode .card-header {
            background: #4a5568 !important;
            border-bottom-color: rgba(255, 255, 255, 0.1) !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode label {
            color: #e2e8f0 !important;
        }

        body.dark-mode .text-danger {
            color: #ef4444 !important;
        }

        body.dark-mode .btn-light {
            background: #4a5568 !important;
            border-color: #718096 !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode .btn-light:hover {
            background: #718096 !important;
            color: white !important;
        }

        body.dark-mode .is-invalid {
            border-color: #ef4444 !important;
        }

        body.dark-mode .invalid-feedback {
            color: #fca5a5 !important;
        }

        body.dark-mode textarea {
            background: #4a5568 !important;
            border-color: #718096 !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode textarea:focus {
            background: #4a5568 !important;
            border-color: #667eea !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode select {
            background: #4a5568 !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode option {
            background: #4a5568 !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode .form-check-input {
            background: #4a5568 !important;
            border-color: #718096 !important;
        }

        body.dark-mode .form-check-input:checked {
            background-color: #667eea !important;
            border-color: #667eea !important;
        }

        /* Card title specific styling */
        body.dark-mode .card-title {
            color: #e2e8f0 !important;
        }

        body.dark-mode h1,
        body.dark-mode h2,
        body.dark-mode h3 {
            color: #e2e8f0 !important;
        }

        /* Input fields with more specificity */
        body.dark-mode input[type="text"],
        body.dark-mode input[type="email"],
        body.dark-mode input[type="number"],
        body.dark-mode input[type="password"],
        body.dark-mode input[type="file"],
        body.dark-mode input::file-selector-button {
            background: #4a5568 !important;
            border-color: #718096 !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode input[type="text"]:focus,
        body.dark-mode input[type="email"]:focus,
        body.dark-mode input[type="number"]:focus {
            background: #4a5568 !important;
            border-color: #667eea !important;
            color: #e2e8f0 !important;
        }

        /* Placeholder text */
        body.dark-mode ::placeholder {
            color: #a0aec0 !important;
            opacity: 1 !important;
        }

        body.dark-mode ::-webkit-input-placeholder {
            color: #a0aec0 !important;
        }

        body.dark-mode ::-moz-placeholder {
            color: #a0aec0 !important;
            opacity: 1 !important;
        }

        /* Card header background override */
        body.dark-mode .card .card-header {
            background: #4a5568 !important;
        }

        /* Form control general styling */
        body.dark-mode .form-control {
            background-color: #4a5568 !important;
            border-color: #718096 !important;
            color: #e2e8f0 !important;
        }

        body.dark-mode .form-control:focus {
            background-color: #4a5568 !important;
            border-color: #667eea !important;
            color: #e2e8f0 !important;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 70px;
            padding: 1.5rem 2rem;
            min-height: calc(100vh - 70px);
            background: transparent;
            width: calc(100% - 280px);
            transition: background 0.3s ease;
        }

        .content-wrapper {
            width: 100%;
            max-width: none;
            margin: 0;
        }

        /* Full width tables and cards */
        .content-wrapper .table {
            width: 100%;
        }

        .content-wrapper .card {
            width: 100%;
        }

        .content-wrapper .row {
            margin-left: 0;
            margin-right: 0;
        }

        .content-wrapper .col {
            padding-left: 0;
            padding-right: 0;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .modern-sidebar {
                transform: translateX(-100%);
                width: 100%;
            }

            .modern-sidebar.open {
                transform: translateX(0);
            }

            .sticky-header {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .mobile-menu-btn {
                display: block;
                background: none;
                border: none;
                font-size: 1.5rem;
                color: #2d3748;
                cursor: pointer;
            }
        }

        @media (min-width: 769px) {
            .mobile-menu-btn {
                display: none;
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Loading animation */
        .loading {
            opacity: 0;
            animation: fadeIn 0.5s ease forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        /* Modern Resource Page Styles */
        .modern-resource-page {
            width: 100%;
            max-width: none;
        }

        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .page-header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title-section {
            flex: 1;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0 0 0.5rem 0;
            display: flex;
            align-items: center;
        }

        .page-subtitle {
            color: #718096;
            margin: 0;
        }

        .page-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .modern-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .modern-btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .modern-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .modern-btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }

        .modern-btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.4);
            color: white;
        }

        /* Search Section */
        .search-section {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .search-container {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .search-form {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 1rem;
        }

        .search-input-group {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            color: #a0aec0;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        /* Modern Table Card */
        .modern-table-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .table-container {
            overflow-x: auto;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-header {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table-row {
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .table-row:hover {
            background: #f8fafc;
        }

        .table-cell {
            padding: 1rem;
            color: #4a5568;
        }

        .actions-cell {
            text-align: center;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .action-btn {
            padding: 0.5rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn-view {
            background: rgba(56, 189, 248, 0.1);
            color: #0ea5e9;
        }

        .action-btn-view:hover {
            background: #0ea5e9;
            color: white;
        }

        .action-btn-edit {
            background: rgba(251, 191, 36, 0.1);
            color: #f59e0b;
        }

        .action-btn-edit:hover {
            background: #f59e0b;
            color: white;
        }

        .action-btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .action-btn-delete:hover {
            background: #ef4444;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
        }

        .empty-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .empty-icon {
            font-size: 3rem;
            color: #cbd5e0;
        }

        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        /* Modern Pagination Styles */
        .pagination {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .pagination li {
            margin: 0;
        }

        .pagination .page-link {
            min-width: 40px;
            height: 40px;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #e2e8f0;
            background: white;
            color: #4a5568;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .pagination .page-link:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            background: #f7fafc;
            color: #cbd5e0;
            border-color: #e2e8f0;
            cursor: not-allowed;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            background: #f7fafc;
            color: #cbd5e0;
            box-shadow: none;
        }

        /* Pagination info text */
        .pagination-info {
            color: #718096;
            font-size: 0.9rem;
        }

        /* Dark mode pagination */
        body.dark-mode .pagination .page-link {
            background: #2d3748;
            border-color: #4a5568;
            color: #e2e8f0;
        }

        body.dark-mode .pagination .page-link:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: transparent;
        }

        body.dark-mode .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        body.dark-mode .pagination .page-item.disabled .page-link {
            background: #1a202c;
            color: #4a5568;
            border-color: #2d3748;
        }

        body.dark-mode .pagination-info {
            color: #a0aec0;
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>

<body class="loading">
    <div class="admin-layout">
        <!-- Modern Sidebar -->
        <div class="modern-sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('accesstree.admin.dashboard') }}" class="sidebar-brand">
                    <i class="fas fa-shield-alt"></i>
                    <span>{{ config('app.name') }}</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <!-- Main Navigation -->
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('accesstree.admin.dashboard') ? 'active' : '' }}"
                            href="{{ route('accesstree.admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('accesstree.admin.permissions.*') ? 'active' : '' }}"
                            href="{{ route('accesstree.admin.permissions.index') }}">
                            <i class="fas fa-key"></i>
                            <span>Permissions</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('accesstree.admin.roles.*') ? 'active' : '' }}"
                            href="{{ route('accesstree.admin.roles.index') }}">
                            <i class="fas fa-users-cog"></i>
                            <span>Roles</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('accesstree.admin.users.*') ? 'active' : '' }}"
                            href="{{ route('accesstree.admin.users.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </div>
                </div>

                <!-- Data Tables -->
                @php
                    $userTables = \Obrainwave\AccessTree\Helpers\TableHelper::getUserTables();
                @endphp

                @if (count($userTables) > 0)
                    <div class="nav-section">
                        <div class="nav-section-title">Data Tables</div>
                        @foreach ($userTables as $tableName => $formattedName)
                            <div class="nav-item">
                                <a class="nav-link {{ request()->routeIs('accesstree.admin.tables.*') && request()->route('table') == $tableName ? 'active' : '' }}"
                                    href="{{ route('accesstree.admin.tables.index', ['table' => $tableName]) }}">
                                    <i
                                        class="{{ \Obrainwave\AccessTree\Helpers\TableHelper::getTableIcon($tableName) }}"></i>
                                    <span>{{ $formattedName }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- System -->
                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('accesstree.admin.system.settings') ? 'active' : '' }}"
                            href="{{ route('accesstree.admin.system.settings') }}">
                            <i class="fas fa-cog"></i>
                            <span>System Settings</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('accesstree.admin.system.logs') || request()->routeIs('accesstree.admin.system.logs.*') ? 'active' : '' }}"
                            href="{{ route('accesstree.admin.system.logs') }}">
                            <i class="fas fa-file-alt"></i>
                            <span>System Logs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <form method="POST" action="{{ route('accesstree.admin.logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="nav-link"
                                style="background: none; border: none; color: #ffffff; cursor: pointer; display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; width: 100%; text-align: left;">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Sticky Header -->
        <div class="sticky-header">
            <div class="header-left">
                <button class="mobile-menu-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('accesstree.admin.dashboard') }}">Admin</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@yield('title', 'Dashboard')</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <a href="{{ route('accesstree.admin.permissions.create') }}" class="header-btn">
                        <i class="fas fa-plus"></i>
                        <span>Add Permission</span>
                    </a>
                    <a href="{{ route('accesstree.admin.roles.create') }}" class="header-btn">
                        <i class="fas fa-plus"></i>
                        <span>Add Role</span>
                    </a>
                </div>
                <div class="theme-toggle">
                    <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Dark Mode">
                        <i class="fas fa-moon" id="theme-icon"></i>
                    </button>
                </div>
                <div class="user-menu">
                    <div class="user-avatar" onclick="toggleUserMenu()">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-wrapper">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- JavaScript for interactions -->
    <script>
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        // Toggle user menu
        function toggleUserMenu() {
            // Add user menu functionality here
            console.log('User menu clicked');
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add loading animation
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.remove('loading');

            // Load saved theme preference
            const savedTheme = localStorage.getItem('adminTheme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                updateThemeIcon(true);
            }
        });

        // Toggle theme
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');

            // Save preference
            localStorage.setItem('adminTheme', isDark ? 'dark' : 'light');

            // Update icon
            updateThemeIcon(isDark);
        }

        // Update theme icon
        function updateThemeIcon(isDark) {
            const icon = document.getElementById('theme-icon');
            if (isDark) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }
    </script>
</body>

</html>
