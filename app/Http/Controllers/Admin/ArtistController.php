<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Artist;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

class ArtistController extends Controller
{
    public function index()
    {
        // Auto-cleanup any orphan artists whose personnel user account no longer exists
        $orphans = Artist::whereNull('user_id')->orWhereDoesntHave('user')->get();
        foreach ($orphans as $orphan) {
            \App\Models\PortfolioItem::where('artist_id', $orphan->id)->delete();
            \App\Models\Review::where('artist_id', $orphan->id)->delete();
            \App\Models\ScheduleRequest::where('artist_id', $orphan->id)->delete();
            \App\Models\QueueEntry::where('artist_id', $orphan->id)->delete();
            \App\Models\Appointment::where('artist_id', $orphan->id)->delete();
            $orphan->delete();
        }

        $artists = Artist::with('user')->whereHas('user')->latest()->paginate(10);
        $categories = Service::getAllCategories();
        return view('admin.artists.index', compact('artists', 'categories'));
    }

    public function create()
    {
        $categories = Service::getAllCategories();
        return view('admin.artists.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:artists,email',
            'phone'               => 'nullable|string|max:20',
            'category'            => 'nullable|string|max:100',
            'specialty'           => 'nullable|array',
            'specialty.*'         => 'string|max:255',
            'bio'                 => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0|max:80',
            'skills'              => 'nullable|string',
            'certifications'      => 'nullable|string',
            'languages'           => 'nullable|string',
            'starting_rate'       => 'nullable|numeric|min:0',
            'social_instagram'    => 'nullable|string|max:255',
            'social_facebook'     => 'nullable|string|max:255',
            'preferred_styles'    => 'nullable|string',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:20480',
            'image_url'           => 'nullable|string'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('artists', 'public');
            $validated['image_url'] = '/storage/' . $path;
            $validated['profile_photo'] = $path;
        }

        $parseTags = function($input) {
            if (empty($input)) return [];
            if (is_array($input)) return $input;
            return array_values(array_filter(array_map('trim', explode(',', $input))));
        };

        // Handle custom specialty
        $specialties = $request->input('specialty', []);
        if (in_array('Other', $specialties) && $request->filled('specialty_custom')) {
            $specialties = array_filter($specialties, fn($s) => $s !== 'Other');
            $specialties[] = $request->specialty_custom;
            $validated['specialty'] = array_values($specialties);
        } else {
            $validated['specialty'] = $specialties;
        }
        $validated['specialties'] = $validated['specialty'];

        $validated['skills'] = $parseTags($request->input('skills'));
        $validated['certifications'] = $parseTags($request->input('certifications'));
        $validated['languages'] = $parseTags($request->input('languages'));
        $validated['preferred_styles'] = $parseTags($request->input('preferred_styles'));
        $validated['social_links'] = [
            'instagram' => $request->input('social_instagram', ''),
            'facebook' => $request->input('social_facebook', ''),
        ];

        // Synchronize with Personnel (User) database
        $user = \App\Models\User::where('email', $validated['email'])->first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'phone'             => $validated['phone'] ?? null,
                'role'              => 'staff',
                'password'          => bcrypt('StaffPassword123!'),
                'email_verified_at' => now(),
            ]);
        }
        $validated['user_id'] = $user->id;

        Artist::create($validated);
        return redirect()->route('admin.artists.index')->with('success', 'Artist profile and connected personnel account created successfully.');
    }

    public function show($id)
    {
        $artist = Artist::with('portfolioItems')->findOrFail($id);
        return view('admin.artists.show', compact('artist'));
    }

    public function edit($id)
    {
        $artist = Artist::with('portfolioItems')->findOrFail($id);
        $categories = Service::getAllCategories();
        return view('admin.artists.edit', compact('artist', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $artist = Artist::findOrFail($id);
        
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:artists,email,'.$id,
            'phone'               => 'nullable|string|max:20',
            'category'            => 'nullable|string|max:100',
            'specialty'           => 'nullable|array',
            'specialty.*'         => 'string|max:255',
            'bio'                 => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0|max:80',
            'skills'              => 'nullable|string',
            'certifications'      => 'nullable|string',
            'languages'           => 'nullable|string',
            'starting_rate'       => 'nullable|numeric|min:0',
            'social_instagram'    => 'nullable|string|max:255',
            'social_facebook'     => 'nullable|string|max:255',
            'preferred_styles'    => 'nullable|string',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:20480',
            'image_url'           => 'nullable|string'
        ]);

        if ($request->hasFile('image')) {
            // Delete old local image if it exists
            if ($artist->image_url && str_contains($artist->image_url, '/storage/artists/')) {
                $oldPath = str_replace('/storage/', '', $artist->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('image')->store('artists', 'public');
            $validated['image_url'] = '/storage/' . $path;
            $validated['profile_photo'] = $path;
        }

        $parseTags = function($input) {
            if (empty($input)) return [];
            if (is_array($input)) return $input;
            return array_values(array_filter(array_map('trim', explode(',', $input))));
        };

        // Handle custom specialty
        $specialties = $request->input('specialty', []);
        if (is_array($specialties) && in_array('Other', $specialties) && $request->filled('specialty_custom')) {
            $specialties = array_filter($specialties, fn($s) => $s !== 'Other');
            $specialties[] = $request->specialty_custom;
            $validated['specialty'] = array_values($specialties);
        } else {
            $validated['specialty'] = $specialties;
        }
        $validated['specialties'] = $validated['specialty'];

        $validated['skills'] = $parseTags($request->input('skills', $artist->skills));
        $validated['certifications'] = $parseTags($request->input('certifications', $artist->certifications));
        $validated['languages'] = $parseTags($request->input('languages', $artist->languages));
        $validated['preferred_styles'] = $parseTags($request->input('preferred_styles', $artist->preferred_styles));
        $validated['social_links'] = [
            'instagram' => $request->input('social_instagram', $artist->social_links['instagram'] ?? ''),
            'facebook' => $request->input('social_facebook', $artist->social_links['facebook'] ?? ''),
        ];

        $artist->update($validated);

        // Keep connected User record in sync
        if ($artist->user_id) {
            $user = \App\Models\User::find($artist->user_id);
            if ($user) {
                $user->update([
                    'name'  => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? $user->phone,
                ]);
            }
        } elseif ($artist->email) {
            $user = \App\Models\User::where('email', $artist->email)->first();
            if ($user) {
                $artist->update(['user_id' => $user->id]);
                $user->update([
                    'name'  => $validated['name'],
                    'email' => $validated['email'],
                ]);
            }
        }

        return redirect()->route('admin.artists.index')->with('success', 'Artist profile updated successfully.');
    }

    public function destroy($id)
    {
        $artist = Artist::findOrFail($id);
        $userId = $artist->user_id;
        $artistEmail = $artist->email;

        // Cascade cleanup
        \App\Models\PortfolioItem::where('artist_id', $artist->id)->delete();
        \App\Models\Review::where('artist_id', $artist->id)->delete();
        \App\Models\ScheduleRequest::where('artist_id', $artist->id)->delete();
        \App\Models\QueueEntry::where('artist_id', $artist->id)->delete();
        \App\Models\Appointment::where('artist_id', $artist->id)->delete();
        $artist->delete();

        // Delete connected staff account in personnel database
        if ($userId) {
            \App\Models\User::where('id', $userId)->where('role', 'staff')->delete();
        } elseif ($artistEmail) {
            \App\Models\User::where('email', $artistEmail)->where('role', 'staff')->delete();
        }

        return redirect()->route('admin.artists.index')->with('success', 'Artist profile and connected personnel account deleted successfully.');
    }
}
