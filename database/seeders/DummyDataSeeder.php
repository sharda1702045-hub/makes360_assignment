<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Contact;
use App\Models\ContactList;
use App\Models\Campaign;
use App\Models\CampaignStat;
use App\Models\Template;
use App\Models\EmailEvent;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing data for a clean test
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('contact_contact_list')->truncate();
        Contact::truncate();
        ContactList::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Get or Create User
        $user = User::first() ?: User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Create Audience Lists
        $lists = [
            ContactList::create(['user_id' => $user->id, 'name' => 'VIP Customers']),
            ContactList::create(['user_id' => $user->id, 'name' => 'Newsletter Subscribers']),
            ContactList::create(['user_id' => $user->id, 'name' => 'New Leads']),
        ];

        // 3. Create Contacts and map them to lists
        for ($i = 0; $i < 5; $i++) {
            $contact = Contact::create([
                'user_id' => $user->id,
                'email' => "user{$i}@example.com",
                'first_name' => "User{$i}",
                'last_name' => "Smith",
                'status' => rand(0, 10) > 8 ? 'suppressed' : 'active',
            ]);

            // Randomly assign to 1 or 2 lists
            $randomLists = (array) array_rand($lists, rand(1, 2));
            foreach ($randomLists as $index) {
                $list = $lists[$index];
                $contact->lists()->attach($list->id);
                $list->increment('total_contacts');
            }
        }

        /*
        // 3.5 Create Featured Contacts in all lists
        $featuredEmails = ['vip@example.com', 'test@example.com', 'marketing@example.com'];
        foreach ($featuredEmails as $email) {
            $featured = Contact::create([
                'user_id' => $user->id,
                'email' => $email,
                'first_name' => 'Featured',
                'last_name' => 'Contact',
                'status' => 'active',
            ]);
            foreach ($lists as $list) {
                $featured->lists()->attach($list->id);
                $list->increment('total_contacts');
            }
        }
        */

        // 4. Create Templates
        $templates = [
            Template::create(['user_id' => $user->id, 'name' => 'Product Update', 'subject' => 'Check out our new features!', 'body_html' => '<h1>Updates</h1><p>We have launched new features.</p>']),
            Template::create(['user_id' => $user->id, 'name' => 'Holiday Offer', 'subject' => 'Get 20% off this weekend', 'body_html' => '<h1>Discount</h1><p>Use code HOLIDAY20.</p>']),
        ];

        // 5. Create a single Draft Campaign for testing UI
        Campaign::create([
            'user_id' => $user->id,
            'template_id' => $templates[0]->id,
            'contact_list_id' => $lists[0]->id,
            'name' => 'Initial Test Campaign',
            'status' => 'draft',
            'total_recipients' => 100,
        ]);

        // 6. Webhook logs and events removed as per user request to keep it clean for real sending.

        // 7. Insert a few failed jobs for testing the monitor
        for ($i = 0; $i < 3; $i++) {
            DB::table('failed_jobs')->insert([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'connection' => 'redis',
                'queue' => 'default',
                'payload' => json_encode(['job' => 'App\\Jobs\\SendEmailJob', 'data' => ['id' => $i]]),
                'exception' => 'Illuminate\\Database\\QueryException: SQLSTATE[HY000]: General error: 2006 MySQL server has gone away',
                'failed_at' => Carbon::now()->subHours(rand(1, 24)),
            ]);
        }
        // 7. Seed initial system logs (Non-email related)
        WebhookLog::create([
            'message_id' => 'sys_' . uniqid(),
            'event_type' => 'system_boot',
            'payload' => ['status' => 'ready', 'env' => 'production']
        ]);
        WebhookLog::create([
            'message_id' => 'sys_' . uniqid(),
            'event_type' => 'queue_worker_started',
            'payload' => ['queue' => 'default', 'worker' => 'worker-01']
        ]);
    }
}
