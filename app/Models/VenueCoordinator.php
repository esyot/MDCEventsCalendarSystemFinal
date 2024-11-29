<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueCoordinator extends Model
{
    use HasFactory;

    protected $guarded = [];

    // In VenueCoordinator.php
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id', 'id');
    }
}
