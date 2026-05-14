<?php

namespace App\Services;

use App\Models\EmailEvent;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    public function getDeliveryTrend($days = 7)
    {
        $data = EmailEvent::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('type', 'delivered')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($data->isEmpty()) {
            return [
                'labels' => collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('D'))->toArray(),
                'values' => array_fill(0, 7, 0)
            ];
        }

        return [
            'labels' => $data->pluck('date')->map(fn($d) => Carbon::parse($d)->format('D'))->toArray(),
            'values' => $data->pluck('count')->toArray(),
        ];
    }

    public function getEngagementTrend($days = 7)
    {
        $opens = EmailEvent::where('type', 'open')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')->get();

        $clicks = EmailEvent::where('type', 'click')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')->get();

        if ($opens->isEmpty() && $clicks->isEmpty()) {
            return [
                'labels' => collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('D'))->toArray(),
                'opens' => array_fill(0, 7, 0),
                'clicks' => array_fill(0, 7, 0)
            ];
        }

        return [
            'labels' => $opens->pluck('date')->map(fn($d) => Carbon::parse($d)->format('D'))->toArray(),
            'opens' => $opens->pluck('count')->toArray(),
            'clicks' => $clicks->pluck('count')->toArray(),
        ];
    }
}
