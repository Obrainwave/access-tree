@extends('accesstree::admin.layouts.app')

@section('title', 'View User')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">User Details</h3>
                    <div>
                        <a href="{{ route("accesstree.admin.{$resourceName}.roles", $item) }}" class="btn btn-primary">
                            <i class="fas fa-users-cog"></i> Manage Roles
                        </a>
                        <a href="{{ route("accesstree.admin.{$resourceName}.index") }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $item->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $item->email }}</td>
                                </tr>
                                <tr>
                                    <th>Root User:</th>
                                    <td>
                                        <span class="badge bg-{{ $item->is_root_user ? 'danger' : 'secondary' }}">
                                            {{ $item->is_root_user ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Roles:</th>
                                    <td>{{ $item->roles->count() }}</td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $item->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated:</th>
                                    <td>{{ $item->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
