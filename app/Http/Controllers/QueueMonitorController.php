<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;

use App\Services\QueueMonitorService;

class QueueMonitorController extends Controller
{
    protected $monitorService;

    public function __construct(QueueMonitorService $monitorService)
    {
        $this->monitorService = $monitorService;
    }

    public function index()
    {
        $stats = $this->monitorService->getMetrics();
        return view('queue-monitor.index', compact('stats'));
    }

    public function metrics(): JsonResponse
    {
        return response()->json($this->monitorService->getMetrics());
    }
}
