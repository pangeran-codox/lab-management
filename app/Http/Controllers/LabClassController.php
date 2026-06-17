<?php

namespace App\Http\Controllers;

use App\Models\LabClass;
use App\Models\Organization;
use Illuminate\Http\Request;

class LabClassController extends Controller
{
    public function index()
    {
        $classes = LabClass::with('organization')->orderBy('grade_level')->orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        return view('classes.index', compact('classes', 'organizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'grade_level' => 'required|string|max:50',
            'major' => 'nullable|string|max:100',
            'student_count' => 'nullable|integer',
            'academic_year' => 'required|string|max:20',
        ]);

        LabClass::create($validated);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, LabClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'grade_level' => 'required|string|max:50',
            'major' => 'nullable|string|max:100',
            'student_count' => 'nullable|integer',
            'academic_year' => 'required|string|max:20',
        ]);

        $class->update($validated);

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(LabClass $class)
    {
        $class->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}
