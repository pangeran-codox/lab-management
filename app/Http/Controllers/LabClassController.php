<?php

namespace App\Http\Controllers;

use App\Models\LabClass;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LabClassController extends Controller
{
    public function index()
    {
        $classes       = LabClass::with('organization')->orderBy('grade_level')->orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        return view('classes.index', compact('classes', 'organizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'grade_level'     => 'required|string|max:50',
            'major'           => 'nullable|string|max:100',
            'student_count'   => 'nullable|integer',
            'academic_year'   => 'required|string|max:20',
        ]);

        // Auto-generate PIN unik saat kelas dibuat
        $validated['pin'] = LabClass::generateUniquePin();

        LabClass::create($validated);

        // Clear cache kelas
        Cache::forget('active_classes');

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, LabClass $class)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'grade_level'     => 'required|string|max:50',
            'major'           => 'nullable|string|max:100',
            'student_count'   => 'nullable|integer',
            'academic_year'   => 'required|string|max:20',
        ]);

        $class->update($validated);

        // Clear cache kelas
        Cache::forget('active_classes');

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function resetPin(LabClass $class)
    {
        $class->update(['pin' => LabClass::generateUniquePin()]);

        // Hapus cache PIN lama kalau ada
        Cache::forget('class_pin_' . $class->pin);
        Cache::forget('active_classes');

        return back()->with('success', "PIN kelas {$class->name} berhasil di-reset.");
    }

    public function destroy(LabClass $class)
    {
        $class->delete();

        // Clear cache kelas
        Cache::forget('active_classes');

        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}