<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Service;
use App\Models\Artist;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Review;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Default Admin Account
        $admin = User::updateOrCreate(
            ['email' => 'admin@pedro.shop'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '09171234567',
                'email_verified_at' => now(),
            ]
        );

        // 2. Default Staff Account
        $staffUser = User::updateOrCreate(
            ['email' => 'staff@pedro.shop'],
            [
                'name' => 'Regular Staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '09181234567',
                'email_verified_at' => now(),
            ]
        );

        // 3. Default Customer Account
        $customerUser = User::updateOrCreate(
            ['email' => 'customer@pedro.shop'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '09191234567',
                'email_verified_at' => now(),
            ]
        );

        // 4. Default Services
        $servicesList = [
            ['name' => 'Classic Haircut', 'category' => 'Barbering', 'price' => 350.00, 'duration' => '45 min'],
            ['name' => 'Beard Trim', 'category' => 'Barbering', 'price' => 200.00, 'duration' => '30 min'],
            ['name' => 'Fade Cut', 'category' => 'Barbering', 'price' => 450.00, 'duration' => '60 min'],
            ['name' => 'Box Braids', 'category' => 'Hair Styling', 'price' => 1500.00, 'duration' => '180 min'],
            ['name' => 'Cornrows', 'category' => 'Hair Styling', 'price' => 800.00, 'duration' => '120 min'],
            ['name' => 'Small Tattoo', 'category' => 'Tattoo', 'price' => 1200.00, 'duration' => '60 min'],
            ['name' => 'Custom Tattoo', 'category' => 'Tattoo', 'price' => 1500.00, 'duration' => '120 min'],
            ['name' => 'Ear Piercing', 'category' => 'Piercing', 'price' => 300.00, 'duration' => '20 min'],
            ['name' => 'Body Piercing', 'category' => 'Piercing', 'price' => 600.00, 'duration' => '45 min'],
            ['name' => 'Barber & Styling', 'category' => 'Barbering', 'price' => 500.00, 'duration' => '60 min'],
            ['name' => 'Cap Cleaning', 'category' => 'Others', 'price' => 150.00, 'duration' => '30 min'],
        ];

        $services = [];
        foreach ($servicesList as $srv) {
            $services[$srv['name']] = Service::updateOrCreate(['name' => $srv['name']], $srv);
        }

        // 5. Default Artists with Portfolios and Categories
        $artistsList = [
            [
                'name' => 'Pedro Martinez', 
                'specialty' => ['Master Barber', 'Fade Cut'], 
                'category' => 'Barbering',
                'rating' => 4.9, 
                'email' => 'pedro.m@artisan.shop',
                'image_url' => 'https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=400&q=80',
                'portfolio_images' => [
                    'https://images.unsplash.com/photo-1621605815841-28d944683b83?w=500&q=80',
                    'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=500&q=80',
                    'https://images.unsplash.com/photo-1599351431247-f577f5d48c8a?w=500&q=80'
                ]
            ],
            [
                'name' => 'Maya Johnson', 
                'specialty' => ['Braiding Specialist', 'Cornrows'], 
                'category' => 'Hair Styling',
                'rating' => 4.8, 
                'email' => 'maya.j@artisan.shop',
                'image_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&q=80',
                'portfolio_images' => [
                    'https://images.unsplash.com/photo-1582095133179-bf1089201970?w=500&q=80',
                    'https://images.unsplash.com/photo-1574621100236-d25b64cfdd63?w=500&q=80'
                ]
            ],
            [
                'name' => 'Rico Santos', 
                'specialty' => ['Tattoo Artist', 'Realism'], 
                'category' => 'Tattoo',
                'rating' => 5.0, 
                'email' => 'rico.s@artisan.shop',
                'image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80',
                'portfolio_images' => [
                    'https://images.unsplash.com/photo-1598371839696-5c5bb00bdc28?w=500&q=80',
                    'https://images.unsplash.com/photo-1590246814883-57c511e76533?w=500&q=80',
                    'https://images.unsplash.com/photo-1611501275019-9b5cda994e8d?w=500&q=80'
                ]
            ],
            [
                'name' => 'Lisa Chen', 
                'specialty' => ['Piercing Specialist'], 
                'category' => 'Piercing',
                'rating' => 4.9, 
                'email' => 'lisa.c@artisan.shop',
                'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80',
                'portfolio_images' => [
                    'https://images.unsplash.com/photo-1611930528536-482d46ed3021?w=500&q=80',
                    'https://images.unsplash.com/photo-1540206351-d6465b3ac5c1?w=500&q=80'
                ]
            ],
            [
                'name' => 'Jay Williams', 
                'specialty' => ['Barber & Cap Specialist'], 
                'category' => 'Others',
                'rating' => 4.7, 
                'email' => 'jay.w@artisan.shop',
                'image_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80',
                'portfolio_images' => [
                    'https://images.unsplash.com/photo-1532710093739-9470acff0190?w=500&q=80',
                    'https://images.unsplash.com/photo-1512690196162-75d3d0299ecb?w=500&q=80'
                ]
            ],
        ];

        $createdArtists = [];
        foreach ($artistsList as $art) {
            $user = User::updateOrCreate(
                ['email' => $art['email']],
                [
                    'name' => $art['name'],
                    'password' => Hash::make('password'),
                    'role' => 'staff',
                    'email_verified_at' => now(),
                ]
            );

            $art['user_id'] = $user->id;
            $createdArtists[] = Artist::updateOrCreate(['email' => $art['email']], $art);
        }

        // 6. Sample Appointments, Payments, and Reviews
        if (Appointment::count() === 0) {
            $pedro = $createdArtists[0] ?? null;
            $maya = $createdArtists[1] ?? null;
            $classicHaircut = $services['Classic Haircut'] ?? null;
            $boxBraids = $services['Box Braids'] ?? null;

            if ($pedro && $classicHaircut) {
                // Completed past appointment
                $apptPast = Appointment::create([
                    'user_id' => $customerUser->id,
                    'artist_id' => $pedro->id,
                    'service_id' => $classicHaircut->id,
                    'client_name' => $customerUser->name,
                    'phone_number' => '09191234567',
                    'appointment_date' => now()->subDays(1)->toDateString(),
                    'appointment_time' => '10:00',
                    'status' => 'completed',
                    'total_price' => $classicHaircut->price,
                    'duration' => $classicHaircut->duration,
                    'notes' => 'Customer requested clean skin fade.',
                ]);

                Payment::create([
                    'appointment_id' => $apptPast->id,
                    'amount' => $classicHaircut->price,
                    'method' => 'cash',
                    'status' => 'completed',
                    'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                    'items' => [['name' => $classicHaircut->name, 'price' => $classicHaircut->price, 'quantity' => 1]],
                ]);

                Review::create([
                    'user_id' => $customerUser->id,
                    'artist_id' => $pedro->id,
                    'appointment_id' => $apptPast->id,
                    'rating' => 5.0,
                    'comment' => 'Pedro gave me the sharpest fade I have ever had! Highly recommend.',
                ]);

                // Today confirmed appointment
                Appointment::create([
                    'user_id' => $customerUser->id,
                    'artist_id' => $pedro->id,
                    'service_id' => $classicHaircut->id,
                    'client_name' => $customerUser->name,
                    'phone_number' => '09191234567',
                    'appointment_date' => now()->toDateString(),
                    'appointment_time' => '14:00',
                    'status' => 'confirmed',
                    'total_price' => $classicHaircut->price,
                    'duration' => $classicHaircut->duration,
                    'notes' => 'Regular touchup appointment.',
                ]);
            }

            if ($maya && $boxBraids) {
                // Today in-progress appointment
                $apptMaya = Appointment::create([
                    'user_id' => $customerUser->id,
                    'artist_id' => $maya->id,
                    'service_id' => $boxBraids->id,
                    'client_name' => 'Maria Clara',
                    'phone_number' => '09209876543',
                    'appointment_date' => now()->toDateString(),
                    'appointment_time' => '16:00',
                    'status' => 'in-progress',
                    'total_price' => $boxBraids->price,
                    'duration' => $boxBraids->duration,
                    'notes' => 'First time getting box braids.',
                ]);
            }
        }
    }
}
