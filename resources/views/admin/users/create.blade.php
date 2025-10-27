@extends('accesstree::admin.layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="modern-resource-page">
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1 class="page-title">Create User</h1>
                    <p class="page-subtitle">Add a new user to the system</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('accesstree.admin.users.index') }}" class="modern-btn modern-btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Users</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="card-header">
                <h3 class="card-title">User Information</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('accesstree.admin.users.store') }}">
                    @csrf

                    @foreach ($fields as $fieldName => $fieldConfig)
                        <div class="form-group">
                            <label for="{{ $fieldName }}" class="form-label">
                                {{ $fieldConfig['label'] }}
                                @if ($fieldConfig['required'] ?? false)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @if ($fieldConfig['type'] === 'text' || $fieldConfig['type'] === 'email')
                                <input type="{{ $fieldConfig['type'] }}"
                                    class="form-control @error($fieldName) is-invalid @enderror" id="{{ $fieldName }}"
                                    name="{{ $fieldName }}" value="{{ old($fieldName) }}"
                                    placeholder="{{ $fieldConfig['placeholder'] ?? '' }}"
                                    @if ($fieldConfig['required'] ?? false) required @endif>
                            @elseif($fieldConfig['type'] === 'checkbox-group')
                                <div class="checkbox-group row">
                                    @foreach ($fieldConfig['options'] as $value => $label)
                                        <div class="col-md-4 col-sm-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="{{ $fieldName }}[]"
                                                    value="{{ $value }}"
                                                    id="{{ $fieldName }}_{{ $value }}"
                                                    {{ in_array($value, old($fieldName, [])) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="{{ $fieldName }}_{{ $value }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @error($fieldName)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach

                    <div class="form-actions">
                        <button type="submit" class="modern-btn modern-btn-primary">
                            <i class="fas fa-save"></i>
                            <span>Create User</span>
                        </button>
                        <a href="{{ route('accesstree.admin.users.index') }}" class="modern-btn modern-btn-secondary">
                            <i class="fas fa-times"></i>
                            <span>Cancel</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
