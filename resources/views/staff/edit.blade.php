@extends('layouts.app')

@section('page-title', 'Edit Staff')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Staff</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.update', $staff) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Full Name *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="e.g., Juan Dela Cruz"
                                       value="{{ old('name', $staff->name) }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email Address *</label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       placeholder="e.g., juan@brewbeanco.com"
                                       value="{{ old('email', $staff->email) }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-bold">New Password</label>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Leave blank to keep current password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave blank if you don't want to change the password.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">Confirm New Password</label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       class="form-control" 
                                       placeholder="Confirm new password">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label fw-bold">Role *</label>
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">Select Role</option>
                                    <option value="admin" {{ old('role', $staff->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="manager" {{ old('role', $staff->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="staff" {{ old('role', $staff->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="branch_id" class="form-label fw-bold">
                                    <i class="fas fa-store me-1"></i> Branch
                                </label>
                                <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        @php
                                            $location = str_replace('☕ Brew & Bean Co. - ', '', $branch->name);
                                        @endphp
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $staff->branch_id) == $branch->id ? 'selected' : '' }}>
                                            {{ $location }}
                                            @if($location != $branch->name)
                                                <span class="text-muted">({{ $branch->name }})</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Select the branch where this staff will be assigned.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection