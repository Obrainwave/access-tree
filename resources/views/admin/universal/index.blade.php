@extends('accesstree::admin.layouts.app')

@section('title', 'Manage ' . ucfirst(str_replace('_', ' ', $table)))

@section('content')
    <!-- Modern Universal Table Management Page -->
    <div class="modern-resource-page">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1 class="page-title">
                        <i class="fas fa-table me-3"></i>
                        {{ ucfirst(str_replace('_', ' ', $table)) }} Management
                    </h1>
                    <p class="page-subtitle">Manage and organize your {{ str_replace('_', ' ', $table) }} data efficiently
                    </p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('accesstree.admin.tables.overview') }}" class="modern-btn modern-btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>All Tables</span>
                    </a>
                    <a href="{{ route('accesstree.admin.tables.create', $table) }}" class="modern-btn modern-btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Add New {{ ucfirst(str_replace('_', ' ', Str::singular($table))) }}</span>
                    </a>
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
                            placeholder="Search {{ ucfirst(str_replace('_', ' ', $table)) }}..."
                            value="{{ request('search') }}">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="filter-group">
                        <select name="per_page" class="filter-select" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                        </select>
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
                            @foreach (array_slice($columns, 0, 5) as $column)
                                <th class="table-header">{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                            @endforeach
                            <th class="table-header actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(${$table} as $item)
                            <tr class="table-row">
                                @foreach (array_slice($columns, 0, 5) as $column)
                                    <td class="table-cell">
                                        @if ($column === 'created_at' || $column === 'updated_at')
                                            <span class="date-cell">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ $item->$column ? \Carbon\Carbon::parse($item->$column)->format('M d, Y H:i') : '-' }}
                                            </span>
                                        @elseif($column === 'email')
                                            <a href="mailto:{{ $item->$column }}" class="email-link">
                                                <i class="fas fa-envelope me-1"></i>
                                                {{ $item->$column }}
                                            </a>
                                        @elseif($column === 'status' || $column === 'active')
                                            <span class="status-badge status-{{ $item->$column ? 'active' : 'inactive' }}">
                                                <i class="fas fa-circle"></i>
                                                {{ $item->$column ? 'Active' : 'Inactive' }}
                                            </span>
                                        @else
                                            <span class="text-cell">{!! Str::limit($item->$column, 50) !!}</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="table-cell actions-cell">
                                    <div class="action-buttons">
                                        <a href="{{ route('accesstree.admin.tables.show', [$table, $item->id]) }}"
                                            class="action-btn action-btn-view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('accesstree.admin.tables.edit', [$table, $item->id]) }}"
                                            class="action-btn action-btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('accesstree.admin.tables.destroy', [$table, $item->id]) }}"
                                            method="POST" class="d-inline" onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="table-row">
                                <td colspan="{{ count(array_slice($columns, 0, 5)) + 1 }}" class="table-cell empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-inbox empty-icon"></i>
                                        <h3>No {{ str_replace('_', ' ', $table) }} found</h3>
                                        <p>Get started by creating your first
                                            {{ str_replace('_', ' ', Str::singular($table)) }}</p>
                                        <a href="{{ route('accesstree.admin.tables.create', $table) }}"
                                            class="modern-btn modern-btn-primary">
                                            <i class="fas fa-plus"></i>
                                            <span>Create First
                                                {{ ucfirst(str_replace('_', ' ', Str::singular($table))) }}</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if (${$table}->hasPages())
                <div class="pagination-container">
                    <div class="pagination-info">
                        <span class="pagination-text">
                            Showing {{ ${$table}->firstItem() }} to {{ ${$table}->lastItem() }} of
                            {{ ${$table}->total() }} results
                        </span>
                    </div>
                    <div class="pagination-links">
                        {{ ${$table}->appends(request()->query())->links('pagination::default') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Additional Universal Table Styles */
        .modern-btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }

        .filter-group {
            margin-left: 1rem;
        }

        .filter-select {
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .date-cell {
            color: #718096;
            font-size: 0.875rem;
        }

        .email-link {
            color: #667eea;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .email-link:hover {
            color: #5a67d8;
            text-decoration: underline;
        }

        .text-cell {
            color: #4a5568;
        }

        .actions-header {
            text-align: center;
            width: 150px;
        }

        .pagination-info {
            display: flex;
            align-items: center;
        }

        .pagination-text {
            color: #718096;
            font-size: 0.875rem;
        }

        .pagination-links {
            display: flex;
            align-items: center;
        }

        /* Responsive adjustments for universal tables */
        @media (max-width: 768px) {
            .search-container {
                flex-direction: column;
                gap: 1rem;
            }

            .filter-group {
                margin-left: 0;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.25rem;
            }
        }
    </style>

    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this item? This action cannot be undone.');
        }
    </script>
@endsection
