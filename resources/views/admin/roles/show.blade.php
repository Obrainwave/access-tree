@extends('accesstree::admin.layouts.app')

@section('title', 'View Role')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Role Details</h3>
                    <div>
                        @can("edit_{$resourceName}")
                            <a href="{{ route("accesstree.admin.{$resourceName}.edit", $item) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
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
                                    <th>Slug:</th>
                                    <td><code>{{ $item->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $item->status ? 'success' : 'secondary' }}">
                                            {{ $item->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Permissions:</th>
                                    <td>{{ $item->permissions->count() }}</td>
                                </tr>
                                <tr>
                                    <th>Users:</th>
                                    <td>{{ $item->users->count() }}</td>
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
