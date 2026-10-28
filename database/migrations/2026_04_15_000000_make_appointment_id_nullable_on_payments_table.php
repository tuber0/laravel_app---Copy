<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeAppointmentIdNullableOnPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Using raw SQL to avoid doctrine/dbal dependency for nullable change
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE payments MODIFY appointment_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE payments MODIFY appointment_id BIGINT UNSIGNED NOT NULL');
    }
}
