@extends('accesstree::admin.layouts.app')

@php
    // Define helper functions
    function getFieldType($column)
    {
        if (str_contains($column, 'email')) {
            return 'email';
        }
        if (str_contains($column, 'password')) {
            return 'password';
        }
        if (str_contains($column, 'phone')) {
            return 'tel';
        }
        if (str_contains($column, 'url')) {
            return 'url';
        }
        if (str_contains($column, 'date')) {
            return 'date';
        }
        if (str_contains($column, 'time')) {
            return 'datetime-local';
        }
        if (
            str_contains($column, 'description') ||
            str_contains($column, 'content') ||
            str_contains($column, 'notes')
        ) {
            return 'textarea';
        }
        if (str_contains($column, 'status') || str_contains($column, 'type') || str_contains($column, 'category')) {
            return 'select';
        }
        if (str_contains($column, 'active') || str_contains($column, 'enabled') || str_contains($column, 'is_')) {
            return 'checkbox';
        }
        if (str_contains($column, 'amount') || str_contains($column, 'price') || str_contains($column, 'cost')) {
            return 'number';
        }
        if (str_contains($column, 'image') || str_contains($column, 'photo') || str_contains($column, 'avatar')) {
            return 'file';
        }

        return 'text';
    }

    function getSelectOptions($column)
    {
        $options = [];

        if (str_contains($column, 'status')) {
            $options = ['active' => 'Active', 'inactive' => 'Inactive', 'pending' => 'Pending'];
        } elseif (str_contains($column, 'type')) {
            $options = ['standard' => 'Standard', 'premium' => 'Premium', 'enterprise' => 'Enterprise'];
        } elseif (str_contains($column, 'category')) {
            $options = ['general' => 'General', 'technical' => 'Technical', 'support' => 'Support'];
        }

        return $options;
    }

    function getFieldHelpText($column)
    {
        if (str_contains($column, 'email')) {
            return 'Enter a valid email address';
        }
        if (str_contains($column, 'phone')) {
            return 'Enter phone number with country code';
        }
        if (str_contains($column, 'url')) {
            return 'Enter a valid URL starting with http:// or https://';
        }
        if (str_contains($column, 'date')) {
            return 'Select a date';
        }
        if (str_contains($column, 'password')) {
            return 'Password must be at least 8 characters';
        }

        return null;
    }
@endphp

@section('title', (isset($item) ? 'Edit' : 'Create') . ' ' . ucfirst(str_replace('_', ' ', Str::singular($table))))

@section('content')
    <!-- Modern Form Page -->
    <div class="modern-resource-page">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1 class="page-title">
                        <i class="fas fa-{{ isset($item) ? 'edit' : 'plus' }} me-3"></i>
                        {{ isset($item) ? 'Edit' : 'Create' }} {{ ucfirst(str_replace('_', ' ', Str::singular($table))) }}
                    </h1>
                    <p class="page-subtitle">
                        {{ isset($item) ? 'Update the information below' : 'Fill in the information below to create a new ' . str_replace('_', ' ', Str::singular($table)) }}
                    </p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('accesstree.admin.tables.index', $table) }}" class="modern-btn modern-btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Modern Form Card -->
        <div class="modern-form-card">
            <form
                action="{{ isset($item) ? route('accesstree.admin.tables.update', [$table, $item->id]) : route('accesstree.admin.tables.store', $table) }}"
                method="POST" enctype="multipart/form-data" class="modern-form">
                @csrf
                @if (isset($item))
                    @method('PUT')
                @endif

                <div class="form-grid">
                    @foreach ($fillableColumns as $column)
                        <div class="form-group">
                            <label for="{{ $column }}" class="form-label">
                                {{ ucfirst(str_replace('_', ' ', $column)) }}
                                @if (str_contains($column, 'required') || in_array($column, ['name', 'title', 'email']))
                                    <span class="required-asterisk">*</span>
                                @endif
                            </label>

                            @php
                                $fieldType = getFieldType($column);
                            @endphp

                            @if ($fieldType === 'textarea')
                                <textarea class="modern-input @error($column) error @enderror" id="{{ $column }}" name="{{ $column }}"
                                    rows="4" placeholder="Enter {{ str_replace('_', ' ', $column) }}..."
                                    {{ in_array($column, ['name', 'title', 'email']) ? 'required' : '' }}>{{ old($column, $item->$column ?? '') }}</textarea>
                            @elseif($fieldType === 'select')
                                <select class="modern-input @error($column) error @enderror" id="{{ $column }}"
                                    name="{{ $column }}"
                                    {{ in_array($column, ['name', 'title', 'email']) ? 'required' : '' }}>
                                    <option value="">Select {{ ucfirst(str_replace('_', ' ', $column)) }}</option>
                                    @foreach (getSelectOptions($column) as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old($column, $item->$column ?? '') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif($fieldType === 'checkbox')
                                <div class="checkbox-group">
                                    <input class="modern-checkbox @error($column) error @enderror" type="checkbox"
                                        id="{{ $column }}" name="{{ $column }}" value="1"
                                        {{ old($column, $item->$column ?? false) ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="{{ $column }}">
                                        {{ ucfirst(str_replace('_', ' ', $column)) }}
                                    </label>
                                </div>
                            @else
                                <input type="{{ $fieldType }}"
                                    class="modern-input @error($column) error @enderror" id="{{ $column }}"
                                    name="{{ $column }}" value="{{ old($column, $item->$column ?? '') }}"
                                    placeholder="Enter {{ str_replace('_', ' ', $column) }}..."
                                    {{ in_array($column, ['name', 'title', 'email']) ? 'required' : '' }}
                                    @if ($fieldType === 'email') autocomplete="email" @endif
                                    @if ($fieldType === 'password') autocomplete="new-password" @endif>
                            @endif

                            @error($column)
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror

                            @php
                                $helpText = getFieldHelpText($column);
                            @endphp

                            @if ($helpText)
                                <div class="help-text">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $helpText }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="form-actions">
                    <a href="{{ route('accesstree.admin.tables.index', $table) }}" class="modern-btn modern-btn-secondary">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </a>
                    <button type="submit" class="modern-btn modern-btn-primary">
                        <i class="fas fa-save"></i>
                        <span>{{ isset($item) ? 'Update' : 'Create' }}
                            {{ ucfirst(str_replace('_', ' ', Str::singular($table))) }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Modern Form Styles */
        .modern-form-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .modern-form {
            padding: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .required-asterisk {
            color: #e53e3e;
            margin-left: 0.25rem;
        }

        .modern-input {
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .modern-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .modern-input.error {
            border-color: #e53e3e;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
        }

        .modern-checkbox {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 2px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modern-checkbox:checked {
            background: #667eea;
            border-color: #667eea;
        }

        .checkbox-label {
            font-weight: 500;
            color: #4a5568;
            cursor: pointer;
            margin: 0;
        }

        .error-message {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .help-text {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #718096;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .modern-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection
