<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Prometheus\Facades\Prometheus;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPrometheusCollectors();
    }

    protected function registerPrometheusCollectors(): void
    {
        Prometheus::addGauge('app_requests_total')
            ->helpText('Total number of HTTP requests')
            ->name('app_requests_total');

        Prometheus::addGauge('app_uptime_seconds')
            ->helpText('Application uptime in seconds')
            ->value(fn() => time() - filemtime(base_path('composer.json')));
    }
}
