<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\JsonResponse;

use App\Services\ImportService;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function index()
    {
        $activeImport = $this->importService->getLatestActiveImport();
        $recentActivity = $this->importService->getHistory(3);
        $contactLists = \App\Models\ContactList::latest()->get();
        return view('imports.index', compact('activeImport', 'recentActivity', 'contactLists'));
    }

    public function history()
    {
        $imports = $this->importService->getHistory();
        return view('imports.history', compact('imports'));
    }

    public function status($id): JsonResponse
    {
        return response()->json($this->importService->getImportStatus($id));
    }

    public function export()
    {
        return $this->importService->exportHistory();
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'contact_list_id' => 'nullable|exists:contact_lists,id'
        ]);

        $file = $request->file('file');
        $path = $file->storeAs('imports', $file->getClientOriginalName());
        $fullPath = Storage::path($path);

        // Create the job record
        $job = \App\Models\ImportJob::create([
            'user_id' => auth()->id() ?? 1,
            'filename' => $file->getClientOriginalName(),
            'type' => 'contacts',
            'status' => 'Pending',
            'total_rows' => 0,
            'processed_rows' => 0,
            'failed_rows' => 0,
            'duplicate_rows' => 0,
        ]);

        // Dispatch background job
        \App\Jobs\ImportContactsJob::dispatch($job->id, $fullPath, $request->contact_list_id);

        return back()->with('success', 'Import has been queued and is processing in the background.');
    }
}
