<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Services\TemplateService;

class TemplateController extends Controller
{
    protected $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    public function index(Request $request)
    {
        $templates = $this->templateService->getTemplates($request->all());
        return view('templates.index', compact('templates'));
    }


    public function create()
    {
        return view('templates.create');
    }

    public function show($id)
    {
        $template = $this->templateService->getTemplateDetails($id);
        return view('templates.show', compact('template'));
    }

    public function edit($id)
    {
        $template = $this->templateService->getTemplateDetails($id);
        return view('templates.edit', compact('template'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $template = $this->templateService->createTemplate($validated);

        return response()->json([
            'success' => true,
            'message' => 'Template saved successfully',
            'redirect' => route('templates.index')
        ]);
    }

    public function destroy($id)
    {
        $this->templateService->deleteTemplate($id);
        return redirect()->route('templates.index')->with('success', 'Template deleted successfully.');
    }

    public function duplicate($id)
    {
        $template = $this->templateService->duplicateTemplate($id);
        return redirect()->route('templates.edit', $template->id)->with('success', 'Template duplicated successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $template = $this->templateService->updateTemplate($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully',
            'redirect' => route('templates.index')
        ]);
    }
}
