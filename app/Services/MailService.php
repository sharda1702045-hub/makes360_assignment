<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

class MailService
{
    /**
     * Send a campaign email to a specific contact.
     */
    public function sendCampaignEmail(Campaign $campaign, Contact $contact): string
    {
        $template = $campaign->template;
        $body = $this->parseTemplate($template->body_html, $contact);
        $subject = $this->parseTemplate($template->subject, $contact);

        // In a real production app, we would use the SES transport directly or a custom Mailable.
        // For this assignment, we use the Laravel Mail facade with a generic raw HTML body.
        $message = Mail::html($body, function ($message) use ($contact, $subject) {
            $message->to($contact->email)
                ->subject($subject);
        });

        // Return the real message ID if available from the transport, or a fallback.
        if ($message && method_exists($message, 'getMessageId')) {
            return $message->getMessageId();
        }

        return 'msg_' . uniqid();
    }

    /**
     * Parse template placeholders like {{ first_name }}.
     */
    protected function parseTemplate(string $content, Contact $contact): string
    {
        $variables = [
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'email' => $contact->email,
        ];

        // Merge custom attributes if any
        if ($contact->attributes) {
            $variables = array_merge($variables, $contact->attributes);
        }

        foreach ($variables as $key => $value) {
            $content = str_replace('{{ ' . $key . ' }}', (string)$value, $content);
            $content = str_replace('{{' . $key . '}}', (string)$value, $content);
        }

        return $content;
    }
}
