<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $percentage = $this->total_rows > 0 
            ? round(($this->processed_rows + $this->failed_rows) / $this->total_rows * 100, 2)
            : 0;

        return [
            'id' => $this->id,
            'status' => $this->status,
            'metrics' => [
                'total' => $this->total_rows,
                'processed' => $this->processed_rows,
                'failed' => $this->failed_rows,
                'skipped' => $this->skipped_rows,
                'percentage' => $percentage,
            ],
            'errors' => $this->error_log,
            'timestamps' => [
                'started_at' => $this->started_at?->toIso8601String(),
                'completed_at' => $this->completed_at?->toIso8601String(),
            ],
        ];
    }
}
