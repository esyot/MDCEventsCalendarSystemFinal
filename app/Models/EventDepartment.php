<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventDepartment extends Model
{
    protected $guarded = [];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }


}
