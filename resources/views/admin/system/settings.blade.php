@extends('accesstree::admin.layouts.app')

@section('title', 'System Settings')

@section('content')
    <!-- System Information Card -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="action-card">
                <div class="action-card-header">
                    <h5 class="action-card-title">
                        <i class="fas fa-cog me-2"></i>
                        System Information
                    </h5>
                </div>
                <div class="action-card-body">
                    <div class="status-grid">
                        <div class="status-item">
                            <div class="status-item-icon">
                                <i class="fab fa-php"></i>
                            </div>
                            <div class="status-item-content">
                                <span class="status-item-label">PHP Version</span>
                                <span class="status-item-value">{{ $systemInfo['php_version'] }}</span>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-item-icon">
                                <i class="fab fa-laravel"></i>
                            </div>
                            <div class="status-item-content">
                                <span class="status-item-label">Laravel Version</span>
                                <span class="status-item-value">{{ $systemInfo['laravel_version'] }}</span>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-item-icon">
                                <i class="fas fa-server"></i>
                            </div>
                            <div class="status-item-content">
                                <span class="status-item-label">Environment</span>
                                <span class="badge bg-success">{{ ucfirst($systemInfo['environment']) }}</span>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-item-icon">
                                <i class="fas fa-bug"></i>
                            </div>
                            <div class="status-item-content">
                                <span class="status-item-label">Debug Mode</span>
                                <span class="badge {{ $systemInfo['debug_mode'] ? 'bg-danger' : 'bg-success' }}">
                                    {{ $systemInfo['debug_mode'] ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-item-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="status-item-content">
                                <span class="status-item-label">Database Driver</span>
                                <span class="status-item-value">{{ $systemInfo['database_driver'] }}</span>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-item-icon">
                                <i class="fas fa-memory"></i>
                            </div>
                            <div class="status-item-content">
                                <span class="status-item-label">Cache Driver</span>
                                <span class="status-item-value">{{ $systemInfo['cache_driver'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="status-card">
                <div class="status-card-header">
                    <h5 class="status-card-title">
                        <i class="fas fa-database me-2"></i>
                        System Statistics
                    </h5>
                </div>
                <div class="status-card-body">
                    <div class="status-grid">
                        <div class="status-item">
                            <div class="status-item-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="status-item-content">
                                <span class="status-item-label">Total Users</span>
                                <span class="status-item-value">{{ $stats['total_users'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Information -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="action-card">
                <div class="action-card-header">
                    <h5 class="action-card-title">
                        <i class="fas fa-hdd me-2"></i>
                        Storage Information
                    </h5>
                </div>
                <div class="action-card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="stat-card stat-card-info">
                                <div class="stat-card-body">
                                    <div class="stat-card-content">
                                        <div class="stat-card-icon">
                                            <i class="fas fa-server"></i>
                                        </div>
                                        <div class="stat-card-info">
                                            <h3 class="stat-card-number">
                                                {{ number_format($storageInfo['total_size'] / 1024 / 1024, 2) }} MB</h3>
                                            <p class="stat-card-label">Total Storage Used</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card stat-card-success">
                                <div class="stat-card-body">
                                    <div class="stat-card-content">
                                        <div class="stat-card-icon">
                                            <i class="fas fa-database"></i>
                                        </div>
                                        <div class="stat-card-info">
                                            <h3 class="stat-card-number">
                                                {{ number_format($storageInfo['database_size'], 2) }} MB</h3>
                                            <p class="stat-card-label">Database Size</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Actions -->
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="action-card">
                <div class="action-card-header">
                    <h5 class="action-card-title">
                        <i class="fas fa-tools me-2"></i>
                        System Actions
                    </h5>
                </div>
                <div class="action-card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <button class="action-btn action-btn-danger w-100" onclick="clearCache()">
                                <div class="action-btn-icon">
                                    <i class="fas fa-trash"></i>
                                </div>
                                <div class="action-btn-content">
                                    <span class="action-btn-title">Clear Cache</span>
                                    <span class="action-btn-subtitle">Remove cached data</span>
                                </div>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="action-btn action-btn-warning w-100" onclick="optimizeApp()">
                                <div class="action-btn-icon">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div class="action-btn-content">
                                    <span class="action-btn-title">Optimize App</span>
                                    <span class="action-btn-subtitle">Optimize application performance</span>
                                </div>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('accesstree.admin.system.settings') }}"
                                class="action-btn action-btn-success w-100" style="text-decoration: none;">
                                <div class="action-btn-icon">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div class="action-btn-content">
                                    <span class="action-btn-title">Refresh Data</span>
                                    <span class="action-btn-subtitle">Reload system information</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .status-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-item-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 1.2rem;
        }

        .status-item-content {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-item-label {
            font-weight: 500;
            color: #4a5568;
        }

        .status-item-value {
            font-weight: 700;
            color: #2d3748;
        }

        .w-100 {
            width: 100% !important;
        }
    </style>

    <script>
        function clearCache() {
            if (!confirm('Are you sure you want to clear all caches?')) return;

            fetch('{{ route('accesstree.admin.system.clear-cache') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message || 'Cache cleared successfully');
                })
                .catch(error => {
                    alert('Error clearing cache');
                });
        }

        function optimizeApp() {
            if (!confirm('Are you sure you want to optimize the application? This may take a moment.')) return;

            fetch('{{ route('accesstree.admin.system.optimize') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message || 'Application optimized successfully');
                })
                .catch(error => {
                    alert('Error optimizing application');
                });
        }
    </script>
@endsection
