<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDealsSummary()
    {
        return DB::connection('tenant')->table('deals')
            ->selectRaw('status, COUNT(*) as total, SUM(amount) as total_amount')
            ->groupBy('status')
            ->get();
    }
}
