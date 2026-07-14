<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────

    public function index()
    {
        $s = Setting::getMany([
            // Identitas
            Setting::SITE_NAME,
            Setting::SITE_LOGO,
            Setting::SITE_HEAD_NAME,
            Setting::SITE_ADDRESS,
            Setting::SITE_PHONE,
            // WhatsApp
            Setting::WA_NOTIFY_BOOKING,
            Setting::WA_NOTIFY_LAB,
            Setting::WA_ADMIN_NUMBER,
            // Booking
            Setting::BOOKING_OPEN,
            Setting::BOOKING_MAX_DAYS,
            // Lab Control
            Setting::LAB_SESSION_AUTO,
            Setting::LAB_TOKEN_GRACE,
            // Laporan
            Setting::REPORT_FOOTER,
        ]);

        // Defaults untuk field baru yang belum punya nilai
        $s[Setting::SITE_NAME]         ??= config('app.name', 'Lab Management');
        $s[Setting::WA_NOTIFY_BOOKING] ??= '1';
        $s[Setting::WA_NOTIFY_LAB]     ??= '1';
        $s[Setting::BOOKING_OPEN]      ??= '1';
        $s[Setting::BOOKING_MAX_DAYS]  ??= '30';
        $s[Setting::LAB_SESSION_AUTO]  ??= '1';
        $s[Setting::LAB_TOKEN_GRACE]   ??= '10';

        return view('admin.settings', compact('s'));
    }

    // ──────────────────────────────────────────────────────────
    // SAVE — Identitas Sekolah / Lab
    // ──────────────────────────────────────────────────────────

    public function saveIdentitas(Request $request)
    {
        $request->validate([
            'site_name'      => 'required|string|max:100',
            'site_head_name' => 'nullable|string|max:100',
            'site_address'   => 'nullable|string|max:255',
            'site_phone'     => 'nullable|string|max:30',
        ]);

        Setting::setMany([
            Setting::SITE_NAME      => $request->site_name,
            Setting::SITE_HEAD_NAME => $request->site_head_name ?? '',
            Setting::SITE_ADDRESS   => $request->site_address   ?? '',
            Setting::SITE_PHONE     => $request->site_phone     ?? '',
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Identitas sekolah berhasil disimpan.');
    }

    // ──────────────────────────────────────────────────────────
    // SAVE — Upload Logo
    // ──────────────────────────────────────────────────────────

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get(Setting::SITE_LOGO);
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            Setting::set(Setting::SITE_LOGO, $path);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Logo berhasil diupload.');
    }

    // ──────────────────────────────────────────────────────────
    // SAVE — Notifikasi WhatsApp
    // ──────────────────────────────────────────────────────────

    public function saveWa(Request $request)
    {
        $request->validate([
            'wa_admin_number' => 'nullable|string|max:20|regex:/^[0-9+\s\-]+$/',
        ]);

        Setting::setMany([
            Setting::WA_NOTIFY_BOOKING => $request->has('wa_notify_booking') ? '1' : '0',
            Setting::WA_NOTIFY_LAB     => $request->has('wa_notify_lab')     ? '1' : '0',
            Setting::WA_ADMIN_NUMBER   => $request->wa_admin_number ?? '',
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan WhatsApp berhasil disimpan.');
    }

    // ──────────────────────────────────────────────────────────
    // SAVE — Booking
    // ──────────────────────────────────────────────────────────

    public function saveBooking(Request $request)
    {
        $request->validate([
            'booking_max_days' => 'required|integer|min:1|max:365',
        ]);

        Setting::setMany([
            Setting::BOOKING_OPEN     => $request->has('booking_open') ? '1' : '0',
            Setting::BOOKING_MAX_DAYS => (string) $request->booking_max_days,
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan booking berhasil disimpan.');
    }

    // ──────────────────────────────────────────────────────────
    // SAVE — Lab Control / Sesi
    // ──────────────────────────────────────────────────────────

    public function saveLabControl(Request $request)
    {
        $request->validate([
            'lab_token_grace' => 'required|integer|min:0|max:60',
        ]);

        Setting::setMany([
            Setting::LAB_SESSION_AUTO => $request->has('lab_session_auto') ? '1' : '0',
            Setting::LAB_TOKEN_GRACE  => (string) $request->lab_token_grace,
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan Lab Control berhasil disimpan.');
    }

    // ──────────────────────────────────────────────────────────
    // SAVE — Laporan
    // ──────────────────────────────────────────────────────────

    public function saveLaporan(Request $request)
    {
        $request->validate([
            'report_footer' => 'nullable|string|max:500',
        ]);

        Setting::set(Setting::REPORT_FOOTER, $request->report_footer ?? '');

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan laporan berhasil disimpan.');
    }

    // ──────────────────────────────────────────────────────────
    // SAVE — Ukuran Font Kop
    // ──────────────────────────────────────────────────────────

    public function saveKopSize(Request $request)
    {
        $request->validate([
            'kop_name_size'    => 'required|integer|min:10|max:48',
            'kop_address_size' => 'required|integer|min:8|max:32',
            'kop_phone_size'   => 'required|integer|min:8|max:32',
        ]);

        Setting::setMany([
            Setting::KOP_NAME_SIZE    => (string) $request->kop_name_size,
            Setting::KOP_ADDRESS_SIZE => (string) $request->kop_address_size,
            Setting::KOP_PHONE_SIZE   => (string) $request->kop_phone_size,
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteLogo()
    {
        $logo = Setting::get(Setting::SITE_LOGO);
        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }
        Setting::set(Setting::SITE_LOGO, null);

        return redirect()->route('settings.index')
            ->with('success', 'Logo berhasil dihapus.');
    }
}
