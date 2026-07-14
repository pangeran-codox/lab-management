<?php

namespace App\Http\Controllers;

use App\Models\MikroTikDevice;
use App\Models\MikroTikLab;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MikroTikSettingController extends Controller
{
    // ──────────────────────────────────────────────────────────────────
    // INDEX — daftar semua device + lab-nya
    // ──────────────────────────────────────────────────────────────────

    public function index()
    {
        $devices   = MikroTikDevice::with(['labs.resource.users' => function($q) {
                            $q->where('role', 'teknisi');
                        }])->orderBy('id')->get();

        $resources = Resource::whereNull('deleted_at')
                        ->where('status', 'active')
                        ->orderBy('name')
                        ->get();

        // Semua user teknisi (untuk dropdown assign)
        $teknisiList = \App\Models\User::where('role', 'teknisi')
                        ->where('is_active', true)
                        ->orderBy('full_name')
                        ->get();

        return view('admin.mikrotik-settings', compact('devices', 'resources', 'teknisiList'));
    }

    // ──────────────────────────────────────────────────────────────────
    // STORE DEVICE
    // ──────────────────────────────────────────────────────────────────

    public function storeDevice(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'host'      => 'required|string|max:100',
            'port'      => 'required|integer|min:1|max:65535',
            'username'  => 'required|string|max:100',
            'password'  => 'required|string|max:255',
            'bot_url'   => 'required|url|max:255',
            'bot_token' => 'nullable|string|max:255',
            'notes'     => 'nullable|string|max:500',
        ]);

        $data['is_active'] = true;

        MikroTikDevice::create($data);
        MikroTikDevice::clearLabMapCache();

        return redirect()->route('mikrotik.settings.index')
            ->with('success', "Perangkat \"{$data['name']}\" berhasil ditambahkan.");
    }

    // ──────────────────────────────────────────────────────────────────
    // UPDATE DEVICE
    // ──────────────────────────────────────────────────────────────────

    public function updateDevice(Request $request, MikroTikDevice $device)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'host'      => 'required|string|max:100',
            'port'      => 'required|integer|min:1|max:65535',
            'username'  => 'required|string|max:100',
            'password'  => 'nullable|string|max:255', // nullable: kosong = tidak ubah
            'bot_url'   => 'required|url|max:255',
            'bot_token' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes'     => 'nullable|string|max:500',
        ]);

        // Jika password dikosongkan, jangan overwrite
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['is_active'] = $request->has('is_active') ? true : false;

        $device->update($data);
        MikroTikDevice::clearLabMapCache();

        return redirect()->route('mikrotik.settings.index')
            ->with('success', "Perangkat \"{$device->name}\" berhasil diperbarui.");
    }

    // ──────────────────────────────────────────────────────────────────
    // DELETE DEVICE
    // ──────────────────────────────────────────────────────────────────

    public function destroyDevice(MikroTikDevice $device)
    {
        $name = $device->name;
        // Cascade delete labs via FK
        $device->delete();
        MikroTikDevice::clearLabMapCache();

        return redirect()->route('mikrotik.settings.index')
            ->with('success', "Perangkat \"{$name}\" berhasil dihapus.");
    }

    // ──────────────────────────────────────────────────────────────────
    // STORE LAB (tambah lab ke device)
    // ──────────────────────────────────────────────────────────────────

    public function storeLab(Request $request, MikroTikDevice $device)
    {
        // Validasi max 2 lab per device
        if ($device->labs()->count() >= 2) {
            return redirect()->route('mikrotik.settings.index')
                ->with('error', "Perangkat \"{$device->name}\" sudah menangani 2 lab (maksimum).");
        }

        $data = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'lab_key'     => [
                'required', 'string', 'max:30', 'alpha_dash',
                // lab_key harus unik di seluruh tabel
                \Illuminate\Validation\Rule::unique('mikrotik_labs', 'lab_key'),
            ],
            'bot_lab_id'  => 'required|integer|min:1',
            'nat_comment' => 'nullable|string|max:100',
            'interface'   => 'nullable|string|max:100',
            'dhcp_server' => 'nullable|string|max:100',
            'network'     => 'nullable|string|max:50',
            'vlan_id'     => 'nullable|integer|min:1|max:4094',
        ]);

        $data['mikrotik_device_id'] = $device->id;
        $data['is_active']          = true;
        $data['lab_key']            = strtolower(trim($data['lab_key']));

        MikroTikLab::create($data);
        MikroTikDevice::clearLabMapCache();

        return redirect()->route('mikrotik.settings.index')
            ->with('success', "Lab \"{$data['lab_key']}\" berhasil ditambahkan ke {$device->name}.");
    }

    // ──────────────────────────────────────────────────────────────────
    // UPDATE LAB
    // ──────────────────────────────────────────────────────────────────

    public function updateLab(Request $request, MikroTikLab $lab)
    {
        $data = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'lab_key'     => [
                'required', 'string', 'max:30', 'alpha_dash',
                \Illuminate\Validation\Rule::unique('mikrotik_labs', 'lab_key')->ignore($lab->id),
            ],
            'bot_lab_id'  => 'required|integer|min:1',
            'nat_comment' => 'nullable|string|max:100',
            'interface'   => 'nullable|string|max:100',
            'dhcp_server' => 'nullable|string|max:100',
            'network'     => 'nullable|string|max:50',
            'vlan_id'     => 'nullable|integer|min:1|max:4094',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['lab_key']   = strtolower(trim($data['lab_key']));

        $lab->update($data);
        MikroTikDevice::clearLabMapCache();

        return redirect()->route('mikrotik.settings.index')
            ->with('success', "Lab \"{$lab->lab_key}\" berhasil diperbarui.");
    }

    // ──────────────────────────────────────────────────────────────────
    // DELETE LAB
    // ──────────────────────────────────────────────────────────────────

    public function destroyLab(MikroTikLab $lab)
    {
        $key = $lab->lab_key;
        $lab->delete();
        MikroTikDevice::clearLabMapCache();

        return redirect()->route('mikrotik.settings.index')
            ->with('success', "Lab \"{$key}\" berhasil dihapus.");
    }

    // ──────────────────────────────────────────────────────────────────
    // ASSIGN TEKNISI ke lab
    // ──────────────────────────────────────────────────────────────────

    public function assignTeknisi(Request $request, MikroTikLab $lab)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        // Pastikan role teknisi
        if ($user->role !== 'teknisi') {
            return back()->with('error', "{$user->full_name} bukan teknisi.");
        }

        // Cek maks 2 lab per teknisi
        $labCount = $user->resources()->count();
        if ($labCount >= 2) {
            return back()->with('error', "{$user->full_name} sudah di-assign ke {$labCount} lab (maksimum 2).");
        }

        // Cek apakah sudah di-assign ke lab ini
        if ($user->resources()->where('resource_id', $lab->resource_id)->exists()) {
            return back()->with('error', "{$user->full_name} sudah di-assign ke lab ini.");
        }

        $user->resources()->attach($lab->resource_id);

        return back()->with('success', "{$user->full_name} berhasil di-assign ke {$lab->resource->name}.");
    }

    // ──────────────────────────────────────────────────────────────────
    // UNASSIGN TEKNISI dari lab
    // ──────────────────────────────────────────────────────────────────

    public function unassignTeknisi(Request $request, MikroTikLab $lab)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $user->resources()->detach($lab->resource_id);

        return back()->with('success', "{$user->full_name} berhasil dilepas dari {$lab->resource->name}.");
    }

    public function testConnection(MikroTikDevice $device)
    {
        try {
            $headers = [];
            if ($device->bot_token) {
                $headers['Authorization'] = 'Bearer ' . $device->bot_token;
            }

            $response = Http::timeout(5)
                ->withHeaders($headers)
                ->get(rtrim($device->bot_url, '/') . '/health');

            if ($response->successful()) {
                return back()->with('success', "✅ Koneksi ke bot \"{$device->name}\" berhasil. Response: " . $response->status());
            }

            return back()->with('error', "⚠️ Bot merespons dengan status {$response->status()}.");
        } catch (\Exception $e) {
            Log::warning("MikroTik bot test failed for device #{$device->id}: " . $e->getMessage());
            return back()->with('error', "❌ Gagal terhubung ke bot \"{$device->name}\": " . $e->getMessage());
        }
    }
}
