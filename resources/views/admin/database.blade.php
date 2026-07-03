@extends('layouts.app')

@section('page-title', 'Database Management')

@section('content')
<style>
    .db-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        border: 1px solid #e8e8e8;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        text-align: center;
        height: 100%;
    }
    .db-card .title {
        font-weight: 600;
        font-size: 18px;
        margin-bottom: 6px;
    }
    .db-card .subtitle {
        color: #999;
        font-size: 14px;
        margin-bottom: 16px;
    }
    .btn-action {
        display: inline-block;
        padding: 10px 30px;
        border-radius: 40px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: 0.2s;
    }
    .btn-download {
        background: #28a745;
        color: white;
    }
    .btn-download:hover {
        background: #1e7e34;
        color: white;
    }
    .btn-import {
        background: #6F4E37;
        color: white;
    }
    .btn-import:hover {
        background: #5d3e2a;
        color: white;
    }
    .upload-area {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 6px;
    }
    .upload-area:hover {
        border-color: #6F4E37;
        background: #fafafa;
    }
    .upload-area p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }
    .alert-success {
        background: #d4edda;
        color: #155724;
        padding: 12px 20px;
        border-radius: 8px;
        border: 1px solid #c3e6cb;
        margin-bottom: 15px;
    }
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        padding: 12px 20px;
        border-radius: 8px;
        border: 1px solid #f5c6cb;
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Database Management</h2>
        <a href="{{ route('admin.settings') }}" class="btn btn-secondary btn-sm">
            Back to Settings
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- Download Database -->
        <div class="col-md-6">
            <div class="db-card">
                <div class="title">Download Database</div>
                <div class="subtitle">Export database as SQLite file</div>
                <a href="{{ route('admin.export.backup') }}" class="btn-action btn-download">
                    Download SQLite
                </a>
            </div>
        </div>

        <!-- Import Database -->
        <div class="col-md-6">
            <div class="db-card">
                <div class="title">Import Database</div>
                <div class="subtitle">Restore from SQLite file</div>
                <form action="{{ route('admin.import.backup') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div class="upload-area" onclick="document.getElementById('sqlFile').click()">
                        <p>Click to upload SQLite file</p>
                        <input type="file" name="file" id="sqlFile" class="d-none" accept=".sqlite,.sql,.db">
                        <span id="fileName" style="font-size:13px; color:#6F4E37;"></span>
                    </div>
                    <button type="submit" class="btn-action btn-import mt-2" id="importBtn">
                        Import SQLite
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="db-card" style="padding:15px 20px; text-align:left;">
                <div class="row text-center">
                    <div class="col-3">
                        <span style="font-size:20px;font-weight:700;color:#6F4E37;">{{ $stats['products'] ?? 0 }}</span>
                        <div style="color:#999;font-size:12px;">Products</div>
                    </div>
                    <div class="col-3">
                        <span style="font-size:20px;font-weight:700;color:#6F4E37;">{{ $stats['items'] ?? 0 }}</span>
                        <div style="color:#999;font-size:12px;">Items</div>
                    </div>
                    <div class="col-3">
                        <span style="font-size:20px;font-weight:700;color:#6F4E37;">{{ $stats['branches'] ?? 0 }}</span>
                        <div style="color:#999;font-size:12px;">Branches</div>
                    </div>
                    <div class="col-3">
                        <span style="font-size:20px;font-weight:700;color:#6F4E37;">{{ $stats['customers'] ?? 0 }}</span>
                        <div style="color:#999;font-size:12px;">Customers</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('sqlFile').addEventListener('change', function() {
        const fileName = document.getElementById('fileName');
        if (this.files.length > 0) {
            fileName.textContent = 'Selected: ' + this.files[0].name;
        } else {
            fileName.textContent = '';
        }
    });

    document.getElementById('importForm').addEventListener('submit', function() {
        const btn = document.getElementById('importBtn');
        btn.textContent = 'Importing...';
        btn.disabled = true;
    });
</script>
@endpush
@endsection