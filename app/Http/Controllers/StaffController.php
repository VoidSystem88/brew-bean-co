<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::with('branch')
            ->whereIn('role', ['admin', 'manager', 'staff', 'delivery'])
            ->paginate(10);
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('staff.create', compact('branches'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'role' => ['required', Rule::in(['admin', 'manager', 'staff', 'delivery'])],
    ]);

    // Only require branch_id for staff and delivery roles
    if (in_array($request->role, ['staff', 'delivery'])) {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);
        $validated['branch_id'] = $request->branch_id;
    } else {
        // Admin and Manager have no branch restriction
        $validated['branch_id'] = null;
    }

    $validated['password'] = Hash::make($validated['password']);
    $validated['is_active'] = 1;

    User::create($validated);

    $roleName = ucfirst($validated['role']);
    if ($roleName === 'Delivery') {
        $roleName = 'Delivery Rider';
    }

    return redirect()->route('staff.index')
        ->with('success', $roleName . ' created successfully.');
}

    public function edit(User $staff)
    {
        $branches = Branch::all();
        return view('staff.edit', compact('staff', 'branches'));
    }

    public function update(Request $request, User $staff)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => ['required', 'email', Rule::unique('users')->ignore($staff->id)],
        'role' => ['required', Rule::in(['admin', 'manager', 'staff', 'delivery'])],
    ]);

    // Only require branch_id for staff and delivery roles
    if (in_array($request->role, ['staff', 'delivery'])) {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);
        $validated['branch_id'] = $request->branch_id;
    } else {
        // Admin and Manager have no branch restriction
        $validated['branch_id'] = null;
    }

    if ($request->filled('password')) {
        $validated['password'] = Hash::make($request->password);
        $staff->update($validated);
    } else {
        $staff->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'branch_id' => $validated['branch_id'],
        ]);
    }

    return redirect()->route('staff.index')
        ->with('success', 'Staff member updated successfully.');
}

    public function destroy(User $staff)
    {
        $staff->delete();
        return redirect()->route('staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }
}