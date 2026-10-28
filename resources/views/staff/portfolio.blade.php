<?php
require_once resource_path('views/components/staff-layout.php');
$page = 'portfolio';
render_staff_layout_start($page, 'My Portfolio');
?>

<div class="dashboard-container p-2 p-md-4">
    @if(!$artist)
    <div class="alert alert-warning border-warning bg-dark-subtle text-warning shadow-sm" style="border-radius: 12px;">
        <div class="d-flex align-items-center gap-3">
            <div class="icon-box-warning p-2 rounded-circle" style="background: rgba(255, 193, 7, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
            </div>
            <div>
                <strong class="d-block">Account Initialization Required</strong>
                <span class="small opacity-75">Your professional Artist profile hasn't been initialized yet. Please contact your Administrator to set up your professional roster entry.</span>
            </div>
        </div>
    </div>
    @else
    <div class="d-flex align-items-center gap-2 mb-4">
        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill d-flex align-items-center gap-2 border border-success border-opacity-10" style="font-size: 0.75rem;">
            <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; border: 2px solid rgba(16, 185, 129, 0.3); animation: pulse 2s infinite;"></div>
            Live Profile: {{ $artist->name }} ({{ $artist->email }})
        </span>
    </div>
    @endif

    <div class="row align-items-center mb-5">
        <div class="col">
            <h1 class="page-title mb-0" style="font-family: var(--font-bebas); letter-spacing: 2px; color: var(--primary); font-size: 3rem;">Manage Professional Portfolio</h1>
            <p class="text-muted-foreground small mb-0 fw-medium">Showcase your talent. Update your bio, specialties, and gallery to stand out to clients.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success bg-success-subtle border-success border-opacity-25 text-success d-flex align-items-center gap-3 mb-4 p-3" style="border-radius: 12px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span class="fw-bold">{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger bg-danger-subtle border-danger border-opacity-25 text-danger d-flex align-items-center gap-3 mb-4 p-3" style="border-radius: 12px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        <span class="fw-bold">{{ session('error') }}</span>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            @if($artist)
            <div class="glass-card p-4 p-md-5" style="border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); background: rgba(18,18,18,0.8);">
                <form action="{{ route('staff.portfolio.update') }}" method="POST" enctype="multipart/form-data" id="portfolioForm">
                    @csrf
                    
                    <div class="row g-5 mb-5 align-items-center">
                        <div class="col-md-4 col-lg-3 text-center">
                            <div class="position-relative d-inline-block">
                                <div class="profile-preview-wrapper shadow-lg" style="width: 180px; height: 180px; border-radius: 50%; padding: 4px; background: linear-gradient(45deg, var(--primary), transparent); overflow: hidden;">
                                    @php
                                        $photoPath = $artist->profile_photo ?: $artist->image_url;
                                    @endphp
                                    @if($photoPath)
                                        <img id="primary_preview" src="{{ \Illuminate\Support\Str::startsWith($photoPath, 'http') ? $photoPath : asset('storage/' . $photoPath) }}" alt="Profile Image" class="img-fluid rounded-circle object-fit-cover w-100 h-100 shadow" style="background: var(--input)">
                                    @else
                                        <div id="primary_preview_placeholder" class="rounded-circle bg-muted d-flex align-items-center justify-content-center w-100 h-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:64px; color:var(--muted-foreground)"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                        </div>
                                        <img id="primary_preview" src="" class="img-fluid rounded-circle object-fit-cover w-100 h-100 shadow d-none" style="background: var(--input)">
                                    @endif
                                </div>
                                <label for="primary_image_input" class="btn btn-gold btn-sm position-absolute bottom-0 end-0 rounded-circle p-2 shadow-lg" style="width: 40px; height: 40px; border: 2px solid #121212;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" /></svg>
                                </label>
                                <input type="file" id="primary_image_input" name="primary_image" class="d-none" accept="image/jpeg, image/png, image/jpg, image/webp" onchange="previewPrimaryImage(this)">
                            </div>
                            <div class="mt-3 text-gold small fw-bold text-uppercase" style="letter-spacing: 1px;">Profile Photo</div>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <div class="card bg-input border-0 p-4" style="border-radius: 16px;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Professional Specialties</label>
                                        <input type="text" name="specialty" class="form-control bg-dark border-secondary text-white py-2" value="{{ old('specialty', is_array($artist->specialties ?: $artist->specialty) ? implode(', ', $artist->specialties ?: $artist->specialty) : ($artist->specialties ?: $artist->specialty)) }}" placeholder="e.g. Master Barber, Realism, Box Braids">
                                        <div class="form-text text-muted-foreground mt-1 small">Comma separated tags.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Experience (Years)</label>
                                        <input type="number" name="years_of_experience" min="0" max="60" class="form-control bg-dark border-secondary text-white py-2" value="{{ old('years_of_experience', $artist->years_of_experience ?? 5) }}" placeholder="e.g. 8">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Starting Rate (₱)</label>
                                        <input type="number" step="0.01" name="starting_rate" min="0" class="form-control bg-dark border-secondary text-white py-2" value="{{ old('starting_rate', $artist->starting_rate ?? 350.00) }}" placeholder="e.g. 350">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Qualifications & Tags -->
                    <div class="card bg-input border-0 p-4 mb-5" style="border-radius: 16px;">
                        <h5 class="fw-bold text-white mb-3" style="font-family: var(--font-montserrat);">Professional Profile Details</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Core Skills & Techniques</label>
                                <input type="text" name="skills" class="form-control bg-dark border-secondary text-white py-2" value="{{ old('skills', is_array($artist->skills) ? implode(', ', $artist->skills) : $artist->skills) }}" placeholder="e.g. Skin Fade, Scissor Work, Hot Towel, Micro Realism">
                                <div class="form-text text-muted-foreground small">Comma-separated skills highlighted on your artist card.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Certifications & Licenses</label>
                                <input type="text" name="certifications" class="form-control bg-dark border-secondary text-white py-2" value="{{ old('certifications', is_array($artist->certifications) ? implode(', ', $artist->certifications) : $artist->certifications) }}" placeholder="e.g. Licensed Master Barber, Bloodborne Pathogen Certified">
                                <div class="form-text text-muted-foreground small">Professional credentials and safety accreditations.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Languages Spoken</label>
                                <input type="text" name="languages" class="form-control bg-dark border-secondary text-white py-2" value="{{ old('languages', is_array($artist->languages) ? implode(', ', $artist->languages) : $artist->languages) }}" placeholder="e.g. English, Tagalog">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Instagram Profile URL / Handle</label>
                                <input type="text" name="social_instagram" class="form-control bg-dark border-secondary text-white py-2" value="{{ old('social_instagram', $artist->social_links['instagram'] ?? '') }}" placeholder="https://instagram.com/yourhandle">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Facebook Profile URL</label>
                                <input type="text" name="social_facebook" class="form-control bg-dark border-secondary text-white py-2" value="{{ old('social_facebook', $artist->social_links['facebook'] ?? '') }}" placeholder="https://facebook.com/yourprofile">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Preferred Artistic Styles</label>
                                <input type="text" name="preferred_styles" class="form-control bg-dark border-secondary text-white py-2" value="{{ old('preferred_styles', is_array($artist->preferred_styles) ? implode(', ', $artist->preferred_styles) : $artist->preferred_styles) }}" placeholder="e.g. Textured Crops, Neo-traditional ink, Clean geometric braids">
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-gold small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Professional Biography</label>
                        <textarea name="bio" class="form-control bg-dark border-secondary text-white p-4" rows="4" style="border-radius: 16px; font-size: 1rem; line-height: 1.6;" placeholder="Tell clients about your artistic background, years of experience, and signature aesthetic...">{{ old('bio', $artist->bio) }}</textarea>
                        <div class="form-text text-muted-foreground mt-2 opacity-75 fw-medium">Your bio is your first impression. Keep it professional and engaging.</div>
                        @error('bio') <div class="text-danger small mt-1 fw-bold">{{ $message }}</div> @enderror
                    </div>

                    <!-- Portfolio Gallery Items Table / Grid -->
                    <div class="gallery-management-section mt-5 border-top border-secondary pt-5">
                        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                            <div>
                                <h4 class="mb-1 fw-bold" style="color:var(--foreground); font-family:var(--font-montserrat)">Portfolio Gallery Items</h4>
                                <p class="text-muted-foreground small mb-0">Structured gallery pieces showcasing your artisan work.</p>
                            </div>
                            <label for="gallery_images_input" class="btn btn-gold px-4 py-2 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px; border-radius: 8px;">
                                + Add Portfolio Photos
                            </label>
                            <input type="file" id="gallery_images_input" name="gallery_images[]" class="d-none" accept="image/jpeg, image/png, image/jpg, image/webp" multiple>
                        </div>
                        
                        @if($artist->portfolioItems && $artist->portfolioItems->count() > 0)
                        <div class="row g-4 mb-4">
                            @foreach($artist->portfolioItems as $pItem)
                            <div class="col-sm-6 col-md-4 col-xl-3" id="portfolio-item-card-{{ $pItem->id }}">
                                <div class="card h-100 bg-dark border-secondary overflow-hidden shadow-sm">
                                    <div class="position-relative ratio ratio-1x1">
                                        <img src="{{ \Illuminate\Support\Str::startsWith($pItem->image_path, 'http') ? $pItem->image_path : asset('storage/' . $pItem->image_path) }}" class="object-fit-cover w-100 h-100" alt="{{ $pItem->title }}">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle p-1" style="width:30px;height:30px;" onclick="markItemForDeletion({{ $pItem->id }})">
                                            &times;
                                        </button>
                                    </div>
                                    <div class="p-3">
                                        <div class="fw-bold text-white small mb-1 text-truncate">{{ $pItem->title ?: 'Work Sample' }}</div>
                                        <div class="text-muted small text-truncate">{{ $pItem->caption ?: 'Category: ' . ($pItem->category ?: $artist->category) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @elseif(is_array($artist->portfolio_images) && count($artist->portfolio_images) > 0)
                        <div class="row g-4" id="gallery-container">
                            @foreach($artist->portfolio_images as $index => $img)
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 gallery-item-card" data-url="{{ trim($img) }}">
                                <div class="position-relative ratio ratio-1x1 rounded-4 overflow-hidden shadow-sm" style="border: 1px solid rgba(255,255,255,0.05); transition: transform 0.3s ease;">
                                    <img src="{{ \Illuminate\Support\Str::startsWith(trim($img), 'http') ? trim($img) : asset('storage/' . trim($img)) }}" class="object-fit-cover w-100 h-100" alt="Portfolio Image">
                                    <div class="position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-black bg-opacity-60 overlay-action opacity-0 hover-opacity-100 transition" style="top:0; left:0; backdrop-filter: blur(2px);">
                                        <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-4 remove-gallery-btn" style="font-size: 0.7rem; letter-spacing: 1px;">
                                            REMOVE
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="col-12 text-center py-5 rounded-4" style="background: rgba(255,255,255,0.02); border: 2px dashed rgba(255,255,255,0.05);">
                            <div class="mb-3 opacity-25">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:48px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                            </div>
                            <p class="text-muted-foreground small fw-medium mb-0">No gallery items yet. Click "+ Add Portfolio Photos" to upload work samples.</p>
                        </div>
                        @endif
                    </div>

                    <!-- Hidden container to hold removed URLs & Item IDs -->
                    <div id="hidden-removals"></div>
                    <div id="hidden-portfolio-deletions"></div>

                    <div class="submit-area sticky-bottom bg-dark bg-opacity-75 p-4 mt-5 d-flex align-items-center justify-content-between" style="border-radius: 16px; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.05); bottom: 20px;">
                        <span class="text-muted-foreground small d-none d-md-block fw-medium">All updates sync directly with customer booking & portfolio views.</span>
                        <button type="submit" class="btn btn-gold btn-lg fw-bold text-black px-5 rounded-pill shadow-lg hover-scale" style="min-height: 54px; letter-spacing: 1px;">
                            SAVE PORTFOLIO CHANGES
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.hover-opacity-100 { opacity: 0; transition: opacity 0.3s ease-in-out; }
.gallery-item-card:hover .hover-opacity-100 { opacity: 1; }
.gallery-item-card:hover .rounded-4 { transform: translateY(-5px); border-color: var(--primary) !important; }
.bg-input { background: rgba(255, 255, 255, 0.03); }
.text-gold { color: var(--primary) !important; }
.hover-scale:hover { transform: scale(1.02); }
@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
</style>

<script>
    function previewPrimaryImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('primary_preview');
                const placeholder = document.getElementById('primary_preview_placeholder');
                
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function markItemForDeletion(id) {
        if (confirm('Are you sure you want to remove this portfolio piece?')) {
            const bin = document.getElementById('hidden-portfolio-deletions');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_portfolio_items[]';
            input.value = id;
            bin.appendChild(input);

            const card = document.getElementById('portfolio-item-card-' + id);
            if (card) {
                card.style.display = 'none';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const removeBtns = document.querySelectorAll('.remove-gallery-btn');
        const hiddenBin = document.getElementById('hidden-removals');

        removeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.gallery-item-card');
                const url = card.getAttribute('data-url');
                
                // Add hidden input to tell controller to remove it
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_gallery[]';
                input.value = url;
                hiddenBin.appendChild(input);

                // Hide the card
                card.style.display = 'none';
            });
        });
    });
</script>

<?php render_staff_layout_end(); ?>
