<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DealService
{
    public function createDeal(array $data)
    {
        $id = DB::connection('tenant')->table('deals')->insertGetId(array_merge(
            $data,
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ));
        return DB::connection('tenant')->table('deals')->find($id);
    }
}
