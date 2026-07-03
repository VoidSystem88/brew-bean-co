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
            ->whereIn('role', ['manager', 'staff'])
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
            'role' => ['required', Rule::in(['manager', 'staff'])],
            'branch_id' => 'required|exists:branches,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = 1;

        User::create($validated);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member created successfully.');
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
            'role' => ['required', Rule::in(['manager', 'staff'])],
            'branch_id' => 'required|exists:branches,id',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $staff->update($validated);

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