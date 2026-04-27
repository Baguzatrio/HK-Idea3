<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission as SpatiePermission;

#[Fillable(['name', 'guard_name', 'divisi_id', 'judul_report', 'nama_report', 'link_dashboard'])]
class Permission extends SpatiePermission
{

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }
}