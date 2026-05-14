<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('total_contacts')->default(0);
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'email']);
        });

        Schema::create('contact_list_mapping', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_list_id')->constrained()->onDelete('cascade');
            $table->primary(['contact_id', 'contact_list_id']);
        });

        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('subject');
            $table->longText('body_html');
            $table->longText('body_text')->nullable();
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->constrained();
            $table->foreignId('contact_list_id')->constrained();
            $table->string('name');
            $table->string('status')->default('draft'); // draft, scheduled, processing, completed, failed, cancelled
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->string('batch_id')->nullable(); // For Laravel Job Batching
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('campaign_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->string('message_id')->nullable(); // Provider message ID
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
        });

        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->index();
            $table->string('event'); // delivered, opened, clicked, bounced
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('changes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('campaign_emails');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('templates');
        Schema::dropIfExists('contact_list_mapping');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_lists');
    }
};
