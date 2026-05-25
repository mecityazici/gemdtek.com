<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HealthController extends Controller
{
    /**
     * GET /health  → 200 if all checks pass, 503 otherwise.
     * Designed for UptimeRobot / cron monitoring.
     *
     * Response body example:
     * {
     *   "status": "ok",
     *   "timestamp": "2026-05-25T08:00:00Z",
     *   "checks": {
     *     "database": { "status": "ok", "latency_ms": 3 },
     *     "cache":    { "status": "ok" },
     *     "storage":  { "status": "ok" }
     *   }
     * }
     */
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $healthy = collect($checks)->every(fn ($c) => $c['status'] === 'ok');

        return response()->json([
            'status' => $healthy ? 'ok' : 'fail',
            'timestamp' => now()->toIso8601String(),
            'app' => config('app.name'),
            'env' => config('app.env'),
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }

    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $latency = (int) round((microtime(true) - $start) * 1000);

            return ['status' => 'ok', 'latency_ms' => $latency];
        } catch (Throwable $e) {
            return ['status' => 'fail', 'error' => $this->safeMessage($e)];
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'health:'.now()->timestamp;
            Cache::put($key, 'ok', 10);
            $value = Cache::get($key);
            Cache::forget($key);

            return $value === 'ok'
                ? ['status' => 'ok']
                : ['status' => 'fail', 'error' => 'roundtrip mismatch'];
        } catch (Throwable $e) {
            return ['status' => 'fail', 'error' => $this->safeMessage($e)];
        }
    }

    private function checkStorage(): array
    {
        try {
            $disk = Storage::disk(config('filesystems.default', 'public'));
            $file = 'health/'.now()->timestamp.'.txt';
            $disk->put($file, 'ok');
            $exists = $disk->exists($file);
            $disk->delete($file);

            return $exists
                ? ['status' => 'ok']
                : ['status' => 'fail', 'error' => 'write failed'];
        } catch (Throwable $e) {
            return ['status' => 'fail', 'error' => $this->safeMessage($e)];
        }
    }

    private function safeMessage(Throwable $e): string
    {
        // Production'da raw error mesajı leak etmesin diye class adıyla yetin
        return config('app.debug') ? $e->getMessage() : class_basename($e);
    }
}
