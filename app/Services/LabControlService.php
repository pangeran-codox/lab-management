<?php

namespace App\Services;

use App\Models\LabSession;
use App\Models\Teacher;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LabControlService
{
    public function generateFromBooking($booking): ?LabSession
    {
        $labKey = $this->getLabKeyByResourceId($booking->resource_id);
        if (!$labKey) return null;

        $start = $booking->booking_date->setTimeFrom(
            Carbon::parse($booking->timeSlot->start_time ?? '07:00')
        );
        $end = $booking->booking_date->setTimeFrom(
            Carbon::parse($booking->timeSlot->end_time ?? '09:00')
        );

        $teacher = Teacher::whereRaw('LOWER(name) = ?', [strtolower($booking->teacher_name)])->first();

        $existingSession = LabSession::where('source_type', 'booking')
            ->where('resource_id', $booking->resource_id)
            ->whereDate('session_start', $booking->booking_date)
            ->where('teacher_name', $booking->teacher_name)
            ->where('is_active', true)
            ->first();

        if ($existingSession) {
            if ($end > $existingSession->session_end) {
                $existingSession->update(['session_end' => $end]);
            }
            return $existingSession;
        }

        return LabSession::create([
            'token'         => LabSession::generateToken(),
            'lab_key'       => $labKey,
            'resource_id'   => $booking->resource_id,
            'source_type'   => 'booking',
            'source_id'     => $booking->id,
            'teacher_name'  => $booking->teacher_name,
            'teacher_phone' => $teacher->phone ?? null,
            'session_start' => $start,
            'session_end'   => $end,
        ]);
    }

    public function generateFromSchedule($schedule): ?LabSession
    {
        $labKey = $this->getLabKeyByResourceId($schedule->resource_id);
        if (!$labKey) return null;

        $today = now()->toDateString();
        $start = Carbon::parse("$today {$schedule->start_time}");
        $end   = Carbon::parse("$today {$schedule->end_time}");

        $teacher = Teacher::where('name', $schedule->teacher_name)->first();

        $existsBySource = LabSession::where('source_type', 'schedule')
            ->where('source_id', $schedule->id)
            ->whereDate('session_start', $today)
            ->exists();
        if ($existsBySource) return null;

        $existingSession = LabSession::where('source_type', 'schedule')
            ->where('resource_id', $schedule->resource_id)
            ->whereDate('session_start', $today)
            ->where('teacher_name', $schedule->teacher_name)
            ->where('is_active', true)
            ->first();

        if ($existingSession) {
            if ($end > $existingSession->session_end) {
                $existingSession->update(['session_end' => $end]);
            }
            return $existingSession;
        }

        return LabSession::create([
            'token'         => LabSession::generateToken(),
            'lab_key'       => $labKey,
            'resource_id'   => $schedule->resource_id,
            'source_type'   => 'schedule',
            'source_id'     => $schedule->id,
            'teacher_name'  => $schedule->teacher_name ?? 'Guru',
            'teacher_phone' => $teacher->phone ?? null,
            'session_start' => $start,
            'session_end'   => $end,
        ]);
    }

    public function sendWebhook(LabSession $session): void
    {
        // Cek toggle notifikasi WA Lab Control dari Settings
        if (!\App\Models\Setting::isEnabled(\App\Models\Setting::WA_NOTIFY_LAB)) {
            return;
        }

        // Ambil bot_url & bot_token dari device yang handle lab ini (bukan dari .env global)
        $labMap    = \App\Models\MikroTikDevice::getLabMapCached();
        $labConfig = $labMap[$session->lab_key] ?? null;

        if ($labConfig) {
            $botUrl    = rtrim($labConfig['bot_url'], '/');
            $botToken  = $labConfig['bot_token'] ?? null;
            $webhookUrl = $botUrl . '/api/webhook/lab-session';
        } else {
            // Fallback ke config lama jika lab_key belum ada di DB
            $webhookUrl = config('mikrotik.webhook');
            $botToken   = config('mikrotik.bot_token');
        }

        if (!$webhookUrl) return;

        try {
            $headers = [];
            if ($botToken) {
                $headers['Authorization'] = 'Bearer ' . $botToken;
            }

            Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders($headers)
                ->post($webhookUrl, [
                    'event'         => 'lab_session_created',
                    'token'         => $session->token,
                    'lab_key'       => $session->lab_key,
                    'lab_name'      => $session->lab_name,
                    'teacher_name'  => $session->teacher_name,
                    'teacher_phone' => $session->teacher_phone,
                    'session_start' => $session->session_start->format('d/m/Y H:i'),
                    'session_end'   => $session->session_end->format('d/m/Y H:i'),
                    'link'          => route('lab.control', $session->token),
                ]);
        } catch (\Exception $e) {
            Log::warning('sendWebhook failed for lab_key=' . $session->lab_key . ': ' . $e->getMessage());
        }
    }

    private function getLabKeyByResourceId(int $resourceId): ?string
    {
        foreach (\App\Models\MikroTikDevice::getLabMapCached() as $key => $config) {
            if ($config['resource_id'] == $resourceId) {
                return $key;
            }
        }
        return null;
    }
}
