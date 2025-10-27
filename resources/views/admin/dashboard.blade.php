@extends('accesstree::admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h1 class="welcome-title">
                        <i class="fas fa-tachometer-alt me-3"></i>
                        Welcome to {{ config('app.name') ? config('app.name') : 'AccessTree' }} Admin
                    </h1>
                    <p class="welcome-subtitle">Manage your application's permissions, roles, and users with ease</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-card-body">
                    <div class="stat-card-content">
                        <div class="stat-card-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="stat-card-info">
                            <h3 class="stat-card-number">{{ $stats['permissions'] }}</h3>
                            <p class="stat-card-label">Total Permissions</p>
                        </div>
                    </div>
                    <div class="stat-card-footer">
                        <span class="stat-card-trend">
                            <i class="fas fa-arrow-up"></i> Active
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-card-body">
                    <div class="stat-card-content">
                        <div class="stat-card-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="stat-card-info">
                            <h3 class="stat-card-number">{{ $stats['roles'] }}</h3>
                            <p class="stat-card-label">Total Roles</p>
                        </div>
                    </div>
                    <div class="stat-card-footer">
                        <span class="stat-card-trend">
                            <i class="fas fa-arrow-up"></i> Active
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-card-body">
                    <div class="stat-card-content">
                        <div class="stat-card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-card-info">
                            <h3 class="stat-card-number">{{ $stats['users'] }}</h3>
                            <p class="stat-card-label">Total Users</p>
                        </div>
                    </div>
                    <div class="stat-card-footer">
                        <span class="stat-card-trend">
                            <i class="fas fa-arrow-up"></i> Registered
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Managed Tables Stats -->
    @if (count($stats['managed_tables']) > 0)
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="section-header">
                    <h5 class="section-title">
                        <i class="fas fa-database me-2"></i>
                        Managed Tables Overview
                        <span class="badge bg-primary ms-2">{{ count($stats['managed_tables']) }}</span>
                    </h5>
                </div>
            </div>
            @foreach ($stats['managed_tables'] as $table)
                <div class="col-lg-3 col-md-6">
                    <a href="{{ route('accesstree.admin.tables.index', $table['table']) }}" class="stat-card-link">
                        <div class="stat-card stat-card-secondary">
                            <div class="stat-card-body">
                                <div class="stat-card-content">
                                    <div class="stat-card-icon">
                                        <i class="fas fa-table"></i>
                                    </div>
                                    <div class="stat-card-info">
                                        <h3 class="stat-card-number">{{ number_format($table['count']) }}</h3>
                                        <p class="stat-card-label">{{ $table['name'] }}</p>
                                    </div>
                                </div>
                                <div class="stat-card-footer">
                                    <span class="stat-card-trend">
                                        <i class="fas fa-arrow-right"></i> View
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Action Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="action-card">
                <div class="action-card-header">
                    <h5 class="action-card-title">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h5>
                    <p class="action-card-subtitle">Common administrative tasks</p>
                </div>
                <div class="action-card-body">
                    <div class="action-grid">
                        <a href="{{ route('accesstree.admin.permissions.create') }}" class="action-btn action-btn-primary">
                            <div class="action-btn-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-btn-content">
                                <span class="action-btn-title">Create Permission</span>
                                <span class="action-btn-subtitle">Add new permission</span>
                            </div>
                        </a>
                        <a href="{{ route('accesstree.admin.roles.create') }}" class="action-btn action-btn-success">
                            <div class="action-btn-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-btn-content">
                                <span class="action-btn-title">Create Role</span>
                                <span class="action-btn-subtitle">Add new role</span>
                            </div>
                        </a>
                        <a href="{{ route('accesstree.admin.users.index') }}" class="action-btn action-btn-info">
                            <div class="action-btn-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="action-btn-content">
                                <span class="action-btn-title">Manage Users</span>
                                <span class="action-btn-subtitle">User management</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="status-card">
                <div class="status-card-header">
                    <h5 class="status-card-title">
                        <i class="fas fa-heartbeat me-2"></i>
                        System Status
                    </h5>
                    <div class="status-indicator status-online">
                        <span class="status-dot"></span>
                        Online
                    </div>
                </div>
                <div class="status-card-body">
                    <div class="status-grid">
                        <div class="status-item">
                            <div class="status-item-icon">
                                <i class="fas fa-key"></i>
                            </div>
                            <div class="status-item-content">
                                <span class="status-item-label">Active Permissions</span>
                                <span class="status-item-value">{{ $stats['active_permissions'] }}</span>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-item-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="status-item-content">
                                <span class="status-item-label">Active Roles</span>
                                <span class="status-item-value">{{ $stats['active_roles'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="status-message">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        AccessTree is running properly. All systems operational.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="activity-card">
                <div class="activity-card-header">
                    <h5 class="activity-card-title">
                        <i class="fas fa-history me-2"></i>
                        Recent Activity
                    </h5>
                </div>
                <div class="activity-card-body">
                    <div class="activity-list">
                        @if (count($recentActivity) > 0)
                            @foreach ($recentActivity as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon {{ $activity['color'] }}">
                                        <i class="{{ $activity['icon'] }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <span class="activity-title">{{ $activity['title'] }}</span>
                                        <span class="activity-time">{{ $activity['time'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="activity-item">
                                <div class="activity-icon activity-icon-info">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="activity-content">
                                    <span class="activity-title">No recent activity</span>
                                    <span class="activity-time">Get started by creating permissions or roles</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Section Header Styles */
        .section-header {
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            display: flex;
            align-items: center;
            margin: 0;
        }

        .stat-card-link {
            text-decoration: none !important;
            color: inherit !important;
            display: block;
        }

        .stat-card-link:hover {
            text-decoration: none !important;
            color: inherit !important;
        }

        /* Stat Card Secondary Variant */
        .stat-card-secondary {
            background: white !important;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            cursor: pointer;
            display: block !important;
        }

        .stat-card-secondary:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15) !important;
            border-color: #667eea;
        }

        .stat-card-secondary .stat-card-icon {
            background: linear-gradient(135deg, #48bb78, #38a169) !important;
            color: white !important;
        }

        .stat-card-secondary .stat-card-number {
            color: #2d3748 !important;
            font-weight: 700;
        }

        .stat-card-secondary .stat-card-label {
            color: #718096 !important;
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

        .stat-card-secondary:hover .stat-card-trend {
            color: #667eea;
        }
    </style>
@endsection
