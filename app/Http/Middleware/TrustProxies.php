<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Proxy yang dipercaya.
     * - null  = tidak ada proxy dipercaya (default, aman untuk akses langsung)
     * - '*'   = percaya semua proxy (JANGAN dipakai di production tanpa alasan)
     * - ['ip'] = hanya percaya IP proxy tertentu
     *
     * Karena app ini diakses langsung (Laragon/lokal), set null.
     * Jika nanti di-deploy di belakang Nginx/load balancer, isi dengan IP proxy-nya.
     */
    protected $proxies = '*';

    /**
     * Header yang dipakai untuk mendeteksi proxy.
     * Hanya aktifkan yang benar-benar dibutuhkan.
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO;
}
