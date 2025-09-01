<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name','label'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public function users()
    {
        return $this->roles->flatMap(function($r){ return $r->users; })->unique('id');
    }
}
