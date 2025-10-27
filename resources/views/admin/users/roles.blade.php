@extends('accesstree::admin.layouts.app')

@section('title', 'Manage User Roles')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Manage Roles for {{ $user->name }}</h3>
                    <div>
                        <a href="{{ route("accesstree.admin.{$resourceName}.show", $user) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to User
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("accesstree.admin.{$resourceName}.sync-roles", $user) }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Current Roles</h5>
                                @if ($user->roles->count() > 0)
                                    <ul class="list-group">
                                        @foreach ($user->roles as $role)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $role->name }}
                                                <span
                                                    class="badge bg-primary rounded-pill">{{ $role->permissions->count() }}
                                                    permissions</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No roles assigned</p>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <h5>Available Roles</h5>
                                <div class="form-check-group">
                                    @foreach ($allRoles as $role)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]"
                                                value="{{ $role->id }}" id="role_{{ $role->id }}"
                                                {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                                <small class="text-muted">({{ $role->permissions->count() }}
                                                    permissions)</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Roles
                            </button>
                            <a href="{{ route("accesstree.admin.{$resourceName}.show", $user) }}"
                                class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
