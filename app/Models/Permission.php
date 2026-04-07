<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'divisi_id',
        'judul_report',
        'nama_report',
        'link_dashboard',
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }
}