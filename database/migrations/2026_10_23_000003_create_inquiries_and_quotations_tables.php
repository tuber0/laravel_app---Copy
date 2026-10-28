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
        // 1. Inquiries table
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('artist_id')->constrained('artists')->onDelete('cascade');
            $table->string('service_type'); // e.g. Tattoo, Piercing, Barbering, Custom
            $table->text('description');
            $table->json('reference_images')->nullable(); // JSON array of stored file paths
            $table->date('preferred_date')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->string('status')->default('pending'); // pending, quoted, accepted, declined, expired
            $table->timestamps();
        });

        // 2. Inquiry Messages table (simple thread)
        Schema::create('inquiry_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained('inquiries')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // 3. Quotations table
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained('inquiries')->onDelete('cascade');
            $table->foreignId('artist_id')->constrained('artists')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('estimated_duration')->nullable(); // e.g. "2 hours", "3 sessions"
            $table->text('notes')->nullable();
            $table->date('validity_date')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, declined, revision_requested, expired
            $table->text('revision_notes')->nullable();
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('queue_entry_id')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')->nullOnDelete();
            $table->foreign('queue_entry_id')->references('id')->on('queue_entries')->nullOnDelete();
        });

        // 4. Quotation Items table
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
        });

        // 5. In-app notifications table
        Schema::create('in_app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type')->default('info'); // info, success, warning, quote
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_app_notifications');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('inquiry_messages');
        Schema::dropIfExists('inquiries');
    }
};
