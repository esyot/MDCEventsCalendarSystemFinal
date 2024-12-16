<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventJunction extends Model
{
    protected $guarded = [];

    public function eventDepartments()
    {
        return $this->hasMany(EventDepartment::class);
    }
}
