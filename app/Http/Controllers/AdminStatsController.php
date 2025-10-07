<?php

namespace App\Http\Controllers;

use App\Http\Services\AdminDashboardStatsService;

class AdminStatsController extends Controller
{

    protected $adminStats;

    public function __construct(AdminDashboardStatsService $adminStats)
    {
        $this->adminStats = $adminStats;
    }

    //  get the stats for admin dashboard
    public function dashboardStats()
    {
        return $this->adminStats->showStats();
    }
}
