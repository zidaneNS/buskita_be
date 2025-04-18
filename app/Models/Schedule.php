<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Schedule extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'time',
        'bus_id',
        'route_id',
        'closed'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }
    
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }
}
