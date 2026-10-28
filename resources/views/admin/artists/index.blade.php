<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'artists';
render_admin_layout_start($page, 'Artist Management');
?>

<div class="dashboard-container">
    <div class="row align-items-center mb-5">
        <div class="col">
            <h1 class="page-title mb-0">Artist Personnel</h1>
            <p class="text-muted small mb-0">Manage your shop's creative talent and their profiles.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.staff.create', ['from' => 'artist']) }}" class="btn btn-gold px-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;margin-right:8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add New Artist
            </a>
        </div>
    </div>

    <!-- User Feedback -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 bg-dark-subtle border-success text-success" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Artist Cards Grid -->
    <div class="row g-4">
        @forelse($artists as $artist)
        <div class="col-md-6 col-xl-4">
            <div class="artist-card h-100">
                <div class="card-options">
                    <div class="dropdown">
                        <button class="btn-icon" data-bs-toggle="dropdown"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" /></svg></button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark p-2 border-border shadow-lg">
                            <li><button class="dropdown-item rounded" onclick='editArtist(@json($artist))'>Edit Profile</button></li>
                            <li><hr class="dropdown-divider border-border"></li>
                            <li>
                                <form action="{{ route('admin.artists.destroy', $artist->id) }}" method="POST" onsubmit="return confirm('Note: This will delete the artist profile permanently.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item rounded text-danger">Delete Artist</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-4 mb-4">
                    <div class="artist-avatar shadow">
                        @if($artist->image_url)
                            <img src="{{ $artist->image_url }}" alt="{{ $artist->name }}">
                        @else
                            {{ substr($artist->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <h5 class="artist-name mb-1">{{ $artist->name }}</h5>
                        <div class="d-flex flex-wrap gap-1">
                            @if(is_array($artist->specialty) && count($artist->specialty) > 0)
                                @foreach($artist->specialty as $s)
                                    <span class="badge bg-gold-subtle text-gold px-2 py-1" style="font-size: 0.7rem;">{{ $s }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-gold-subtle text-gold px-2 py-1" style="font-size: 0.7rem;">{{ is_array($artist->specialty) ? 'General Artist' : ($artist->specialty ?: 'General Artist') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="artist-info-stack mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-gold text-black fw-bold px-2 py-1" style="font-size: 0.75rem;">
                            {{ $artist->years_of_experience ?? 5 }} Years Exp
                        </span>
                        <span class="text-gold fw-bold small">
                            From ₱{{ number_format($artist->starting_rate ?? 300, 2) }}
                        </span>
                    </div>

                    <div class="info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="info-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        <span class="text-white fw-semibold">Linked: {{ $artist->user ? $artist->user->name : 'No account' }}</span>
                        @if($artist->user)
                            <a href="{{ route('admin.staff.oversight') }}" class="ms-auto small text-gold text-decoration-none fw-bold" style="font-size: 0.7rem;">OVERSIGHT →</a>
                        @endif
                    </div>
                    <div class="info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="info-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <span>{{ $artist->email }}</span>
                    </div>
                    <div class="info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="info-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        <span>{{ $artist->phone ?: 'No phone set' }}</span>
                    </div>

                    @if(!empty($artist->skills) && is_array($artist->skills))
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        @foreach(array_slice($artist->skills, 0, 3) as $sk)
                            <span class="badge bg-dark border border-secondary text-white" style="font-size:0.65rem;">{{ $sk }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                
                <p class="artist-bio line-clamp-3 mb-0">{{ $artist->bio ?: 'No biography provided for this artist.' }}</p>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="empty-state">
                <div class="empty-icon-box mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5" stroke="currentColor" style="width:64px;"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.998 5.998 0 00-5.48-3.97M6 18.719a5.993 5.993 0 01-1.48-3.97M6 18.72a9.094 9.094 0 00-3.741-.479 3 3 0 014.682-2.72m.94 3.198l.001.031c0 .225.012.447.037.666A11.944 11.944 0 0012 21c2.17 0 4.207-.576 5.963-1.584A6.062 6.062 0 0118 18.719m-12 0a5.998 5.998 0 015.48-3.97m0 0a5.998 5.998 0 015.48 3.97M12 14.75a3 3 0 100-6 3 3 0 000 6z" /></svg>
                </div>
                <h4 class="fw-bold mb-2">No Artists Registered</h4>
                <p class="text-muted">You haven't added any artists to your roster yet.</p>
                <a href="{{ route('admin.staff.create', ['from' => 'artist']) }}" class="btn btn-outline-gold mt-3 px-4">Initiate First Profile</a>
            </div>
        </div>
        @endforelse
    </div>
    
    <div class="mt-5 d-flex justify-content-center">
        {{ $artists->links() }}
    </div>
</div>

<!-- Add/Edit Artist Modal -->
<div class="modal fade" id="addArtistModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-sidebar border-border border-2">
            <div class="modal-header border-bottom border-border p-4 bg-sidebar">
                <h5 class="modal-title fw-bold text-gold" id="modalTitle" style="font-family: var(--font-bebas); letter-spacing: 1px; font-size: 1.5rem;">Create Artist Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="artistForm" action="{{ route('admin.artists.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label small text-muted text-uppercase fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="artist_name" class="form-control @error('name') is-invalid @enderror" required placeholder="Display name" value="{{ old('name') }}">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="artist_email" class="form-control @error('email') is-invalid @enderror" required placeholder="contact@artisan.com" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Phone (Optional)</label>
                            <input type="text" name="phone" id="artist_phone" class="form-control @error('phone') is-invalid @enderror" placeholder="+1 (555) 000-0000" value="{{ old('phone') }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-muted text-uppercase fw-bold mb-3">Professional Specialty</label>
                        <div class="specialty-grid-container p-3 rounded-4 border border-border" style="background: rgba(0,0,0,0.2); max-height: 200px; overflow-y: auto;">
                            <div class="row g-3">
                                @foreach($categories as $category)
                                    <div class="col-6">
                                        <div class="form-check custom-checkbox">
                                            <input class="form-check-input specialty-checkbox" type="checkbox" name="specialty[]" value="{{ $category }}" id="spec_{{ Str::slug($category) }}" 
                                                {{ is_array(old('specialty')) && in_array($category, old('specialty')) ? 'checked' : '' }}>
                                            <label class="form-check-label small text-white" for="spec_{{ Str::slug($category) }}">
                                                {{ $category }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-6">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input specialty-checkbox" type="checkbox" name="specialty[]" value="Other" id="spec_other" onchange="toggleSpecialtyOther(this.checked)"
                                            {{ is_array(old('specialty')) && in_array('Other', old('specialty')) ? 'checked' : '' }}>
                                        <label class="form-check-label small text-gold fw-bold" for="spec_other">
                                            Other...
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('specialty') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4" id="specialty_custom_container" style="display: none;">
                        <label class="form-label small text-muted text-uppercase fw-bold">Enter Custom Specialty</label>
                        <input type="text" name="specialty_custom" id="artist_specialty_custom" class="form-control" placeholder="e.g. Body Painter, Stylist">
                    </div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Experience (Years)</label>
                            <input type="number" name="years_of_experience" id="artist_experience" class="form-control" placeholder="e.g. 8" min="0" max="60">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Starting Rate (₱)</label>
                            <input type="number" step="0.01" name="starting_rate" id="artist_starting_rate" class="form-control" placeholder="e.g. 350.00" min="0">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Skills (Comma-separated)</label>
                            <input type="text" name="skills" id="artist_skills" class="form-control" placeholder="Skin Fade, Beard, Shave">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Certifications (Comma-sep)</label>
                            <input type="text" name="certifications" id="artist_certifications" class="form-control" placeholder="Licensed Barber, Safety Cert">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Languages (Comma-sep)</label>
                            <input type="text" name="languages" id="artist_languages" class="form-control" placeholder="English, Tagalog">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Preferred Styles</label>
                            <input type="text" name="preferred_styles" id="artist_preferred_styles" class="form-control" placeholder="Realism, Classic Fades">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Instagram URL</label>
                            <input type="text" name="social_instagram" id="artist_instagram" class="form-control" placeholder="https://instagram.com/...">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Facebook URL</label>
                            <input type="text" name="social_facebook" id="artist_facebook" class="form-control" placeholder="https://facebook.com/...">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted text-uppercase fw-bold">Bio / Experience</label>
                        <textarea name="bio" id="artist_bio" class="form-control @error('bio') is-invalid @enderror" rows="4" placeholder="Brief portfolio details...">{{ old('bio') }}</textarea>
                        @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-muted text-uppercase fw-bold">Upload Profile Photo</label>
                        <input type="file" name="image" id="artist_file" class="form-control @error('image') is-invalid @enderror">
                        <p class="text-muted small mt-1">Recommended size: 500x500px. JPG, PNG allowed.</p>
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted text-uppercase fw-bold">OR External Image URL</label>
                        <input type="url" name="image_url" id="artist_image" class="form-control @error('image_url') is-invalid @enderror" placeholder="https://domain.com/path-to-image.jpg">
                        @error('image_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-top border-border p-4">
                    <button type="button" class="btn btn-link text-white text-decoration-none px-4" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-gold px-4 shadow">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .dashboard-container { padding: 3rem; background: var(--background); min-height: 100vh; }
    .page-title { font-family: var(--font-bebas); letter-spacing: 2px; color: var(--primary); font-size: 3rem; margin-bottom: 0.5rem; }
    
    .artist-card {
        background: var(--sidebar);
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 2rem;
        position: relative;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
    }
    
    .artist-card:hover {
        transform: translateY(-10px) scale(1.02);
        border-color: var(--primary);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }
    
    .card-options { position: absolute; top: 1.5rem; right: 1.5rem; z-index: 10; }
    .btn-icon { background: rgba(255,255,255,0.03); border: 1px solid var(--border); color: var(--muted-foreground); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
    .btn-icon:hover { background: rgba(212, 175, 55, 0.1); color: var(--primary); border-color: var(--primary); }
    
    .artist-avatar {
        width: 84px;
        height: 84px;
        background: linear-gradient(135deg, var(--primary), #e5c060);
        color: black;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: 900;
        overflow: hidden;
        border: 2px solid rgba(255,255,255,0.1);
    }
    .artist-avatar img { width: 100%; height: 100%; object-fit: cover; }
    
    .artist-name { font-family: var(--font-montserrat); font-weight: 700; color: white; letter-spacing: -0.5px; }
    
    .artist-info-stack { display: flex; flex-direction: column; gap: 0.85rem; border-top: 1px solid rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.05); padding: 1.25rem 0; }
    .info-item { display: flex; align-items: center; gap: 0.75rem; color: var(--muted-foreground); font-size: 0.85rem; font-weight: 500; }
    .info-icon { width: 16px; color: var(--primary); opacity: 0.8; }
    
    .artist-bio { color: var(--muted-foreground); font-size: 0.85rem; line-height: 1.6; padding-top: 1.25rem; opacity: 0.8; }
    .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    
    .empty-state { padding: 4rem; text-align: center; }
    .empty-icon-box { display: inline-flex; width: 120px; height: 120px; background: rgba(255,255,255,0.03); border-radius: 30px; border: 1px dashed var(--border); items: center; justify-content: center; color: var(--primary); align-items: center; display: flex; margin: 0 auto; margin-bottom: 2rem; }
    
    .modal-content { border-radius: 28px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    .form-control { background: var(--input); border: 1px solid var(--border); color: white; padding: 0.8rem 1.1rem; border-radius: 12px; font-size: 0.9rem; }
    .form-control:focus { background: var(--input); border-color: var(--primary); box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1); color: white; }
    .form-control::placeholder { color: #475569; }

    /* Custom Checkbox Selection */
    .specialty-grid-container::-webkit-scrollbar { width: 5px; }
    .specialty-grid-container::-webkit-scrollbar-track { background: transparent; }
    .specialty-grid-container::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
    
    .custom-checkbox .form-check-input { background-color: #1a1a1a; border-color: var(--border); cursor: pointer; }
    .custom-checkbox .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
    .custom-checkbox .form-check-label { cursor: pointer; transition: color 0.2s; }
    .custom-checkbox:hover .form-check-label { color: var(--primary) !important; }
    
    .btn-gold { background: var(--primary); color: #000; font-weight: 700; border-radius: 12px; transition: 0.4s; }
    .btn-gold:hover { background: #e5c060; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3); }
    .btn-outline-gold { border: 2px solid var(--primary); color: var(--primary); background: transparent; font-weight: 700; border-radius: 12px; }
    .btn-outline-gold:hover { background: var(--primary); color: #000; }
    
    .bg-gold-subtle { background: rgba(212, 175, 55, 0.1); }
    .status-badge { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }

    /* Custom Scrollbar for dropdowns */
    .dropdown-menu::-webkit-scrollbar { width: 4px; }
    .dropdown-menu::-webkit-scrollbar-track { background: transparent; }
    .dropdown-menu::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
</style>

<script>
    function editArtist(artist) {
        document.getElementById('modalTitle').innerText = 'Modify Profile: ' + artist.name;
        
        const baseUrl = window.location.pathname.endsWith('/') ? window.location.pathname.slice(0, -1) : window.location.pathname;
        document.getElementById('artistForm').action = baseUrl + '/' + artist.id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        document.getElementById('artist_name').value = artist.name;
        document.getElementById('artist_email').value = artist.email;
        document.getElementById('artist_phone').value = artist.phone || '';
        document.getElementById('artist_bio').value = artist.bio || '';
        document.getElementById('artist_file').value = '';
        document.getElementById('artist_image').value = artist.image_url || '';
        document.getElementById('artist_experience').value = artist.years_of_experience || '';
        document.getElementById('artist_starting_rate').value = artist.starting_rate || '';
        document.getElementById('artist_skills').value = Array.isArray(artist.skills) ? artist.skills.join(', ') : (artist.skills || '');
        document.getElementById('artist_certifications').value = Array.isArray(artist.certifications) ? artist.certifications.join(', ') : (artist.certifications || '');
        document.getElementById('artist_languages').value = Array.isArray(artist.languages) ? artist.languages.join(', ') : (artist.languages || '');
        document.getElementById('artist_preferred_styles').value = Array.isArray(artist.preferred_styles) ? artist.preferred_styles.join(', ') : (artist.preferred_styles || '');
        document.getElementById('artist_instagram').value = (artist.social_links && artist.social_links.instagram) ? artist.social_links.instagram : '';
        document.getElementById('artist_facebook').value = (artist.social_links && artist.social_links.facebook) ? artist.social_links.facebook : '';
        
        // Handle Specialty Selection (Checkbox Grid)
        const checkboxes = document.querySelectorAll('.specialty-checkbox');
        const customContainer = document.getElementById('specialty_custom_container');
        const customInput = document.getElementById('artist_specialty_custom');
        
        // Reset checkboxes
        checkboxes.forEach(cb => cb.checked = false);
        
        const specialties = Array.isArray(artist.specialty) ? artist.specialty : (artist.specialty ? [artist.specialty] : []);
        let customValues = [];

        specialties.forEach(spec => {
            let found = false;
            checkboxes.forEach(cb => {
                if (cb.value === spec) {
                    cb.checked = true;
                    found = true;
                }
            });
            if (!found) customValues.push(spec);
        });

        if (customValues.length > 0) {
            // Check 'Other' checkbox and fill custom input
            document.getElementById('spec_other').checked = true;
            customContainer.style.display = 'block';
            customInput.value = customValues.join(', ');
        } else {
            customContainer.style.display = 'none';
            customInput.value = '';
        }
        
        const modal = new bootstrap.Modal(document.getElementById('addArtistModal'));
        modal.show();
    }

    function toggleSpecialtyOther(checked) {
        const container = document.getElementById('specialty_custom_container');
        container.style.display = checked ? 'block' : 'none';
        if (!checked) {
            document.getElementById('artist_specialty_custom').value = '';
        }
    }
    
    document.getElementById('addArtistModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('modalTitle').innerText = 'Create Artist Profile';
        document.getElementById('artistForm').action = '{{ route("admin.artists.store") }}';
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('artistForm').reset();
    });

    // Auto-trigger modal if returning from staff creation
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const createdArtistId = urlParams.get('created_artist_id');
        
        if (createdArtistId) {
            // Find the artist data in our paginated set if possible
            // Note: Since we use latest(), the new artist should be on page 1
            @json($artists->items()).forEach(artist => {
                if (artist.id == createdArtistId) {
                    editArtist(artist);
                }
            });
        }
    });
</script>

<?php render_admin_layout_end(); ?>
