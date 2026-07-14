<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MikroTikDevice;

class MikroTikDeviceSeeder extends Seeder
{
    /**
     * Migrate data lama dari MikroTikService::LAB_CONFIG ke DB.
     * Jalankan sekali: php artisan db:seed --class=MikroTikDeviceSeeder
     */
    public function run(): void
    {
        // Cek sudah ada data, skip kalau sudah
        if (MikroTikDevice::count() > 0) {
            $this->command->info('MikroTik devices already seeded, skipping.');
            return;
        }

        $botUrl   = env('BOT_URL', 'http://170.1.0.46:5000');
        $botToken = env('BOT_TOKEN', '');

        // Device 1 — handle lab7 & lab8
        $device = MikroTikDevice::create([
            'name'      => 'MikroTik Utama',
            'host'      => env('MIKROTIK_HOST', '103.182.235.42'),
            'port'      => (int) env('MIKROTIK_PORT', 2112),
            'username'  => env('MIKROTIK_USER', 'admin'),
            'password'  => env('MIKROTIK_PASS', 'm41k3l'),
            'bot_url'   => $botUrl,
            'bot_token' => $botToken,
            'is_active' => true,
        ]);

        $device->labs()->createMany([
            [
                'lab_key'     => 'lab7',
                'resource_id' => 1,
                'bot_lab_id'  => 1,
                'nat_comment' => 'lab 7',
                'interface'   => 'lab 7',
                'dhcp_server' => 'dhcp2',
                'network'     => '192.168.70.0/24',
                'vlan_id'     => 77,
                'is_active'   => true,
            ],
            [
                'lab_key'     => 'lab8',
                'resource_id' => 2,
                'bot_lab_id'  => 2,
                'nat_comment' => 'lab 8',
                'interface'   => 'lab 8',
                'dhcp_server' => 'dhcp3',
                'network'     => '192.168.80.0/24',
                'vlan_id'     => 88,
                'is_active'   => true,
            ],
        ]);

        $this->command->info('MikroTik device seeded with lab7 & lab8.');
    }
}
