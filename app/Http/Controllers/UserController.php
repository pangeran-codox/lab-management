<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resource;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Hanya admin/super admin yang bisa akses
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasFullAccess()) {
                abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::with('resources')->orderBy('role')->orderBy('full_name')->get();
        
        // Preload all resources for metadata-based assignments AND active resources
        $allResources = Resource::all()->keyBy('id');
        $resources = $allResources->filter(fn($r) => $r->status === 'active')->sortBy('name')->values();
        
        // Attach assigned labs to each user
        $users->each(function($user) use ($allResources) {
            $labIds = $user->getAssignedLabIds();
            $user->assigned_labs = $allResources->only($labIds)->sortBy('name')->values();
        });
        
        return view('users.index', compact('users', 'resources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'role' => 'required|in:admin,operator,teknisi,guru',
            'password' => 'required|string|min:8',
            'weekly_quota' => 'nullable|integer|min:1',
        ]);

        $validated['password_hash'] = Hash::make($validated['password']);
        $validated['is_active'] = true;
        $validated['weekly_quota'] = $request->weekly_quota ?? 5;

        $user = User::create($validated);

        // Assign lab jika ada
        if ($request->has('resource_ids')) {
            $user->resources()->sync($request->resource_ids);
        }

        return back()->with('success', 'User "' . $user->full_name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
            'role' => 'required|in:admin,operator,teknisi,guru',
            'password' => 'nullable|string|min:8',
            'weekly_quota' => 'nullable|integer|min:1',
        ]);

        if (!empty($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
        }

        $validated['weekly_quota'] = $request->weekly_quota ?? 5;

        $user->update($validated);

        // Sync lab assignment
        $user->resources()->sync($request->resource_ids ?? []);

        return back()->with('success', 'User "' . $user->full_name . '" berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
