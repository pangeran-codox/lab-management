<?php
namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::withCount(['schedules', 'bookings', 'assignments'])
            ->orderBy('name')
            ->get();
        return view('teachers.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:20|unique:teachers,phone',
        ], [
            'phone.unique' => 'Nomor HP sudah terdaftar untuk guru lain.',
        ]);

        // Generate token unik otomatis
        $lastId = Teacher::max('id') ?? 0;
        $token  = 'GRU-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        // Pastikan token unik
        while (Teacher::where('token', $token)->exists()) {
            $lastId++;
            $token = 'GRU-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        }

        Teacher::create([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'token'     => $token,
            'is_active' => true,
        ]);

        return back()->with('success', "Guru {$request->name} berhasil ditambahkan. Token: {$token}");
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $teacher->update([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->update(['is_active' => false]);
        return back()->with('success', 'Guru dinonaktifkan.');
    }

    // Cek token guru (untuk sistem tugas)
    public function verifyToken(Request $request)
    {
        $teacher = Teacher::where('token', strtoupper($request->token))
            ->where('is_active', true)
            ->first();

        if (!$teacher) {
            return response()->json(['valid' => false], 404);
        }

        return response()->json([
            'valid' => true,
            'id'    => $teacher->id,
            'name'  => $teacher->name,
        ]);
    }
}
