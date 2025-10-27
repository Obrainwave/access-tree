@extends('accesstree::admin.layouts.app')

@php
    // Define helper functions
    function getFieldIcon($column)
    {
        if (str_contains($column, 'email')) {
            return 'envelope';
        }
        if (str_contains($column, 'phone')) {
            return 'phone';
        }
        if (str_contains($column, 'url') || str_contains($column, 'website')) {
            return 'link';
        }
        if (str_contains($column, 'date')) {
            return 'calendar';
        }
        if (str_contains($column, 'time')) {
            return 'clock';
        }
        if (str_contains($column, 'status')) {
            return 'check-circle';
        }
        if (str_contains($column, 'active') || str_contains($column, 'enabled') || str_contains($column, 'is_')) {
            return 'toggle-on';
        }
        if (str_contains($column, 'amount') || str_contains($column, 'price') || str_contains($column, 'cost')) {
            return 'dollar-sign';
        }
        if (str_contains($column, 'image') || str_contains($column, 'photo') || str_contains($column, 'avatar')) {
            return 'image';
        }
        if (
            str_contains($column, 'description') ||
            str_contains($column, 'content') ||
            str_contains($column, 'notes')
        ) {
            return 'file-text';
        }
        if (str_contains($column, 'name') || str_contains($column, 'title')) {
            return 'tag';
        }

        return 'info-circle';
    }

    function formatFieldValue($column, $value)
    {
        if (empty($value)) {
            return null;
        }

        // Email fields
        if (str_contains($column, 'email')) {
            return '<a href="mailto:' . e($value) . '">' . e($value) . '</a>';
        }

        // URL fields
        if (str_contains($column, 'url') || str_contains($column, 'website')) {
            return '<a href="' .
                e($value) .
                '" target="_blank" rel="noopener">' .
                e($value) .
                ' <i class="fas fa-external-link-alt"></i></a>';
        }

        // Phone fields
        if (str_contains($column, 'phone')) {
            return '<a href="tel:' . e($value) . '">' . e($value) . '</a>';
        }

        // Status fields
        if (str_contains($column, 'status')) {
            $badgeClass = in_array(strtolower($value), ['active', 'enabled', 'published', 'completed'])
                ? 'bg-success'
                : 'bg-danger';
            return '<span class="badge ' . $badgeClass . '">' . ucfirst($value) . '</span>';
        }

        // Boolean fields
        if (str_contains($column, 'active') || str_contains($column, 'enabled') || str_contains($column, 'is_')) {
            $badgeClass = $value ? 'bg-success' : 'bg-danger';
            $text = $value ? 'Yes' : 'No';
            return '<span class="badge ' . $badgeClass . '">' . $text . '</span>';
        }

        // Date fields
        if (
            str_contains($column, 'date') &&
            !str_contains($column, 'updated_at') &&
            !str_contains($column, 'created_at')
        ) {
            try {
                return \Carbon\Carbon::parse($value)->format('M d, Y');
            } catch (\Exception $e) {
                return e($value);
            }
        }

        // DateTime fields
        if (
            str_contains($column, 'time') &&
            !str_contains($column, 'updated_at') &&
            !str_contains($column, 'created_at')
        ) {
            try {
                return \Carbon\Carbon::parse($value)->format('M d, Y H:i');
            } catch (\Exception $e) {
                return e($value);
            }
        }

        // Long text fields
        if (
            str_contains($column, 'description') ||
            str_contains($column, 'content') ||
            str_contains($column, 'notes')
        ) {
            if (strlen($value) > 200) {
                return '<div class="text-truncate" title="' .
                    e($value) .
                    '">' .
                    e(substr($value, 0, 200)) .
                    '...</div>';
            }
            return '<div>' . nl2br(e($value)) . '</div>';
        }

        // Image fields
        if (str_contains($column, 'image') || str_contains($column, 'photo') || str_contains($column, 'avatar')) {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return '<img src="' .
                    e($value) .
                    '" alt="' .
                    e($column) .
                    '" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">';
            }
            return e($value);
        }

        // Number fields
        if (str_contains($column, 'amount') || str_contains($column, 'price') || str_contains($column, 'cost')) {
            return number_format($value, 2);
        }

        // Default
        return e($value);
    }
@endphp

@section('title', 'View ' . ucfirst(str_replace('_', ' ', Str::singular($table))))

@section('content')
    <!-- Modern Detail Page -->
    <div class="modern-resource-page">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1 class="page-title">
                        <i class="fas fa-eye me-3"></i>
                        {{ ucfirst(str_replace('_', ' ', Str::singular($table))) }} Details
                    </h1>
                    <p class="page-subtitle">View detailed information about this
                        {{ str_replace('_', ' ', Str::singular($table)) }}</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('accesstree.admin.tables.edit', [$table, $item->id]) }}"
                        class="modern-btn modern-btn-warning">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </a>
                    <a href="{{ route('accesstree.admin.tables.index', $table) }}" class="modern-btn modern-btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="details-grid">
            @foreach ($columns as $column)
                @if (!in_array($column, ['id', 'created_at', 'updated_at']))
                    <div class="detail-card">
                        <div class="detail-card-header">
                            <h6 class="detail-label">
                                <i class="fas fa-{{ getFieldIcon($column) }} me-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $column)) }}
                            </h6>
                        </div>
                        <div class="detail-card-body">
                            <div class="detail-value">
                                @php
                                    $formattedValue = formatFieldValue($column, $item->$column);
                                @endphp

                                @if ($formattedValue)
                                    {!! $formattedValue !!}
                                @else
                                    <span class="empty-value">
                                        <i class="fas fa-minus"></i>
                                        Not set
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- System Information -->
        <div class="system-info-card">
            <div class="system-info-header">
                <h5 class="system-info-title">
                    <i class="fas fa-info-circle me-2"></i>
                    System Information
                </h5>
            </div>
            <div class="system-info-body">
                <div class="system-info-grid">
                    @if (in_array('id', $columns))
                        <div class="system-info-item">
                            <span class="system-info-label">ID</span>
                            <span class="system-info-value">{{ $item->id }}</span>
                        </div>
                    @endif
                    @if (in_array('created_at', $columns) && $item->created_at)
                        <div class="system-info-item">
                            <span class="system-info-label">Created</span>
                            <span class="system-info-value">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y H:i') }}
                            </span>
                        </div>
                    @endif
                    @if (in_array('updated_at', $columns) && $item->updated_at)
                        <div class="system-info-item">
                            <span class="system-info-label">Updated</span>
                            <span class="system-info-value">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($item->updated_at)->format('M d, Y H:i') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modern Detail Page Styles */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .detail-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .detail-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .detail-card-header {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-label {
            font-weight: 600;
            color: #4a5568;
            margin: 0;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .detail-card-body {
            padding: 1.5rem;
        }

        .detail-value {
            font-size: 1rem;
            color: #2d3748;
            line-height: 1.6;
        }

        .empty-value {
            color: #a0aec0;
            font-style: italic;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .system-info-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .system-info-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 1rem 1.5rem;
        }

        .system-info-title {
            color: white;
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .system-info-body {
            padding: 1.5rem;
        }

        .system-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .system-info-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .system-info-label {
            font-weight: 600;
            color: #718096;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .system-info-value {
            color: #4a5568;
            font-size: 1rem;
            display: flex;
            align-items: center;
        }

        .modern-btn-warning {
            background: linear-gradient(135deg, #f6ad55, #ed8936);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .system-info-grid {
                grid-template-columns: 1fr;
            }

            .page-actions {
                flex-direction: column;
                gap: 0.5rem;
            }

            .modern-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection
