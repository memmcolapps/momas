<?php

namespace App\Http\Controllers;

use App\Models\Logger as AppLogger;
use App\Services\StandardResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogController extends Controller
{
    /**
     * Get all logs in a well-structured format
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllLogs(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 50);
            $page = $request->input('page', 1);

            // Get logs from database (most recent first)
            $logs = AppLogger::orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $formattedLogs = $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'level' => strtoupper($log->level),
                    'message' => $log->message,
                    'context' => $log->context,
                    'created_at' => $log->created_at->toIso8601String(),
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return StandardResponse::success(code: 200, message: 'Logs retrieved successfully', data: [
                'logs' => $formattedLogs,
                'meta' => [
                    'current_page' => $logs->currentPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                    'last_page' => $logs->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return StandardResponse::error(code: 500, message: 'Failed to retrieve logs', debug: [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Query and filter logs
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryLogs(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'level' => 'nullable|string|in:info,warning,error,debug',
                'message' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'sort_by' => 'nullable|string|in:created_at,level,message',
                'sort_order' => 'nullable|string|in:asc,desc',
            ]);

            if ($validator->fails()) {
                return StandardResponse::error(code: 422, message: 'Validation failed', data: [
                    'errors' => $validator->errors(),
                ]);
            }

            $level = $request->input('level');
            $message = $request->input('message');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $perPage = $request->input('per_page', 50);
            $page = $request->input('page', 1);
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Build query
            $query = AppLogger::query();

            // Filter by level
            if ($level) {
                $query->where('level', strtolower($level));
            }

            // Filter by message (partial match)
            if ($message) {
                $query->where('message', 'like', "%{$message}%");
            }

            // Filter by date range
            if ($startDate) {
                $query->where('created_at', '>=', Carbon::parse($startDate));
            }

            if ($endDate) {
                $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
            }

            // Apply sorting
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results
            $logs = $query->paginate($perPage, ['*'], 'page', $page);

            $formattedLogs = $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'level' => strtoupper($log->level),
                    'message' => $log->message,
                    'context' => $log->context,
                    'created_at' => $log->created_at->toIso8601String(),
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            });

            // Calculate summary statistics
            $summary = [
                'total' => $logs->total(),
                'info_count' => AppLogger::where('level', 'info')->when($level, function($q) use ($level) {
                    return $q->where('level', strtolower($level));
                })->when($startDate, function($q) use ($startDate) {
                    return $q->where('created_at', '>=', Carbon::parse($startDate));
                })->when($endDate, function($q) use ($endDate) {
                    return $q->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
                })->count(),
                'warning_count' => AppLogger::where('level', 'warning')->when($level, function($q) use ($level) {
                    return $q->where('level', strtolower($level));
                })->when($startDate, function($q) use ($startDate) {
                    return $q->where('created_at', '>=', Carbon::parse($startDate));
                })->when($endDate, function($q) use ($endDate) {
                    return $q->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
                })->count(),
                'error_count' => AppLogger::where('level', 'error')->when($level, function($q) use ($level) {
                    return $q->where('level', strtolower($level));
                })->when($startDate, function($q) use ($startDate) {
                    return $q->where('created_at', '>=', Carbon::parse($startDate));
                })->when($endDate, function($q) use ($endDate) {
                    return $q->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
                })->count(),
            ];

            return StandardResponse::success(code: 200, message: 'Logs queried successfully', data: [
                'logs' => $formattedLogs,
                'summary' => $summary,
                'filters' => [
                    'level' => $level,
                    'message' => $message,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                ],
                'meta' => [
                    'current_page' => $logs->currentPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                    'last_page' => $logs->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return StandardResponse::error(code: 500, message: 'Failed to query logs', debug: [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get log statistics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $query = AppLogger::query();

            if ($startDate) {
                $query->where('created_at', '>=', Carbon::parse($startDate));
            }

            if ($endDate) {
                $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
            }

            $total = $query->count();

            $byLevel = $query->clone()
                ->select('level')
                ->groupBy('level')
                ->selectRaw('count(*) as count')
                ->get()
                ->pluck('count', 'level')
                ->toArray();

            // Get logs per day for the last 30 days
            $dailyLogs = AppLogger::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();

            return StandardResponse::success(code: 200, message: 'Statistics retrieved successfully', data: [
                'total_logs' => $total,
                'by_level' => [
                    'info' => $byLevel['info'] ?? 0,
                    'warning' => $byLevel['warning'] ?? 0,
                    'error' => $byLevel['error'] ?? 0,
                    'debug' => $byLevel['debug'] ?? 0,
                ],
                'daily_logs' => $dailyLogs,
            ]);
        } catch (\Exception $e) {
            return StandardResponse::error(code: 500, message: 'Failed to retrieve statistics', debug: [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get a single log by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLog($id)
    {
        try {
            $log = AppLogger::find($id);

            if (!$log) {
                return StandardResponse::error(code: 404, message: 'Log not found');
            }

            return StandardResponse::success(code: 200, message: 'Log retrieved successfully', data: [
                'id' => $log->id,
                'level' => strtoupper($log->level),
                'message' => $log->message,
                'context' => $log->context,
                'created_at' => $log->created_at->toIso8601String(),
                'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return StandardResponse::error(code: 500, message: 'Failed to retrieve log', debug: [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete a log by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteLog($id)
    {
        return StandardResponse::error(403, 'You cannot proceed with this action');
        try {
            $log = AppLogger::find($id);

            if (!$log) {
                return StandardResponse::error(code: 404, message: 'Log not found');
            }

            $log->delete();

            return StandardResponse::success(code: 200, message: 'Log deleted successfully');
        } catch (\Exception $e) {
            return StandardResponse::error(code: 500, message: 'Failed to delete log', debug: [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Clear all logs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearLogs()
    {
        return StandardResponse::error(403, 'You cannot proceed with this action');
        try {
            AppLogger::truncate();

            return StandardResponse::success(code: 200, message: 'All logs cleared successfully');
        } catch (\Exception $e) {
            return StandardResponse::error(code: 500, message: 'Failed to clear logs', debug: [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
