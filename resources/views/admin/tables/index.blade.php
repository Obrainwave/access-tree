@extends('accesstree::admin.layouts.app')

@section('title', 'All Tables')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-database"></i> Database Tables
                        </h3>
                        <div>
                            <a href="{{ route('accesstree.admin.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            <button class="btn btn-primary" onclick="discoverTables()">
                                <i class="fas fa-search"></i> Discover Tables
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (count($tables) > 0)
                            <div class="row">
                                @foreach ($tables as $table)
                                    <div class="col-md-4 col-lg-3 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fas fa-table fa-3x text-primary"></i>
                                                </div>
                                                <h5 class="card-title">{{ ucfirst(str_replace('_', ' ', $table)) }}</h5>
                                                <p class="card-text text-muted">
                                                    {{ ucfirst(str_replace('_', ' ', $table)) }} table
                                                </p>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('accesstree.admin.tables.index', $table) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-list"></i> View
                                                    </a>
                                                    <a href="{{ route('accesstree.admin.tables.create', $table) }}"
                                                        class="btn btn-sm btn-success">
                                                        <i class="fas fa-plus"></i> Add
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-database fa-5x text-muted mb-3"></i>
                                <h4>No Tables Found</h4>
                                <p class="text-muted">No user tables found in the database.</p>
                                <button class="btn btn-primary" onclick="discoverTables()">
                                    <i class="fas fa-search"></i> Discover Tables
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function discoverTables() {
            if (confirm('This will discover all tables in your database. Continue?')) {
                // You can implement AJAX call here to run the discover command
                window.location.href = '{{ route('accesstree.admin.tables.overview') }}?discover=1';
            }
        }
    </script>
@endsection
