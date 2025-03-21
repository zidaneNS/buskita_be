<?php

namespace App\Http\Middleware;

use App\Models\Schedule;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshScheduleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $schedules = Schedule::all();

        foreach ($schedules as $schedule) {
            $schedule_time = Carbon::parse($schedule->bus_schedule);
            $now = Carbon::parse(now());

            if ($now->diffInHours($schedule_time) >= 1) {
                $schedule->delete();
            }
        }

        return $next($request);
    }
}
