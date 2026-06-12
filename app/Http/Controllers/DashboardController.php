<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $authUser = auth()->user();
        $allowedResources = null;
        
        if (!in_array($authUser->role, ['admin', 'operator'])) {
            $allowedResources = $authUser->metadata['allowed_resources'] ?? [];
        }

        $data = $this->dashboardService->getDashboardData($allowedResources);

        return view('dashboard', $data);
    }
}
