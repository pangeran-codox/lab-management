<?php
namespace App\Http\Controllers;

use App\Models\LabSession;
use App\Services\MikroTikService;
use App\Services\LabControlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LabControlController extends Controller
{
    public function __construct(
        private LabControlService $labControl
    ) {}

    public function control(Request $request, string $token)
    {
        $token = strtoupper(trim($token));

        $session = LabSession::where('token', $token)
            ->where('is_active', true)
            ->whereNull('invalidated_at')
            ->where('session_end', '>', now())
            ->where('session_start', '<=', now()->addMinutes(5))
            ->first();

        if (!$session) {
            return view('lab-control.invalid', [
                'message' => 'Link tidak valid, sudah expired, atau sesi belum dimulai.'
            ]);
        }

        $session->markAsUsed();
        $labName = LabSession::LAB_MAP[$session->lab_key]['name'] ?? $session->lab_key;

        return view('lab-control.control', compact('session', 'labName', 'token'));
    }

    public function status(Request $request, string $token)
    {
        $session = $this->findSession($token);
        if (!$session) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        try {
            $mikrotik = new MikroTikService();
            $status = $mikrotik->getLabStatus($session->lab_key);
            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function toggleInternet(Request $request, string $token)
    {
        $session = $this->findSession($token);
        if (!$session) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $request->validate(['action' => 'required|in:on,off']);

        try {
            $mikrotik = new MikroTikService();
            $result = $request->action === 'on'
                ? $mikrotik->enableLab($session->lab_key)
                : $mikrotik->disableLab($session->lab_key);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request, string $token)
    {
        $session = $this->findSession($token);
        if ($session) {
            $session->invalidate('logout');
        }

        return view('lab-control.invalid', [
            'message' => 'Sesi telah diakhiri. Link ini tidak dapat digunakan lagi.'
        ]);
    }

    public function generateToken(Request $request)
    {
        $request->validate([
            'lab_key'       => 'required|in:lab7,lab8',
            'teacher_name'  => 'required|string|max:100',
            'teacher_phone' => 'nullable|string|max:20',
            'duration'      => 'required|integer|min:30|max:480',
        ]);

        $session = LabSession::create([
            'token'         => LabSession::generateToken(),
            'lab_key'       => $request->lab_key,
            'resource_id'   => LabSession::LAB_MAP[$request->lab_key]['resource_id'],
            'source_type'   => 'manual',
            'teacher_name'  => $request->teacher_name,
            'teacher_phone' => $request->teacher_phone,
            'session_start' => now(),
            'session_end'   => now()->addMinutes($request->duration),
        ]);

        $this->labControl->sendWebhook($session);

        return back()->with('success', "Token {$session->token} berhasil dibuat · Link: " . route('lab.control', $session->token));
    }

    // Public wrapper untuk dipakai dari controller lain
    public function sendWebhookPublic(LabSession $session): void
    {
        $this->labControl->sendWebhook($session);
    }

    private function findSession(string $token): ?LabSession
    {
        return LabSession::where('token', strtoupper(trim($token)))
            ->where('is_active', true)
            ->whereNull('invalidated_at')
            ->where('session_end', '>', now())
            ->first();
    }
}