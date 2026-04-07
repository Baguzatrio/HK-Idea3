<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Permission;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';

    protected $fillable = [
        'kode',
        'nama',
        'lantai',
        'logo',
        'url',
        'no_urut',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    public function permissions(): HasMany
{
    return $this->hasMany(Permission::class);
}
}