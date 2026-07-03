@extends('layouts.app')

@section('page-title', 'Staff Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-users me-2"></i>Staff Management</h2>
            @if(request()->has('branch_id'))
                @php
                    $branch = App\Models\Branch::find(request()->branch_id);
                    $location = $branch ? str_replace('☕ Brew & Bean Co. - ', '', $branch->name) : '';
                @endphp
                @if($branch)
                    <small class="text-muted">
                        <i class="fas fa-store me-1"></i> 
                        {{ $location }} Branch
                    </small>
                @endif
            @endif
        </div>
        <a href="{{ route('staff.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i> Add Staff
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
                            <th><i class="fas fa-user me-1"></i>Name</th>
                            <th><i class="fas fa-envelope me-1"></i>Email</th>
                            <th><i class="fas fa-store me-1"></i>Branch</th>
                            <th><i class="fas fa-user-tag me-1"></i>Role</th>
                            <th><i class="fas fa-clock me-1"></i>Joined</th>
                            <th class="text-center"><i class="fas fa-cog me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $member)
                        @php
                            // Extract location from branch name
                            $location = '';
                            if ($member->branch) {
                                $location = str_replace('☕ Brew & Bean Co. - ', '', $member->branch->name);
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2" style="width: 32px; height: 32px; background: #6F4E37; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $member->name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $member->email }}</td>
                            <td>
                                @if($member->branch)
                                    <span class="badge bg-primary">
                                        <i class="fas fa-store me-1"></i> {{ $location ?: $member->branch->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">No Branch</span>
                                @endif
                            </td>
                            <td>
                                @if($member->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($member->role === 'manager')
                                    <span class="badge bg-warning text-dark">Manager</span>
                                @elseif($member->role === 'staff')
                                    <span class="badge bg-info">Staff</span>
                                @else
                                    <span class="badge bg-secondary">{{ $member->role }}</span>
                                @endif
                            </td>
                            <td>{{ $member->created_at->format('M d, Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('staff.edit', $member) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('staff.destroy', $member) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this staff member?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x d-block mb-2"></i>
                                No staff members found.
                                @if(request()->has('branch_id'))
                                    <br><small>Try selecting a different branch or clear the filter.</small>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-2 text-muted small">
        <i class="fas fa-info-circle me-1"></i>Total: <strong>{{ $staff->count() }}</strong> staff members
        @if(request()->has('branch_id'))
            @php
                $branch = App\Models\Branch::find(request()->branch_id);
                $location = $branch ? str_replace('☕ Brew & Bean Co. - ', '', $branch->name) : '';
            @endphp
            @if($branch)
                <span class="ms-2 badge bg-primary">{{ $location }} Branch</span>
            @endif
        @endif
    </div>
</div>

<style>
    .avatar-circle {
        width: 32px;
        height: 32px;
        background: #6F4E37;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        flex-shrink: 0;
    }
</style>
@endsection