<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount('staff')->orderBy('name')->get();
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $branchName = '☕ Brew & Bean Co. - ' . trim($request->location);

        $branch = Branch::create([
            'name' => $branchName,
            'location' => trim($request->location),
            'contact_number' => $request->phone ?? null, // Use contact_number instead of phone
            'address' => $request->address ?? null,
            'phone' => $request->phone ?? null,
            'email' => $request->email ?? null,
        ]);

        return redirect()->route('branches.index')
            ->with('success', '✅ Branch "' . $branch->name . '" created successfully!');
    }

    public function show(Branch $branch)
    {
        $branch->loadCount('staff');
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $location = str_replace('☕ Brew & Bean Co. - ', '', $branch->name);
        return view('branches.edit', compact('branch', 'location'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $branchName = '☕ Brew & Bean Co. - ' . trim($request->location);

        $branch->update([
            'name' => $branchName,
            'location' => trim($request->location),
            'contact_number' => $request->phone ?? null,
            'address' => $request->address ?? null,
            'phone' => $request->phone ?? null,
            'email' => $request->email ?? null,
        ]);

        return redirect()->route('branches.index')
            ->with('success', '✅ Branch updated successfully!');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->staff()->count() > 0) {
            return redirect()->route('branches.index')
                ->with('error', '❌ Cannot delete branch with assigned staff. Please reassign staff first.');
        }

        $branchName = $branch->name;
        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', '✅ Branch "' . $branchName . '" deleted successfully!');
    }
}