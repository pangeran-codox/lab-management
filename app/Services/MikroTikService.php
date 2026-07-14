<?php
namespace App\Services;

use App\Models\MikroTikDevice;
use Illuminate\Support\Facades\Http;

class MikroTikService
{
    /**
     * Ambil config lab dari DB (cached).
     * Format: [ 'lab7' => [ 'name', 'bot_lab_id', 'bot_url', ... ], ... ]
     */
    private function labConfig(): array
    {
        return MikroTikDevice::getLabMapCached();
    }

    // ──────────────────────────────────────────────────────────────────
    // Enable Internet
    // ──────────────────────────────────────────────────────────────────

    public function enableLab(string $labKey): array
    {
        $config = $this->labConfig()[$labKey] ?? null;
        if (!$config) {
            return ['success' => false, 'error' => 'Kontrol internet tidak tersedia untuk lab ini.', 'unsupported' => true];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->botHeaders($config))
                ->post("{$config['bot_url']}/api/lab/internet", [
                    'lab_id' => $config['bot_lab_id'],
                    'action' => 'on',
                ]);

            $data = $response->json();
            return [
                'success' => $data['success'] ?? false,
                'lab'     => $config['name'],
                'action'  => 'enabled',
                'message' => $data['message'] ?? '',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Disable Internet
    // ──────────────────────────────────────────────────────────────────

    public function disableLab(string $labKey): array
    {
        $config = $this->labConfig()[$labKey] ?? null;
        if (!$config) {
            return ['success' => false, 'error' => 'Kontrol internet tidak tersedia untuk lab ini.', 'unsupported' => true];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->botHeaders($config))
                ->post("{$config['bot_url']}/api/lab/internet", [
                    'lab_id' => $config['bot_lab_id'],
                    'action' => 'off',
                ]);

            $data = $response->json();
            return [
                'success' => $data['success'] ?? false,
                'lab'     => $config['name'],
                'action'  => 'disabled',
                'message' => $data['message'] ?? '',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Status
    // ──────────────────────────────────────────────────────────────────

    public function getLabStatus(string $labKey): array
    {
        $config = $this->labConfig()[$labKey] ?? null;
        if (!$config) {
            return [
                'success'     => false,
                'error'       => 'Kontrol internet tidak tersedia untuk lab ini.',
                'unsupported' => true,
                'internet'    => null,
            ];
        }

        try {
            $headers = $this->botHeaders($config);
            $botUrl  = $config['bot_url'];

            $statusResp  = Http::timeout(10)->withHeaders($headers)
                ->get("{$botUrl}/api/lab/status/{$config['bot_lab_id']}");
            $devicesResp = Http::timeout(10)->withHeaders($headers)
                ->get("{$botUrl}/api/lab/devices/{$config['bot_lab_id']}");

            $statusData  = $statusResp->json();
            $devicesData = $devicesResp->json();

            $natEnabled  = ($statusData['status'] ?? '') === 'online';
            $devices     = $devicesData['devices'] ?? [];
            $activeUsers = count(array_filter($devices, fn($d) => $d['active'] ?? false));

            return [
                'success'      => true,
                'lab_key'      => $labKey,
                'lab_name'     => $config['name'],
                'nat_enabled'  => $natEnabled,
                'status'       => $natEnabled ? 'online' : 'offline',
                'active_users' => $activeUsers,
                'devices'      => $devices,
                'network'      => $config['network'] ?? '',
            ];
        } catch (\Exception $e) {
            return [
                'success'      => false,
                'lab_key'      => $labKey,
                'lab_name'     => $config['name'],
                'nat_enabled'  => false,
                'status'       => 'error',
                'active_users' => 0,
                'devices'      => [],
                'error'        => $e->getMessage(),
            ];
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Misc
    // ──────────────────────────────────────────────────────────────────

    public function listNATRules(): array
    {
        // Ambil dari device pertama yang aktif
        $map = $this->labConfig();
        if (empty($map)) return [];

        $first = reset($map);
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->botHeaders($first))
                ->get("{$first['bot_url']}/api/nat/rules");
            return $response->json()['rules'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function debugNATRules(): array
    {
        return $this->listNATRules();
    }

    // ──────────────────────────────────────────────────────────────────
    // Helper: susun header Authorization untuk bot
    // ──────────────────────────────────────────────────────────────────

    private function botHeaders(array $config): array
    {
        if (!empty($config['bot_token'])) {
            return ['Authorization' => 'Bearer ' . $config['bot_token']];
        }
        return [];
    }
}
