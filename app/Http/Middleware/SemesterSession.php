<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;
use App\Models\SemesterSession as SemesterSessionModel;

class SemesterSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Skip MIS users - they can access all data
            if ($user->role && $user->role->name === 'MIS') {
                return $next($request);
            }

            // Get or set active semester for non-MIS users
            $activeSemester = Semester::where('is_active', true)->first();
            
            if ($activeSemester) {
                // Update user's current semester if not set or different
                if (!$user->current_semester_id || $user->current_semester_id !== $activeSemester->id) {
                    $user->update(['current_semester_id' => $activeSemester->id]);
                }

                // Create or update semester session
                SemesterSessionModel::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'semester_id' => $activeSemester->id
                    ],
                    [
                        'updated_at' => now()
                    ]
                );
            }
        }

        return $next($request);
    }
}
