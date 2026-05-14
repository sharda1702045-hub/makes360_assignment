<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Contact;
use App\Models\Campaign;
use App\Models\CampaignStat;
use App\Models\Template;
use App\Models\ImportJob;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Create Contacts
        for ($i = 0; $i < 50; $i++) {
            Contact::create([
                'user_id' => $user->id,
                'email' => "contact{$i}@example.com",
                'first_name' => "FirstName{$i}",
                'last_name' => "LastName{$i}",
                'status' => $i % 10 == 0 ? 'suppressed' : 'active',
            ]);
        }

        // 3. Create Templates
        $template = Template::create([
            'user_id' => $user->id,
            'name' => 'Welcome Email',
            'subject' => 'Welcome to the platform!',
            'body_html' => '<h1>Welcome</h1><p>Thanks for joining.</p>',
        ]);

        // 4. Create Campaigns
        $campaignId = DB::table('campaigns')->insertGetId([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'contact_list_id' => 1,
            'name' => 'Summer Sale',
            'status' => 'Processing',
            'total_recipients' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('campaign_stats')->insert([
            'campaign_id' => $campaignId,
            'sent_count' => 4500,
            'delivered_count' => 4450,
            'open_count' => 1200,
            'click_count' => 300,
            'bounce_count' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Create Import Job
        ImportJob::create([
            'user_id' => $user->id,
            'filename' => 'contacts_bulk.csv',
            'type' => 'contacts',
            'status' => 'Processing',
            'total_rows' => 100000,
            'processed_rows' => 45000,
            'failed_rows' => 0,
        ]);

        // 6. Create Webhook Logs
        for ($i = 0; $i < 5; $i++) {
            WebhookLog::create([
                'message_id' => "msg_".uniqid(),
                'event_type' => 'Delivery',
                'payload' => ['event' => 'delivered', 'timestamp' => now()],
            ]);
        }
    }
}
