<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'db_name',
        'db_user',
        'db_pass',
        'status',
    ];
    protected static function booted()
    {
        static::creating(function (Tenant $tenant) {
            $tenant->slug = Str::slug($tenant->name);
        });
    }
}
