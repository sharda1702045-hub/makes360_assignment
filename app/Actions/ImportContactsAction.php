<?php

namespace App\Actions;

use App\Models\ImportJob;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportContactsAction
{
    /**
     * Execute the action.
     */
    public function execute(ImportJob $importJob): void
    {
        // We use Excel::queueImport to push the file to the 'imports' queue
        Excel::queueImport(
            new ContactsImport($importJob), 
            $importJob->filename, 
            'local' // Storage disk
        )->allOnQueue('imports');
    }
}
