@extends('accesstree::admin.layouts.app')

@section('title', 'System Logs')

@section('content')
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="page-header-content">
                    <div class="page-title-section">
                        <h2 class="page-title">
                            <i class="fas fa-file-alt me-2"></i>
                            System Logs
                        </h2>
                        <p class="page-subtitle">View and monitor application logs for debugging</p>
                    </div>
                    <div class="page-actions">
                        <div class="btn-group">
                            <select id="linesSelect" class="modern-input" onchange="refreshLogs()">
                                <option value="50" {{ $lines == 50 ? 'selected' : '' }}>Last 50 lines</option>
                                <option value="100" {{ $lines == 100 ? 'selected' : '' }}>Last 100 lines</option>
                                <option value="200" {{ $lines == 200 ? 'selected' : '' }}>Last 200 lines</option>
                                <option value="500" {{ $lines == 500 ? 'selected' : '' }}>Last 500 lines</option>
                                <option value="1000" {{ $lines == 1000 ? 'selected' : '' }}>Last 1000 lines</option>
                            </select>
                            <button class="modern-btn modern-btn-primary" onclick="refreshLogs()">
                                <i class="fas fa-sync-alt"></i>
                                Refresh
                            </button>
                        </div>
                        <a href="{{ route('accesstree.admin.system.logs.download') }}"
                            class="modern-btn modern-btn-success">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                        <button class="modern-btn modern-btn-secondary" onclick="clearLogs()">
                            <i class="fas fa-trash"></i>
                            Clear Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Content -->
    <div class="row">
        <div class="col-12">
            <div class="modern-table-card">
                <div class="action-card-header">
                    <h5 class="action-card-title">
                        <i class="fas fa-file-code me-2"></i>
                        Laravel Application Logs ({{ $lines }} lines)
                    </h5>
                </div>
                <div class="log-content">
                    <pre id="logContent">{{ $logContent }}</pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .log-content {
            padding: 2rem;
            background: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
            line-height: 1.6;
            max-height: 600px;
            overflow-y: auto;
            border-radius: 0 0 16px 16px;
        }

        .log-content pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Log line formatting */
        .log-line {
            padding: 0.25rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .log-line:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .log-content::-webkit-scrollbar {
            width: 8px;
        }

        .log-content::-webkit-scrollbar-track {
            background: #2d2d2d;
        }

        .log-content::-webkit-scrollbar-thumb {
            background: #555;
            border-radius: 4px;
        }

        .log-content::-webkit-scrollbar-thumb:hover {
            background: #777;
        }

        /* Log date styling */
        .log-date {
            font-weight: 600;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn-group select {
            border-radius: 12px 0 0 12px;
        }

        .btn-group button {
            border-radius: 0 12px 12px 0;
        }

        @media (max-width: 768px) {
            .page-actions {
                flex-direction: column;
                width: 100%;
            }

            .btn-group,
            .modern-btn {
                width: 100%;
            }
        }
    </style>

    <script>
        function refreshLogs() {
            const lines = document.getElementById('linesSelect').value;
            const logContentElement = document.getElementById('logContent');

            // Show loading state
            logContentElement.textContent = 'Loading...';

            fetch('{{ route('accesstree.admin.system.logs.refresh') }}?lines=' + lines)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Set the raw content
                        logContentElement.textContent = data.content;
                    } else {
                        alert('Error refreshing logs');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error refreshing logs');
                });
        }

        function clearLogs() {
            if (!confirm('Are you sure you want to clear all logs? This action cannot be undone.')) {
                return;
            }

            fetch('{{ route('accesstree.admin.system.logs.clear') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Error clearing logs');
                    }
                })
                .catch(error => {
                    alert('Error clearing logs');
                });
        }

        // Auto-refresh every 30 seconds
        setInterval(refreshLogs, 30000);
    </script>
@endsection
