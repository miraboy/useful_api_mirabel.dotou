<?php

namespace App\Http\Controllers;

use App\Models\TimeSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeSessionController extends Controller
{
    /**
     * Start a new session
     */
    public function start(Request $request)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'start_time' => 'required|date',
        ]);

        $activeSession = TimeSession::where('user_id', $request->user()->id)
            ->whereNull('end_time')
            ->first();

        if ($activeSession) {
            return response()->json([
                'message' => 'You already have an active session. Please stop it before starting a new one.',
                'active_session' => $activeSession
            ], 400);
        }

        $session = TimeSession::create([
            'user_id' => $request->user()->id,
            'task_name' => $request->input('task_name'),
            'start_time' => $request->input('start_time'),
        ]);

        return response()->json([
            'message' => 'Session started successfully',
            'session' => $session
        ], 201);
    }

    /**
     * Stop an active session
     */
    public function stop(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:time_sessions,id',
            'end_time' => 'required|date',
        ]);

        $session = TimeSession::where('id', $request->input('session_id'))
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$session) {
            return response()->json([
                'message' => 'Session not found'
            ], 404);
        }

        if ($session->end_time) {
            return response()->json([
                'message' => 'This session is already stopped'
            ], 400);
        }

        $endTime = Carbon::parse($request->input('end_time'));
        $startTime = Carbon::parse($session->start_time);

        if ($endTime->lte($startTime)) {
            return response()->json([
                'message' => 'End time must be after start time'
            ], 400);
        }

        $duration = $startTime->diffInSeconds($endTime);

        $session->update([
            'end_time' => $endTime,
            'duration' => $duration,
        ]);

        return response()->json([
            'message' => 'Session stopped successfully',
            'session' => $session->fresh()
        ]);
    }

    /**
     * List all user sessions
     */
    public function index(Request $request)
    {
        $sessions = TimeSession::where('user_id', $request->user()->id)
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'sessions' => $sessions
        ]);
    }

    /**
     * Delete a session
     */
    public function destroy(Request $request, $id)
    {
        $session = TimeSession::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$session) {
            return response()->json([
                'message' => 'Session not found'
            ], 404);
        }

        $session->delete();

        return response()->json([
            'message' => 'Session deleted successfully'
        ]);
    }

    /**
     * Get analytics with converted duration based on period
     */
    public function analytics(Request $request)
    {
        $request->validate([
            'period' => 'required|in:day,month,year',
        ]);

        $period = $request->input('period');
        $userId = $request->user()->id;

        $sessions = TimeSession::where('user_id', $userId)
            ->whereNotNull('end_time')
            ->whereNotNull('duration')
            ->get();

        $totalDuration = $sessions->sum('duration');
        $averageDuration = $sessions->count() > 0 ? $sessions->avg('duration') : 0;

        // Convert duration based on period
        $conversionFactor = match($period) {
            'day' => 86400,     // seconds in a day
            'month' => 2592000, // seconds in 30 days
            'year' => 31536000, // seconds in 365 days
        };

        $convertedTotal = $totalDuration / $conversionFactor;
        $convertedAverage = $averageDuration / $conversionFactor;

        // Task breakdown
        $taskBreakdown = $sessions->groupBy('task_name')->map(function ($taskSessions, $taskName) use ($conversionFactor) {
            $taskDuration = $taskSessions->sum('duration');
            return [
                'task_name' => $taskName,
                'total_sessions' => $taskSessions->count(),
                'total_duration_seconds' => $taskDuration,
                'converted_duration' => round($taskDuration / $conversionFactor, 4),
            ];
        })->values();

        return response()->json([
            'period' => $period,
            'total_sessions' => $sessions->count(),
            'total_duration_seconds' => $totalDuration,
            'converted_total_duration' => round($convertedTotal, 4),
            'average_duration_seconds' => round($averageDuration, 2),
            'converted_average_duration' => round($convertedAverage, 4),
            'task_breakdown' => $taskBreakdown,
        ]);
    }
}
