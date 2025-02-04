<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserPermission;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $guarded = [];

    public function user_permissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }




    // Full name as an accessor
    public function getFullNameAttribute()
    {
        return $this->fname . ' ' . $this->lname;
    }

    public function venues()
    {
        return $this->hasManyThrough(Venue::class, VenueCoordinator::class, 'user_id', 'id', 'id', 'venue_id');
    }
}
