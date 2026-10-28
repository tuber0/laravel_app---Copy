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
        // 1. Add additional profile and portfolio fields to artists table
        Schema::table('artists', function (Blueprint $table) {
            $table->unsignedInteger('years_of_experience')->nullable()->after('bio');
            $table->text('specialties')->nullable()->after('specialty');
            $table->text('skills')->nullable()->after('specialties');
            $table->text('certifications')->nullable()->after('skills');
            $table->text('languages')->nullable()->after('certifications');
            $table->decimal('starting_rate', 10, 2)->nullable()->after('languages');
            $table->text('social_links')->nullable()->after('starting_rate'); // JSON: instagram, facebook
            $table->text('preferred_styles')->nullable()->after('social_links');
            $table->string('profile_photo')->nullable()->after('image_url');
        });

        // Sync existing specialty and image_url to new columns if needed
        $artists = DB::table('artists')->get();
        foreach ($artists as $artist) {
            DB::table('artists')->where('id', $artist->id)->update([
                'specialties' => $artist->specialty,
                'profile_photo' => $artist->image_url,
                'years_of_experience' => match($artist->name) {
                    'Pedro Martinez' => 15,
                    'Maya Johnson' => 10,
                    'Rico Santos' => 12,
                    'Lisa Chen' => 8,
                    'Jay Williams' => 6,
                    default => 5
                },
                'starting_rate' => match($artist->category) {
                    'Barbering' => 350.00,
                    'Hair Styling' => 800.00,
                    'Tattoo' => 1200.00,
                    'Piercing' => 300.00,
                    default => 250.00
                },
                'skills' => json_encode(match($artist->category) {
                    'Barbering' => ['Skin Fade', 'Scissor Work', 'Beard Sculpting', 'Hot Towel Shave'],
                    'Hair Styling' => ['Box Braids', 'Cornrows', 'Knotless Braids', 'Loc Maintenance'],
                    'Tattoo' => ['Fine Line', 'Micro Realism', 'Custom Lettering', 'Color Work'],
                    'Piercing' => ['Septum', 'Helix', 'Industrial', 'Sterile Technique'],
                    default => ['Consultation', 'Custom Styling', 'Finishing']
                }),
                'certifications' => json_encode(match($artist->category) {
                    'Barbering' => ['Licensed Master Barber', 'Sanitation Safety Certified'],
                    'Hair Styling' => ['Certified Braiding Specialist', 'Natural Hair Care License'],
                    'Tattoo' => ['Licensed Tattoo Artist', 'Bloodborne Pathogen Certified'],
                    'Piercing' => ['APP Member (Association of Professional Piercers)', 'Body Piercing License'],
                    default => ['Artisan Service Certified']
                }),
                'languages' => json_encode(['English', 'Tagalog']),
                'preferred_styles' => json_encode(match($artist->category) {
                    'Barbering' => ['Modern Textured Crop', 'Classic Pompadour', 'Sharp Taper'],
                    'Hair Styling' => ['Boho Braids', 'Geometric Cornrows', 'Clean Partings'],
                    'Tattoo' => ['Black & Grey Realism', 'Neo-Traditional', 'Illustrative'],
                    'Piercing' => ['Curated Ear Projects', 'Titanium Jewelry Setting'],
                    default => ['Signature Custom Style']
                }),
                'social_links' => json_encode([
                    'instagram' => 'https://instagram.com/pedrosartisan',
                    'facebook' => 'https://facebook.com/pedrosartisan'
                ]),
            ]);
        }

        // 2. Create portfolio_items table for the gallery
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artist_id');
            $table->unsignedBigInteger('staff_artist_id')->nullable(); // compatibility alias
            $table->string('image_path');
            $table->string('title')->nullable();
            $table->string('category')->nullable();
            $table->text('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('artist_id')->references('id')->on('artists')->onDelete('cascade');
        });

        // 3. Migrate existing portfolio_images JSON array into portfolio_items table
        foreach ($artists as $artist) {
            if ($artist->portfolio_images) {
                $images = json_decode($artist->portfolio_images, true);
                if (is_array($images)) {
                    foreach ($images as $index => $imgPath) {
                        DB::table('portfolio_items')->insert([
                            'artist_id' => $artist->id,
                            'staff_artist_id' => $artist->id,
                            'image_path' => $imgPath,
                            'title' => $artist->name . ' Showcase #' . ($index + 1),
                            'category' => $artist->category,
                            'caption' => 'Artisan portfolio showcase by ' . $artist->name,
                            'sort_order' => $index,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');

        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn([
                'years_of_experience',
                'specialties',
                'skills',
                'certifications',
                'languages',
                'starting_rate',
                'social_links',
                'preferred_styles',
                'profile_photo',
            ]);
        });
    }
};
