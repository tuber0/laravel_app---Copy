<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSpecialtyColumnToJsonInArtistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE artists MODIFY COLUMN specialty TEXT');

        // Convert existing string data to JSON arrays
        $artists = \DB::table('artists')->get();
        foreach ($artists as $artist) {
            if ($artist->specialty && !str_starts_with($artist->specialty, '[')) {
                $jsonValue = json_encode([$artist->specialty]);
                \DB::table('artists')->where('id', $artist->id)->update(['specialty' => $jsonValue]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('ALTER TABLE artists MODIFY COLUMN specialty VARCHAR(255)');
    }
}
