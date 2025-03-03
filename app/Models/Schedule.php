<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Schedule extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'bus_schedule',
        'number',
        'capacity',
        'ikut_berangkat',
        'ikut_pulang',
        'berangkat',
        'pulang',
        'pp',
        'pp_khusus'
    ];

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
