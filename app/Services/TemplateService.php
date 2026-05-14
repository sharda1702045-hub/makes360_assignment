<?php

namespace App\Services;

use App\Models\Template;
use Illuminate\Support\Facades\DB;

class TemplateService
{
    public function getTemplates($filters = [], $perPage = 12)
    {
        $query = Template::latest();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('subject', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($perPage);
    }

    public function getTemplateDetails($id)
    {
        $template = Template::with(['campaigns.stats'])->findOrFail($id);
        
        // Aggregate stats
        $stats = [
            'total_sent' => $template->campaigns->sum('sent_count'),
            'total_opens' => $template->campaigns->sum(fn($c) => $c->stats->opens ?? 0),
            'total_clicks' => $template->campaigns->sum(fn($c) => $c->stats->clicks ?? 0),
        ];

        $stats['open_rate'] = $stats['total_sent'] > 0 ? round(($stats['total_opens'] / $stats['total_sent']) * 100, 1) : 0;
        $stats['click_rate'] = $stats['total_sent'] > 0 ? round(($stats['total_clicks'] / $stats['total_sent']) * 100, 1) : 0;

        $template->aggregated_stats = (object) $stats;

        return $template;
    }

    public function createTemplate(array $data)
    {
        return Template::create([
            'user_id' => auth()->id() ?? 1,
            'name' => $data['name'],
            'subject' => $data['subject'],
            'body_html' => $data['content'],
            'body_text' => strip_tags($data['content'])
        ]);
    }

    public function deleteTemplate($id)
    {
        return Template::findOrFail($id)->delete();
    }

    public function duplicateTemplate($id)
    {
        $original = Template::findOrFail($id);
        $copy = $original->replicate();
        $copy->name = $original->name . ' (Copy)';
        $copy->save();
        return $copy;
    }

    public function updateTemplate($id, array $data)
    {
        $template = Template::findOrFail($id);
        $template->update([
            'name' => $data['name'],
            'subject' => $data['subject'],
            'body_html' => $data['content'],
            'body_text' => strip_tags($data['content'])
        ]);
        return $template;
    }
}
