<?php

namespace App\Jobs;

use App\Models\ImportJob;
use App\Services\ImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportContactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobId;
    protected $filePath;
    protected $contactListId;

    /**
     * Create a new job instance.
     */
    public function __construct($jobId, $filePath, $contactListId = null)
    {
        $this->jobId = $jobId;
        $this->filePath = $filePath;
        $this->contactListId = $contactListId;
    }

    /**
     * Execute the job.
     */
    public function handle(ImportService $importService): void
    {
        \Illuminate\Support\Facades\Log::info("Starting Import Job #" . $this->jobId);
        $importJob = ImportJob::find($this->jobId);
        
        if ($importJob) {
            $importService->processCsv($importJob, $this->filePath, $this->contactListId);
        }
    }
}
