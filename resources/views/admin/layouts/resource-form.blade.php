@extends('accesstree::admin.layouts.app')

@section('title', $title)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">{{ $title }}</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @if (isset($item))
                            @method('PUT')
                        @endif

                        @foreach ($fields as $fieldName => $fieldConfig)
                            <div class="form-group mb-3">
                                <label for="{{ $fieldName }}">{{ $fieldConfig['label'] }}</label>

                                @if ($fieldConfig['type'] === 'text')
                                    <input type="text" class="form-control @error($fieldName) is-invalid @enderror"
                                        id="{{ $fieldName }}" name="{{ $fieldName }}"
                                        value="{{ old($fieldName, $item->$fieldName ?? '') }}"
                                        placeholder="{{ $fieldConfig['placeholder'] ?? '' }}"
                                        {{ $fieldConfig['required'] ? 'required' : '' }}>
                                @elseif($fieldConfig['type'] === 'email')
                                    <input type="email" class="form-control @error($fieldName) is-invalid @enderror"
                                        id="{{ $fieldName }}" name="{{ $fieldName }}"
                                        value="{{ old($fieldName, $item->$fieldName ?? '') }}"
                                        placeholder="{{ $fieldConfig['placeholder'] ?? '' }}"
                                        {{ $fieldConfig['required'] ? 'required' : '' }}>
                                @elseif($fieldConfig['type'] === 'select')
                                    <select class="form-control @error($fieldName) is-invalid @enderror"
                                        id="{{ $fieldName }}" name="{{ $fieldName }}"
                                        {{ $fieldConfig['required'] ? 'required' : '' }}>
                                        <option value="">Select {{ $fieldConfig['label'] }}</option>
                                        @foreach ($fieldConfig['options'] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old($fieldName, $item->$fieldName ?? '') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif($fieldConfig['type'] === 'checkbox-group')
                                    <div class="checkbox-group row">
                                        @foreach ($fieldConfig['options'] as $value => $label)
                                            <div class="col-md-4 col-sm-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="{{ $fieldName }}[]" value="{{ $value }}"
                                                        id="{{ $fieldName }}_{{ $value }}"
                                                        {{ in_array($value, old($fieldName, $selectedPermissions ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="{{ $fieldName }}_{{ $value }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($fieldConfig['type'] === 'textarea')
                                    <textarea class="form-control @error($fieldName) is-invalid @enderror" id="{{ $fieldName }}"
                                        name="{{ $fieldName }}" rows="3" {{ $fieldConfig['required'] ? 'required' : '' }}>{{ old($fieldName, $item->$fieldName ?? '') }}</textarea>
                                @endif

                                @error($fieldName)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($item) ? 'Update' : 'Create' }} {{ ucfirst($resourceName) }}
                            </button>
                            <a href="{{ route("accesstree.admin.{$resourceName}.index") }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
