<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ContactService
{
    public function listContacts()
    {
        return DB::connection('tenant')->table('contacts')->get();
    }

    public function createContact(array $data)
    {
        $id = DB::connection('tenant')->table('contacts')->insertGetId([
            ...$data,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return DB::connection('tenant')->table('contacts')->find($id);
    }
}
