<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCreditScore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null) {
            $latest_update = $user->updated_at;
            $now = Carbon::now();
    
            if ($now->diffInDays($latest_update) < -1 && $user->credit_score < 15) {
                $user->update([
                    'credit_score' => $user->credit_score + 1
                ]);
            }
        }

        return $next($request);
    }
}
