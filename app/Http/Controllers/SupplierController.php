<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $supplier = Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier "' . $supplier->name . '" added!');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->items()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Cannot delete supplier with assigned items.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted!');
    }
}