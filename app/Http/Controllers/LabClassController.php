<?php

namespace App\Http\Controllers;

use App\Models\LabClass;
use App\Models\Organization;
use Illuminate\Http\Request;

class LabClassController extends Controller
{
    public function index(Request $request)
    {
        $orgId = $request->query('org_id') ?? session('org_id');
        $classesQuery = LabClass::with('organization')->orderBy('grade_level')->orderBy('name');
        
        if ($orgId) {
            $classesQuery->where('organization_id', $orgId);
        }
        
        $classes = $classesQuery->get();
        $organizations = Organization::orderBy('name')->get();
        return view('classes.index', compact('classes', 'organizations', 'orgId'));
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

        $validated['pin'] = LabClass::generateUniquePin();
        $class = LabClass::create($validated);
        
        $orgId = $request->query('org_id');
        return redirect()->back()->with('success', "Kelas berhasil ditambahkan. PIN: {$class->pin}")
            ->with('org_id', $orgId);
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

        $orgId = $request->query('org_id');
        return redirect()->back()->with('success', 'Kelas berhasil diperbarui.')
            ->with('org_id', $orgId);
    }

    public function destroy(Request $request, LabClass $class)
    {
        $class->delete();
        $orgId = $request->query('org_id');
        return redirect()->back()->with('success', 'Kelas berhasil dihapus.')
            ->with('org_id', $orgId);
    }

    public function resetPin(Request $request, LabClass $class)
    {
        // Generate PIN 6 digit baru yang unik
        $newPin = LabClass::generateUniquePin();

        $class->update(['pin' => $newPin]);

        $orgId = $request->query('org_id');
        return redirect()->back()->with('success', "PIN kelas {$class->name} berhasil direset menjadi {$newPin}.")
            ->with('org_id', $orgId);
    }
}
