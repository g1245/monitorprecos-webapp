<?php

namespace App\Http\Controllers;

use App\Models\AwinWebhookLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AwinWebhookController extends Controller
{
    /**
     * Receive and log raw AWIN transaction webhook payloads.
     *
     * Validates a shared secret token before persisting the full body as JSON.
     */
    public function store(Request $request): JsonResponse
    {
        $secret = config('services.awin.webhook_secret');

        if ($secret && $request->query('secret') !== $secret) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        if (empty($payload)) {
            return response()->json(['message' => 'Empty payload'], 422);
        }

        AwinWebhookLog::create([
            'payload'   => $payload,
            'source_ip' => $request->ip(),
        ]);

        return response()->json(['message' => 'OK'], 200);
    }
}
