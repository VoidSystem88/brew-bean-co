@extends('layouts.app')

@section('page-title', 'Branches')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-store me-2"></i>Branches</h2>
        <a href="{{ route('branches.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> Add Branch
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th><i class="fas fa-store me-1"></i>Branch Name</th>
                            <th><i class="fas fa-location-dot me-1"></i>Address</th>
                            <th><i class="fas fa-phone me-1"></i>Phone</th>
                            <th><i class="fas fa-envelope me-1"></i>Email</th>
                            <th class="text-center"><i class="fas fa-users me-1"></i>Staff</th>
                            <th class="text-center"><i class="fas fa-cog me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $branch->name }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $branch->staff_count ?? 0 }} staff member(s)
                                </small>
                            </td>
                            <td>{{ $branch->address ?? 'N/A' }}</td>
                            <td>{{ $branch->phone ?? 'N/A' }}</td>
                            <td>{{ $branch->email ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">
                                    {{ $branch->staff_count ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this branch?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-store fa-3x d-block mb-2"></i>
                                No branches found. Click "Add Branch" to create one.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-2 text-muted small">
        <i class="fas fa-info-circle me-1"></i>Total: <strong>{{ $branches->count() }}</strong> branches
    </div>
</div>
@endsection