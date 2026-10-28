<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add availability & working hours to artists table
        Schema::table('artists', function (Blueprint $table) {
            if (!Schema::hasColumn('artists', 'live_status')) {
                $table->string('live_status', 20)->default('available')->after('category');
            }
            if (!Schema::hasColumn('artists', 'working_hours')) {
                $table->text('working_hours')->nullable()->after('live_status');
            }
        });

        // Seed default working hours for existing artists
        $defaultSchedule = [
            'monday'    => ['is_working' => false, 'start' => '09:00', 'end' => '18:00'],
            'tuesday'   => ['is_working' => true,  'start' => '09:00', 'end' => '18:00'],
            'wednesday' => ['is_working' => true,  'start' => '09:00', 'end' => '18:00'],
            'thursday'  => ['is_working' => true,  'start' => '09:00', 'end' => '18:00'],
            'friday'    => ['is_working' => true,  'start' => '09:00', 'end' => '18:00'],
            'saturday'  => ['is_working' => true,  'start' => '09:00', 'end' => '18:00'],
            'sunday'    => ['is_working' => true,  'start' => '10:00', 'end' => '17:00'],
        ];

        try {
            DB::table('artists')->whereNull('working_hours')->update([
                'live_status' => 'available',
                'working_hours' => json_encode($defaultSchedule)
            ]);
        } catch (\Throwable $e) {
            // Ignore during initial migration if DB table is empty
        }

        // 2. Create queue_entries table
        if (!Schema::hasTable('queue_entries')) {
            Schema::create('queue_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('artist_id')->constrained('artists')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('queue_number', 20); // e.g. Q01, A-01
                $table->string('client_name');
                $table->string('phone_number')->nullable();
                $table->date('queue_date');
                $table->string('status', 20)->default('waiting'); // waiting, in_service, completed, cancelled, no_show
                $table->unsignedInteger('estimated_wait_minutes')->default(0);
                $table->timestamp('called_at')->nullable();
                $table->timestamp('service_started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['artist_id', 'queue_date', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_entries');

        Schema::table('artists', function (Blueprint $table) {
            if (Schema::hasColumn('artists', 'working_hours')) {
                $table->dropColumn('working_hours');
            }
            if (Schema::hasColumn('artists', 'live_status')) {
                $table->dropColumn('live_status');
            }
        });
    }
};
