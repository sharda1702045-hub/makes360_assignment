<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImportJob;
use App\Services\ImportService;
use Illuminate\Support\Facades\Storage;

$job = ImportJob::find(20);
if (!$job) {
    echo "Job 20 not found\n";
    exit;
}

$path = Storage::path('imports/' . $job->filename);
echo "Processing file: $path\n";

$service = app(ImportService::class);
try {
    $service->processCsv($job, $path);
    echo "Processing complete.\n";
    print_r($job->fresh()->toArray());
} catch (\Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
}
