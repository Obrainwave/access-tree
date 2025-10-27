@extends('accesstree::admin.layouts.app')

@section('title', ucfirst($resourceName) . ' Management')

@section('content')
    <!-- Modern Resource Management Page -->
    <div class="modern-resource-page">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1 class="page-title">
                        <i
                            class="fas fa-{{ $resourceName === 'permissions' ? 'key' : ($resourceName === 'roles' ? 'users-cog' : 'users') }} me-3"></i>
                        {{ ucfirst($resourceName) }} Management
                    </h1>
                    <p class="page-subtitle">Manage and organize your {{ $resourceName }} efficiently</p>
                </div>
                <div class="page-actions">
                    @can("create_{$resourceName}")
                        <a href="{{ route("accesstree.admin.{$resourceName}.create") }}" class="modern-btn modern-btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Add {{ ucfirst($resourceName) }}</span>
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-section">
            <div class="search-container">
                <form method="GET" class="search-form">
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" class="search-input"
                            placeholder="Search {{ $resourceName }}..." value="{{ request('search') }}">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modern Table Card -->
        <div class="modern-table-card">
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            @foreach ($columns as $key => $label)
                                <th class="table-header">{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr class="table-row">
                                @foreach ($columns as $key => $label)
                                    @if ($key === 'actions')
                                        <td class="table-cell actions-cell">
                                            <div class="action-buttons">
                                                @can("view_{$resourceName}")
                                                    <a href="{{ route("accesstree.admin.{$resourceName}.show", $item) }}"
                                                        class="action-btn action-btn-view" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can("edit_{$resourceName}")
                                                    <a href="{{ route("accesstree.admin.{$resourceName}.edit", $item) }}"
                                                        class="action-btn action-btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can("delete_{$resourceName}")
                                                    <form method="POST"
                                                        action="{{ route("accesstree.admin.{$resourceName}.destroy", $item) }}"
                                                        class="d-inline" onsubmit="return confirmDelete()">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="action-btn action-btn-delete"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @elseif($key === 'status')
                                        <td class="table-cell">
                                            <span class="status-badge status-{{ $item->$key ? 'active' : 'inactive' }}">
                                                <i class="fas fa-circle"></i>
                                                {{ $item->$key ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    @elseif($key === 'created_at')
                                        <td class="table-cell">{{ $item->$key->format('M d, Y') }}</td>
                                    @elseif($key === 'permissions_count')
                                        <td class="table-cell">
                                            <span class="count-badge">{{ $item->permissions->count() }}</span>
                                        </td>
                                    @elseif($key === 'users_count')
                                        <td class="table-cell">
                                            <span class="count-badge">{{ $item->users->count() }}</span>
                                        </td>
                                    @elseif($key === 'roles_count')
                                        <td class="table-cell">
                                            <span class="count-badge">{{ $item->roles->count() }}</span>
                                        </td>
                                    @elseif($key === 'is_root_user')
                                        <td class="table-cell">
                                            <span class="status-badge status-{{ $item->$key ? 'root' : 'normal' }}">
                                                <i class="fas fa-{{ $item->$key ? 'crown' : 'user' }}"></i>
                                                {{ $item->$key ? 'Root User' : 'Normal User' }}
                                            </span>
                                        </td>
                                    @else
                                        <td class="table-cell">{{ $item->$key }}</td>
                                    @endif
                                @endforeach
                            </tr>
                        @empty
                            <tr class="table-row">
                                <td colspan="{{ count($columns) }}" class="table-cell empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-inbox empty-icon"></i>
                                        <h3>No {{ $resourceName }} found</h3>
                                        <p>Get started by creating your first {{ $resourceName }}</p>
                                        @can("create_{$resourceName}")
                                            <a href="{{ route("accesstree.admin.{$resourceName}.create") }}"
                                                class="modern-btn modern-btn-primary">
                                                <i class="fas fa-plus"></i>
                                                <span>Add {{ ucfirst($resourceName) }}</span>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($items->hasPages())
                <div class="pagination-container">
                    {{ $items->links('pagination::default') }}
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Modern Resource Page Styles */
        .modern-resource-page {
            width: 100%;
            max-width: none;
        }

        .page-header {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .page-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .page-subtitle {
            color: #718096;
            margin: 0.5rem 0 0 0;
            font-size: 1.1rem;
        }

        .page-actions {
            display: flex;
            gap: 1rem;
        }

        .modern-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .modern-btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }

        .search-section {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .search-container {
            max-width: 500px;
        }

        .search-form {
            width: 100%;
        }

        .search-input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            color: #718096;
            z-index: 2;
        }

        .search-input {
            width: 100%;
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
            position: absolute;
            right: 0.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .modern-table-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-container {
            overflow-x: auto;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table-header {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            color: #4a5568;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-row {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-row:hover {
            background: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .table-cell {
            padding: 1rem;
            vertical-align: middle;
        }

        .actions-cell {
            white-space: nowrap;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .action-btn-view {
            background: #e6fffa;
            color: #00b894;
        }

        .action-btn-edit {
            background: #e3f2fd;
            color: #1976d2;
        }

        .action-btn-delete {
            background: #ffebee;
            color: #d32f2f;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .status-root {
            background: #fff3cd;
            color: #856404;
        }

        .status-normal {
            background: #d1ecf1;
            color: #0c5460;
        }

        .count-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
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

        .empty-content h3 {
            color: #4a5568;
            margin: 0;
        }

        .empty-content p {
            color: #718096;
            margin: 0;
        }

        .pagination-container {
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header-content {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>

    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this {{ $resourceName }}? This action cannot be undone.');
        }
    </script>
@endsection
