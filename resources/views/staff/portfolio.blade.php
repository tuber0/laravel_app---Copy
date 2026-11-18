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

    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="page-title mb-0" style="font-family: var(--font-bebas); letter-spacing: 2px; color: var(--primary); font-size: 3rem;">Manage Professional Portfolio</h1>
            <p class="text-muted-foreground small mb-0 fw-medium">Showcase your talent. Update your profile photo, bio, specialties, and gallery to stand out to clients.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success bg-success-subtle border-success border-opacity-25 text-success d-flex align-items-center gap-3 mb-4 p-3 rounded-4 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span class="fw-bold">{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger bg-danger-subtle border-danger border-opacity-25 text-danger d-flex align-items-center gap-3 mb-4 p-3 rounded-4 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        <span class="fw-bold">{{ session('error') }}</span>
    </div>
    @endif

    <div id="dynamic_form_alert" class="alert alert-danger bg-danger-subtle border-danger border-opacity-25 text-danger d-flex align-items-center gap-3 mb-4 p-3 rounded-4 shadow-sm" style="display: none !important;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        <span class="fw-bold" id="dynamic_form_alert_msg"></span>
    </div>

    @if($errors->any())
    <div class="alert alert-danger bg-danger-subtle border-danger border-opacity-25 text-danger mb-4 p-3 rounded-4 shadow-sm">
        <div class="fw-bold mb-1">Please correct the following errors:</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            @if($artist)
            <div class="glass-card p-4 p-md-5" style="border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); background: rgba(18,18,18,0.85);">
                <form action="{{ route('staff.portfolio.update', [], false) }}" method="POST" enctype="multipart/form-data" id="portfolioForm" onsubmit="handlePortfolioSubmit(event); return false;">
                    @csrf
                    
                    <!-- Profile Photo & Basic Specialties -->
                    <div class="row g-5 mb-5 align-items-center">
                        <div class="col-md-4 col-lg-3 text-center">
                            <div class="position-relative d-inline-block">
                                <div class="profile-preview-wrapper shadow-lg" style="width: 170px; height: 170px; border-radius: 50%; padding: 4px; background: linear-gradient(45deg, var(--primary), transparent); overflow: hidden;">
                                    @php
                                        $photoPath = $artist->profile_photo ?: $artist->image_url;
                                    @endphp
                                    @if($photoPath)
                                        @php
                                            $fp = storage_path('app/public/' . $photoPath);
                                            $v = file_exists($fp) ? filemtime($fp) : time();
                                            $previewSrc = \Illuminate\Support\Str::startsWith($photoPath, 'http') ? $photoPath : (asset('storage/' . $photoPath) . '?v=' . $v);
                                        @endphp
                                        <img id="primary_preview" src="{{ $previewSrc }}" alt="Profile Image" class="img-fluid rounded-circle object-fit-cover w-100 h-100 shadow" style="background: var(--input)">
                                        <div id="primary_preview_placeholder" class="rounded-circle bg-muted d-none align-items-center justify-content-center w-100 h-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:64px; color:var(--muted-foreground)"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                        </div>
                                    @else
                                        <div id="primary_preview_placeholder" class="rounded-circle bg-muted d-flex align-items-center justify-content-center w-100 h-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:64px; color:var(--muted-foreground)"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                        </div>
                                        <img id="primary_preview" src="" class="img-fluid rounded-circle object-fit-cover w-100 h-100 shadow d-none" style="background: var(--input)">
                                    @endif
                                </div>
                                <label for="primary_image_input" class="btn btn-gold btn-sm position-absolute bottom-0 end-0 rounded-circle p-2 shadow-lg" style="width: 40px; height: 40px; border: 2px solid #121212; cursor: pointer;" title="Upload photo file">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" /></svg>
                                </label>
                                <input type="file" id="primary_image_input" name="primary_image" class="d-none" accept="image/jpeg, image/png, image/jpg, image/webp, image/gif" onchange="previewPrimaryImage(this)">
                            </div>
                            <div class="mt-2 text-gold small fw-bold text-uppercase" style="letter-spacing: 1px;">Profile Photo</div>
                            <div class="mt-2">
                                <input type="url" name="primary_image_url" id="primary_image_url" class="form-control form-control-sm bg-dark border-secondary text-white text-center" placeholder="OR paste image URL (https://...)" oninput="previewPrimaryUrl(this.value)">
                            </div>
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

                    <!-- Portfolio Gallery Items Section -->
                    <div class="gallery-management-section mt-5 border-top border-secondary pt-5">
                        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                            <div>
                                <h4 class="mb-1 fw-bold text-white" style="font-family:var(--font-montserrat)">Portfolio Gallery Items</h4>
                                <p class="text-muted-foreground small mb-0">Structured gallery pieces showcasing your artisan work to clients.</p>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-gold px-4 py-2 fw-bold text-uppercase shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.75rem; letter-spacing: 1px; border-radius: 8px;" onclick="triggerGalleryFileInput()">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                    + Add Portfolio Photos
                                </button>
                                <button type="button" class="btn btn-outline-gold px-3 py-2 fw-bold text-uppercase d-inline-flex align-items-center gap-2" style="font-size: 0.75rem; letter-spacing: 1px; border-radius: 8px;" onclick="openUrlItemModal()">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" /></svg>
                                    + Add via URL
                                </button>
                                <input type="file" id="temp_gallery_input" class="d-none" accept="image/jpeg, image/png, image/jpg, image/webp, image/gif" multiple onchange="handleGalleryFilesSelected(this)">
                                <input type="file" id="gallery_images_input" name="gallery_images[]" class="d-none" accept="image/jpeg, image/png, image/jpg, image/webp, image/gif" multiple>
                            </div>
                        </div>

                        <!-- Pending Uploads Notification Banner -->
                        <div id="pending_alert_banner" class="alert alert-warning border-warning bg-dark-subtle text-warning mb-4 rounded-4 shadow-sm align-items-center justify-content-between p-3 flex-wrap gap-3" style="display: none;">
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:1.3rem;">📸</span>
                                <div>
                                    <strong class="text-gold" id="pending_alert_title">New photos ready to save!</strong>
                                    <div class="small opacity-75">Click <strong>"SAVE PORTFOLIO CHANGES"</strong> or the button on the right to publish your work samples.</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-gold text-dark fw-bold rounded-pill px-3 py-2" id="pending_alert_count">0 Photos Selected</span>
                                <button type="button" class="btn btn-save-portfolio btn-sm fw-bold px-4 rounded-pill shadow-sm" onclick="submitPortfolioForm()">SAVE NOW</button>
                            </div>
                        </div>

                        <!-- Combined Gallery Grid -->
                        <div class="row g-4 mb-4" id="gallery_grid_container">
                            <!-- Container for Newly Selected Local File Previews -->
                            <div id="pending_files_container" style="display: contents;"></div>

                            <!-- Container for Items Added via Image URL -->
                            <div id="pending_urls_container" style="display: contents;"></div>

                            <!-- Existing PortfolioItems from Database -->
                            @if($artist->portfolioItems && $artist->portfolioItems->count() > 0)
                                @foreach($artist->portfolioItems as $pItem)
                                <div class="col-sm-6 col-md-4 col-xl-3 existing-item-card" id="portfolio-item-card-{{ $pItem->id }}">
                                    <div class="card h-100 bg-dark border-secondary overflow-hidden shadow-sm" style="border-radius: 14px;">
                                        <div class="position-relative overflow-hidden" style="border-radius: 14px 14px 0 0;">
                                            <div class="ratio ratio-1x1 bg-black">
                                                @php
                                                    $itemFp = storage_path('app/public/' . $pItem->image_path);
                                                    $itemV = file_exists($itemFp) ? filemtime($itemFp) : time();
                                                    $itemImgSrc = \Illuminate\Support\Str::startsWith($pItem->image_path, 'http') ? $pItem->image_path : (asset('storage/' . $pItem->image_path) . '?v=' . $itemV);
                                                @endphp
                                                <img src="{{ $itemImgSrc }}" class="object-fit-cover w-100 h-100" alt="{{ $pItem->title }}">
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle p-1 shadow" style="width:28px;height:28px; display:flex; align-items:center; justify-content:center; z-index: 10; font-size: 16px; line-height: 1;" onclick="markItemForDeletion({{ $pItem->id }})" title="Remove photo">
                                                &times;
                                            </button>
                                        </div>
                                        <div class="p-3">
                                            <div class="fw-bold text-white small mb-1 text-truncate">{{ $pItem->title ?: 'Work Sample' }}</div>
                                            <div class="text-muted small text-truncate">{{ $pItem->caption ?: ($artist->category ? 'Specialty: ' . $artist->category : 'Portfolio Work') }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @elseif(is_array($artist->portfolio_images) && count($artist->portfolio_images) > 0)
                                @foreach($artist->portfolio_images as $index => $img)
                                <div class="col-6 col-sm-4 col-md-3 col-xl-2 gallery-item-card existing-item-card" data-url="{{ trim($img) }}">
                                    <div class="position-relative ratio ratio-1x1 rounded-4 overflow-hidden shadow-sm" style="border: 1px solid rgba(255,255,255,0.05); transition: transform 0.3s ease;">
                                        @php
                                            $cleanImg = trim($img);
                                            $imgFp = storage_path('app/public/' . $cleanImg);
                                            $imgV = file_exists($imgFp) ? filemtime($imgFp) : time();
                                            $imgSrc = \Illuminate\Support\Str::startsWith($cleanImg, 'http') ? $cleanImg : (asset('storage/' . $cleanImg) . '?v=' . $imgV);
                                        @endphp
                                        <img src="{{ $imgSrc }}" class="object-fit-cover w-100 h-100" alt="Portfolio Image">
                                        <div class="position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-black bg-opacity-60 overlay-action opacity-0 hover-opacity-100 transition" style="top:0; left:0; backdrop-filter: blur(2px);">
                                            <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-4 remove-gallery-btn" style="font-size: 0.7rem; letter-spacing: 1px;">
                                                REMOVE
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Empty State Notice (shown when 0 items exist & 0 pending) with Drag & Drop -->
                        <div class="col-12 text-center py-5 rounded-4 transition dropzone-area" id="empty_gallery_state" style="background: rgba(255,255,255,0.02); border: 2px dashed rgba(255,255,255,0.12); cursor: pointer; {{ (($artist->portfolioItems && $artist->portfolioItems->count() > 0) || (is_array($artist->portfolio_images) && count($artist->portfolio_images) > 0)) ? 'display: none;' : '' }}" onclick="triggerGalleryFileInput()">
                            <div class="mb-3 opacity-50">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:58px; height:58px; color:var(--primary);"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                            </div>
                            <h5 class="fw-bold text-white mb-2">No Gallery Photos Yet</h5>
                            <p class="text-muted small fw-medium mb-3">Click here or drag & drop photo files to upload your artisan work samples.</p>
                            <button type="button" class="btn btn-gold btn-sm px-4 py-2 fw-bold text-uppercase" style="letter-spacing: 0.5px;" onclick="event.stopPropagation(); triggerGalleryFileInput();">
                                Upload Photos Now
                            </button>
                        </div>
                    </div>

                    <!-- Hidden container to hold removed URLs & Item IDs -->
                    <div id="hidden-removals"></div>
                    <div id="hidden-portfolio-deletions"></div>

                    <!-- Sticky Bottom Submit Action Bar -->
                    <div class="submit-area sticky-bottom p-4 mt-5 d-flex align-items-center justify-content-between flex-wrap gap-3" style="bottom: 20px; z-index: 100;">
                        <div style="max-width: 650px;">
                            <div class="fw-bold text-gold text-uppercase mb-1" style="font-size: 0.95rem; letter-spacing: 1.5px; font-family: var(--font-montserrat);">Publish & Sync Changes</div>
                            <div class="text-white-50 small" style="font-size: 0.85rem; line-height: 1.5;">All gallery photos and profile updates sync immediately with customer bookings and artist portfolio pages.</div>
                            
                            <!-- Live Upload Progress Bar -->
                            <div id="saveProgressBarContainer" class="w-100 mt-3" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span id="saveProgressLabel" class="small fw-bold text-gold" style="font-size: 0.78rem;">Uploading photos (0%)...</span>
                                    <span id="saveProgressPercent" class="small fw-bold text-white" style="font-size: 0.78rem;">0%</span>
                                </div>
                                <div class="progress" style="height: 7px; background: rgba(255,255,255,0.12); border-radius: 99px; overflow: hidden;">
                                    <div id="saveProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #FFD700, #FFA500) !important;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" id="btnSavePortfolio" class="btn btn-save-portfolio btn-lg fw-bold px-5 rounded-pill shadow-lg" style="min-height: 52px; letter-spacing: 1px; font-size: 0.95rem;">
                                SAVE PORTFOLIO CHANGES
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Add Photo by Image URL -->
<div class="modal fade" id="addUrlPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0" style="background: rgba(26,26,26,0.98); border: 1px solid rgba(212,175,55,0.3) !important; border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-gold" style="font-family: var(--font-montserrat);">Add Portfolio Photo via URL</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase text-muted-foreground">Image URL <span class="text-danger">*</span></label>
                    <input type="url" id="modal_photo_url" class="form-control bg-dark border-secondary text-white py-2" placeholder="https://images.unsplash.com/photo-..." required>
                    <div class="form-text small text-muted">Paste a direct link to an image (JPG, PNG, WebP).</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase text-muted-foreground">Title</label>
                    <input type="text" id="modal_photo_title" class="form-control bg-dark border-secondary text-white py-2" placeholder="e.g. Clean Taper Fade">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase text-muted-foreground">Caption (Optional)</label>
                    <input type="text" id="modal_photo_caption" class="form-control bg-dark border-secondary text-white py-2" placeholder="e.g. Styled with matte pomade">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-muted px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-gold px-4 fw-bold" onclick="confirmAddUrlPhoto()">Add Photo</button>
            </div>
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
#pending_files_container, #pending_urls_container { display: contents; }
.dropzone-area.drag-over {
    border-color: var(--primary) !important;
    background: rgba(212, 175, 55, 0.08) !important;
}
/* Sticky Action Bar & High-Contrast Save Buttons */
.submit-area {
    background: rgba(18, 18, 18, 0.98) !important;
    border: 1.5px solid rgba(212, 175, 55, 0.5) !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.85), 0 0 25px rgba(212, 175, 55, 0.25) !important;
    border-radius: 18px !important;
}

.btn-save-portfolio {
    background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%) !important;
    color: #000000 !important;
    font-weight: 800 !important;
    border: none !important;
    box-shadow: 0 4px 18px rgba(212, 175, 55, 0.45) !important;
    transition: all 0.25s ease-in-out !important;
}

.btn-save-portfolio:hover {
    background: linear-gradient(135deg, #FFE44D 0%, #E5C060 100%) !important;
    color: #000000 !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.65) !important;
}

/* Disabled & Loading state: MUST remain bright gold with crisp, dark, 100% readable text */
.btn-save-portfolio:disabled,
.btn-save-portfolio.is-saving,
#btnSavePortfolio:disabled {
    background: linear-gradient(135deg, #FFE873 0%, #E2BE50 100%) !important;
    color: #000000 !important;
    opacity: 1 !important;
    cursor: wait !important;
    border: 2px solid #FFFFFF !important;
    box-shadow: 0 0 25px rgba(255, 215, 0, 0.6) !important;
    transform: none !important;
}

.btn-save-portfolio:disabled .spinner-border,
#btnSavePortfolio:disabled .spinner-border {
    color: #000000 !important;
    border-color: #000000 !important;
    border-right-color: transparent !important;
}
@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
</style>

<script>
    // ─── Primary Profile Photo Previews ───
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

    function previewPrimaryUrl(url) {
        if (url && url.trim().length > 5) {
            const preview = document.getElementById('primary_preview');
            const placeholder = document.getElementById('primary_preview_placeholder');
            preview.src = url.trim();
            preview.classList.remove('d-none');
            if (placeholder) placeholder.classList.add('d-none');
        }
    }

    // ─── Multiple Gallery File Upload Queue ───
    let pendingGalleryFiles = [];

    function triggerGalleryFileInput() {
        const temp = document.getElementById('temp_gallery_input');
        if (temp) {
            temp.value = '';
            temp.click();
        } else {
            document.getElementById('gallery_images_input').click();
        }
    }

    function addFilesToQueue(fileList) {
        if (!fileList || fileList.length === 0) return;
        saveCurrentPendingInputValues();

        for (let i = 0; i < fileList.length; i++) {
            const file = fileList[i];
            if (!file.type || file.type.startsWith('image/') || /\.(jpe?g|png|webp|gif)$/i.test(file.name)) {
                // Initialize previewSrc with blob URL if available
                try {
                    file.previewSrc = URL.createObjectURL(file);
                } catch (e) {
                    file.previewSrc = '';
                }

                // Also read as DataURL to ensure 100% CSP and browser compatibility
                const reader = new FileReader();
                reader.onload = function(e) {
                    file.previewSrc = e.target.result;
                    if (file._imgId) {
                        const img = document.getElementById(file._imgId);
                        if (img) img.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);

                pendingGalleryFiles.push(file);
            }
        }
        syncGalleryFilesInput();
        renderPendingGalleryCards();
    }

    function handleGalleryFilesSelected(input) {
        if (input.files && input.files.length > 0) {
            addFilesToQueue(input.files);
        }
    }

    function saveCurrentPendingInputValues() {
        const titleInputs = document.querySelectorAll('#pending_files_container input[name="gallery_titles[]"]');
        const captionInputs = document.querySelectorAll('#pending_files_container input[name="gallery_captions[]"]');
        titleInputs.forEach((input, i) => {
            if (pendingGalleryFiles[i]) {
                pendingGalleryFiles[i].customTitle = input.value;
            }
        });
        captionInputs.forEach((input, i) => {
            if (pendingGalleryFiles[i]) {
                pendingGalleryFiles[i].customCaption = input.value;
            }
        });
    }

    function removePendingGalleryFile(index) {
        saveCurrentPendingInputValues();
        pendingGalleryFiles.splice(index, 1);
        syncGalleryFilesInput();
        renderPendingGalleryCards();
    }

    function syncGalleryFilesInput() {
        try {
            const dt = new DataTransfer();
            pendingGalleryFiles.forEach(file => dt.items.add(file));
            document.getElementById('gallery_images_input').files = dt.files;
        } catch (e) {
            console.warn('DataTransfer sync warning:', e);
        }
    }

    function renderPendingGalleryCards() {
        const container = document.getElementById('pending_files_container');
        container.innerHTML = '';

        pendingGalleryFiles.forEach((file, idx) => {
            const imgId = 'pending_img_' + idx;
            file._imgId = imgId;

            const imgSrc = file.previewSrc || (window.URL ? URL.createObjectURL(file) : '');
            const cardId = 'pending_card_' + idx;
            const defaultTitle = file.customTitle !== undefined 
                ? file.customTitle 
                : file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' ');
            const captionVal = file.customCaption !== undefined ? file.customCaption : '';

            const card = document.createElement('div');
            card.className = 'col-sm-6 col-md-4 col-xl-3';
            card.id = cardId;
            card.innerHTML = `
                <div class="card h-100 bg-dark overflow-hidden shadow" style="border: 1px solid var(--primary) !important; border-radius: 14px;">
                    <div class="position-relative overflow-hidden" style="border-radius: 14px 14px 0 0;">
                        <div class="ratio ratio-1x1 bg-black">
                            <img id="${imgId}" src="${imgSrc}" class="object-fit-cover w-100 h-100" alt="Work Preview" style="display: block;">
                        </div>
                        <span class="badge bg-gold text-dark fw-bold position-absolute top-0 start-0 m-2 shadow" style="font-size:0.65rem; letter-spacing:0.5px; z-index: 10;">NEW PHOTO</span>
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle p-1 shadow" style="width:28px;height:28px; display:flex; align-items:center; justify-content:center; z-index: 10; font-size: 16px; line-height: 1;" onclick="removePendingGalleryFile(${idx})" title="Remove photo">
                            &times;
                        </button>
                    </div>
                    <div class="p-3">
                        <label class="small text-muted mb-1" style="font-size:0.75rem;">Photo Title</label>
                        <input type="text" name="gallery_titles[]" class="form-control form-control-sm bg-input border-secondary text-white mb-2" placeholder="Title (e.g. Skin Fade)" value="${defaultTitle}">
                        <label class="small text-muted mb-1" style="font-size:0.75rem;">Caption (optional)</label>
                        <input type="text" name="gallery_captions[]" class="form-control form-control-sm bg-input border-secondary text-white" placeholder="Caption (optional)" value="${captionVal}">
                    </div>
                </div>
            `;
            container.appendChild(card);
        });

        updateGalleryStatus();
    }

    // ─── Add via URL Modal ───
    function openUrlItemModal() {
        document.getElementById('modal_photo_url').value = '';
        document.getElementById('modal_photo_title').value = '';
        document.getElementById('modal_photo_caption').value = '';
        new bootstrap.Modal(document.getElementById('addUrlPhotoModal')).show();
    }

    function confirmAddUrlPhoto() {
        const urlInput = document.getElementById('modal_photo_url');
        const url = urlInput.value.trim();
        if (!url) {
            urlInput.focus();
            return;
        }

        const title = document.getElementById('modal_photo_title').value.trim();
        const caption = document.getElementById('modal_photo_caption').value.trim();

        const container = document.getElementById('pending_urls_container');
        const id = 'url_card_' + Date.now();

        const col = document.createElement('div');
        col.className = 'col-sm-6 col-md-4 col-xl-3';
        col.id = id;
        col.innerHTML = `
            <div class="card h-100 bg-dark overflow-hidden shadow" style="border: 1px solid var(--primary) !important; border-radius: 14px;">
                <div class="position-relative overflow-hidden" style="border-radius: 14px 14px 0 0;">
                    <div class="ratio ratio-1x1 bg-black">
                        <img src="${url}" class="object-fit-cover w-100 h-100" onerror="this.src='https://placehold.co/400x400/222/D4AF37?text=Invalid+Image+URL'" alt="URL Preview" style="display: block;">
                    </div>
                    <span class="badge bg-gold text-dark fw-bold position-absolute top-0 start-0 m-2 shadow" style="font-size:0.65rem; z-index: 10;">NEW (URL)</span>
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle p-1 shadow" style="width:28px;height:28px; display:flex; align-items:center; justify-content:center; z-index: 10; font-size: 16px; line-height: 1;" onclick="document.getElementById('${id}').remove(); updateGalleryStatus();" title="Remove photo">
                        &times;
                    </button>
                </div>
                <div class="p-3">
                    <input type="hidden" name="gallery_urls[]" value="${url}">
                    <label class="small text-muted mb-1" style="font-size:0.75rem;">Photo Title</label>
                    <input type="text" name="gallery_url_titles[]" class="form-control form-control-sm bg-input border-secondary text-white mb-2" placeholder="Title (e.g. Hair Tattoo)" value="${title}">
                    <label class="small text-muted mb-1" style="font-size:0.75rem;">Caption (optional)</label>
                    <input type="text" name="gallery_url_captions[]" class="form-control form-control-sm bg-input border-secondary text-white" placeholder="Caption (optional)" value="${caption}">
                </div>
            </div>
        `;
        container.appendChild(col);

        bootstrap.Modal.getInstance(document.getElementById('addUrlPhotoModal')).hide();
        updateGalleryStatus();
    }

    // ─── Mark Existing Item for Deletion ───
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
                card.remove();
            }
            updateGalleryStatus();
        }
    }

    // ─── Update Visual State and Counter ───
    function updateGalleryStatus() {
        const pendingFileCount = pendingGalleryFiles.length;
        const pendingUrlCount = document.querySelectorAll('#pending_urls_container > div').length;
        const totalPending = pendingFileCount + pendingUrlCount;

        const existingCount = document.querySelectorAll('.existing-item-card').length;
        const totalTotal = totalPending + existingCount;

        const banner = document.getElementById('pending_alert_banner');
        const countBadge = document.getElementById('pending_alert_count');
        const emptyState = document.getElementById('empty_gallery_state');

        if (totalPending > 0) {
            banner.style.display = 'flex';
            countBadge.textContent = totalPending + ' New Photo' + (totalPending > 1 ? 's' : '') + ' Ready';
        } else {
            banner.style.display = 'none';
        }

        if (totalTotal === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }

    // ─── CSRF Token Management & Keepalive ───
    async function getFreshCsrfToken() {
        try {
            const endpoint = '{{ route("csrf.token", [], false) }}';
            const res = await fetch(endpoint, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (res.ok) {
                const data = await res.json();
                if (data && data.token) {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) meta.content = data.token;
                    document.querySelectorAll('input[name="_token"]').forEach(input => input.value = data.token);
                    return data.token;
                }
            }
        } catch (e) {
            console.warn('Could not refresh CSRF token:', e);
        }
        return document.querySelector('input[name="_token"]')?.value || '';
    }

    // Ping every 3 minutes to keep PHP session continuously active
    setInterval(getFreshCsrfToken, 3 * 60 * 1000);

    function showFormError(message) {
        const alertBox = document.getElementById('dynamic_form_alert');
        const alertMsg = document.getElementById('dynamic_form_alert_msg');
        if (alertBox && alertMsg) {
            alertMsg.innerHTML = message;
            alertBox.style.setProperty('display', 'flex', 'important');
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alert(message.replace(/<[^>]*>/g, ''));
        }
    }

    function clearFormError() {
        const alertBox = document.getElementById('dynamic_form_alert');
        if (alertBox) {
            alertBox.style.setProperty('display', 'none', 'important');
        }
    }

    function submitPortfolioForm() {
        handlePortfolioSubmit();
    }

    async function handlePortfolioSubmit(e) {
        if (e && e.preventDefault) e.preventDefault();
        clearFormError();

        // 1. Validate pending file sizes
        let totalBytes = 0;
        for (let i = 0; i < pendingGalleryFiles.length; i++) {
            const f = pendingGalleryFiles[i];
            if (f.size > 20 * 1024 * 1024) {
                showFormError(`Photo <strong>"${f.name}"</strong> is too large (${(f.size / (1024 * 1024)).toFixed(1)}MB). The maximum allowed size per photo is 20MB. Please select a smaller photo.`);
                return;
            }
            totalBytes += f.size;
        }

        const primaryInput = document.getElementById('primary_image_input');
        if (primaryInput && primaryInput.files && primaryInput.files[0]) {
            const pf = primaryInput.files[0];
            if (pf.size > 20 * 1024 * 1024) {
                showFormError(`Profile photo <strong>"${pf.name}"</strong> is too large (${(pf.size / (1024 * 1024)).toFixed(1)}MB). Max allowed size is 20MB.`);
                return;
            }
            totalBytes += pf.size;
        }

        if (totalBytes > 45 * 1024 * 1024) {
            showFormError(`The total size of your selected photos is <strong>${(totalBytes / (1024 * 1024)).toFixed(1)}MB</strong>. To avoid timeouts and server limits, please upload in batches of 40MB or fewer.`);
            return;
        }

        // 2. Save current title and caption values
        saveCurrentPendingInputValues();

        // 3. Update button & progress bar to loading state
        const btn = document.getElementById('btnSavePortfolio');
        const originalBtnHtml = btn ? btn.innerHTML : 'SAVE PORTFOLIO CHANGES';
        const progressContainer = document.getElementById('saveProgressBarContainer');
        const progressBar = document.getElementById('saveProgressBar');
        const progressLabel = document.getElementById('saveProgressLabel');
        const progressPercent = document.getElementById('saveProgressPercent');

        if (progressContainer) {
            progressContainer.style.display = 'block';
            if (progressBar) progressBar.style.width = '0%';
            if (progressLabel) progressLabel.textContent = 'Securing session & preparing upload...';
            if (progressPercent) progressPercent.textContent = '0%';
        }

        if (btn) {
            btn.disabled = true;
            btn.classList.add('is-saving');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2 text-dark" style="border-width: 2.5px;" role="status"></span> SECURING & UPLOADING...';
        }

        // 4. Refresh token before sending to guarantee 100% validity
        const freshToken = await getFreshCsrfToken();

        // 5. Build FormData directly
        const form = document.getElementById('portfolioForm');
        const formData = new FormData(form);

        // Ensure token is set
        formData.set('_token', freshToken);

        // Append pending gallery files directly from memory queue
        if (pendingGalleryFiles.length > 0) {
            formData.delete('gallery_images[]');
            formData.delete('gallery_titles[]');
            formData.delete('gallery_captions[]');

            pendingGalleryFiles.forEach((file) => {
                formData.append('gallery_images[]', file);
                const titleVal = file.customTitle !== undefined ? file.customTitle : file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' ');
                const captionVal = file.customCaption !== undefined ? file.customCaption : '';
                formData.append('gallery_titles[]', titleVal);
                formData.append('gallery_captions[]', captionVal);
            });
        }

        // 6. Execute Upload via XMLHttpRequest with progress & auto-retry on 419
        function executeUpload(tokenToUse, isRetry = false) {
            const xhr = new XMLHttpRequest();
            const actionUrl = form.getAttribute('action') || '{{ route("staff.portfolio.update", [], false) }}';
            xhr.open('POST', actionUrl, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', tokenToUse);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.onprogress = function(evt) {
                if (evt.lengthComputable) {
                    const percent = Math.round((evt.loaded / evt.total) * 100);
                    if (progressContainer) progressContainer.style.display = 'block';
                    if (progressBar) progressBar.style.width = percent + '%';
                    if (progressPercent) progressPercent.textContent = percent + '%';

                    if (btn) {
                        if (percent < 100) {
                            if (progressLabel) progressLabel.textContent = `Uploading photos (${percent}%)...`;
                            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2 text-dark" style="border-width: 2.5px;" role="status"></span> UPLOADING (${percent}%)...`;
                        } else {
                            if (progressLabel) progressLabel.textContent = 'Processing & syncing changes on server...';
                            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2 text-dark" style="border-width: 2.5px;" role="status"></span> PROCESSING & SYNCING...`;
                        }
                    }
                }
            };

            xhr.onload = async function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    if (progressLabel) progressLabel.textContent = 'Upload complete! Syncing portfolio...';
                    if (progressBar) progressBar.style.width = '100%';
                    if (btn) {
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2 text-dark" style="border-width: 2.5px;" role="status"></span> SAVED! RELOADING...';
                    }
                    window.location.reload();
                    return;
                }

                // 419 Session / CSRF Token Expired -> Auto-retry ONCE with fresh token!
                if (xhr.status === 419 && !isRetry) {
                    if (btn) {
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2 text-dark" style="border-width: 2.5px;" role="status"></span> REFRESHING SESSION...';
                    }
                    const retriedToken = await getFreshCsrfToken();
                    formData.set('_token', retriedToken);
                    executeUpload(retriedToken, true);
                    return;
                }

                // Handle other errors (422 validation, 413 file size, 500, etc.)
                let errMsg = 'An unexpected error occurred while saving. Please try again.';
                try {
                    const errData = JSON.parse(xhr.responseText);
                    if (errData.message) {
                        errMsg = errData.message;
                    }
                    if (errData.errors) {
                        const errList = Object.values(errData.errors).flat().join('<br>');
                        if (errList) errMsg = errList;
                    }
                } catch (err) {
                    if (xhr.status === 413) {
                        errMsg = 'Uploaded files are too large for the server. Please upload smaller or compressed photos.';
                    } else if (xhr.status === 419) {
                        errMsg = 'Your session expired. Please refresh the page or try clicking Save again.';
                    }
                }

                if (progressContainer) {
                    progressContainer.style.display = 'none';
                }

                showFormError(errMsg);
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('is-saving');
                    btn.innerHTML = originalBtnHtml;
                }
            };

            xhr.onerror = function() {
                if (progressContainer) {
                    progressContainer.style.display = 'none';
                }
                showFormError('Network connection error while uploading. Please check your connection and try again.');
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('is-saving');
                    btn.innerHTML = originalBtnHtml;
                }
            };

            xhr.send(formData);
        }

        executeUpload(freshToken, false);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Setup remove buttons for legacy portfolio images
        const removeBtns = document.querySelectorAll('.remove-gallery-btn');
        const hiddenBin = document.getElementById('hidden-removals');

        removeBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const card = this.closest('.gallery-item-card');
                const url = card.getAttribute('data-url');
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_gallery[]';
                input.value = url;
                hiddenBin.appendChild(input);

                card.remove();
                updateGalleryStatus();
            });
        });

        // Setup Drag & Drop on Empty State Area
        const dropzone = document.getElementById('empty_gallery_state');
        if (dropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('drag-over');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('drag-over');
                }, false);
            });

            dropzone.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    addFilesToQueue(dt.files);
                }
            }, false);
        }

        // Attach AJAX submission handler to form
        const form = document.getElementById('portfolioForm');
        if (form) {
            form.addEventListener('submit', handlePortfolioSubmit);
        }
    });
</script>

<?php render_staff_layout_end(); ?>
