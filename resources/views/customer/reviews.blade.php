<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'reviews';
render_customer_layout_start($page, 'Client Reviews & Ratings');

function render_rating_stars($rating) {
    $html = '<div class="d-flex align-items-center gap-1">';
    for ($i = 1; $i <= 5; $i++) {
        $fill = 0;
        if ($rating >= $i) {
            $fill = 100;
        } elseif ($rating > $i - 1) {
            $fill = ($rating - ($i - 1)) * 100;
        }
        $html .= '<div style="position: relative; font-size: 1.15rem; line-height: 1; display: inline-block;">';
        $html .= '<span style="color: rgba(255,255,255,0.15);">★</span>';
        $html .= '<span style="position: absolute; top: 0; left: 0; color: var(--primary); overflow: hidden; width: ' . $fill . '%; white-space: nowrap;">★</span>';
        $html .= '</div>';
    }
    $html .= '<span class="ms-1 fw-bold text-gold small" style="font-size:0.85rem;">' . number_format($rating, 1) . '</span>';
    $html .= '</div>';
    return $html;
}
?>

<div class="p-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="page-title mb-1">Client Reviews & Ratings</h2>
            <p class="page-subtitle mb-0">Share your experience, rate completed sessions, and view your verified feedback.</p>
        </div>
        <button class="btn btn-gold btn-sm px-4 py-2 fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#newReviewCard" aria-expanded="{{ ($selectedArtistId || $pendingReviews->isNotEmpty()) ? 'true' : 'false' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;margin-right:6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Write a Review
        </button>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="alert alert-success bg-dark-subtle border-success text-success mb-4 rounded-4 small fw-bold shadow-sm d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger bg-dark-subtle border-danger text-danger mb-4 rounded-4 small fw-bold shadow-sm d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger bg-dark-subtle border-danger text-danger mb-4 rounded-4 small fw-bold shadow-sm">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Awaiting Review Section (Pending Completed Appointments) -->
    @if($pendingReviews->isNotEmpty())
    <div class="card p-4 mb-5 border-0 shadow-lg position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(212,175,55,0.12) 0%, rgba(20,20,20,0.95) 100%); border: 1px solid rgba(212,175,55,0.4) !important; border-radius: 24px;">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <span class="badge bg-gold text-dark fw-bold rounded-pill px-3 py-1 mb-2" style="font-size:0.75rem; letter-spacing:1px;">
                    ⭐ AWAITING YOUR FEEDBACK
                </span>
                <h4 class="fw-bold mb-0 text-white" style="font-size: 1.4rem;">You have {{ $pendingReviews->count() }} completed session{{ $pendingReviews->count() > 1 ? 's' : '' }} ready to review</h4>
            </div>
            <span class="text-muted small">Help your specialist by leaving a quick star rating!</span>
        </div>

        <div class="row g-3 mt-1">
            @foreach($pendingReviews as $appt)
            <div class="col-md-6 col-lg-4">
                <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.08);">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-gold-subtle text-gold px-2 py-1" style="font-size:0.7rem;">{{ $appt->artist->category ?? 'Specialist' }}</span>
                            <span class="text-gold fw-bold small">₱{{ number_format($appt->total_price, 2) }}</span>
                        </div>
                        <h6 class="fw-bold text-white mb-1" style="font-size:1.05rem;">{{ $appt->service->name ?? 'Specialist Service' }}</h6>
                        <div class="text-muted small mb-3">
                            Specialist: <strong class="text-white">{{ $appt->artist->name ?? 'Artist' }}</strong>
                            <br>
                            Date: {{ date('M j, Y', strtotime($appt->appointment_date)) }} • {{ date('g:i A', strtotime($appt->appointment_time)) }}
                        </div>
                    </div>
                    <button type="button" class="btn btn-gold btn-sm w-100 fw-bold py-2" onclick="selectPendingAppointment({{ $appt->id }}, {{ $appt->artist_id }}, '{{ addslashes($appt->artist->name ?? 'Artist') }}', '{{ addslashes($appt->service->name ?? 'Service') }}')">
                        ★ Rate This Session
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Write / Submit Review Form Card (Collapsible) -->
    <div class="collapse {{ ($selectedArtistId || $pendingReviews->isNotEmpty()) ? 'show' : '' }} mb-5" id="newReviewCard">
        <div class="glass-card p-4 p-md-5 mx-auto" style="border-radius: 24px; border: 1px solid rgba(212,175,55,0.3); background: rgba(18,18,18,0.95); max-width: 800px;">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-1 text-gold" style="font-family: var(--font-montserrat);">Leave a Specialist Review</h4>
                    <p class="small text-muted mb-0">Your honest rating and review helps other clients and supports our artists.</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-toggle="collapse" data-bs-target="#newReviewCard" aria-label="Close"></button>
            </div>

            <!-- Session Context Indicator (Visible if reviewing a specific appointment) -->
            <div id="session_context_banner" class="alert p-3 mb-4 rounded-3 align-items-center justify-content-between" style="display: none; background: rgba(212,175,55,0.1); border: 1px dashed var(--primary); color: white;">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:1.2rem;">✂️</span>
                    <div>
                        <div class="small fw-bold text-gold" id="session_context_title">Linked Appointment Session</div>
                        <div class="small text-white" id="session_context_text"></div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-light py-1 px-2" style="font-size:0.75rem;" onclick="clearSessionContext()">✕ Unlink Session</button>
            </div>

            <form action="{{ route('customer.reviews.store') }}" method="POST" id="submitReviewForm">
                @csrf
                <input type="hidden" name="appointment_id" id="review_appointment_id" value="">
                <input type="hidden" name="rating" id="review_rating" value="5">

                <!-- Artist Selection -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase text-muted-foreground">Select Artist / Specialist <span class="text-danger">*</span></label>
                    <select name="artist_id" id="review_artist_id" class="form-select py-3 px-3" required style="background: var(--input); border: 1px solid var(--border); color: white; border-radius: 12px;">
                        <option value="" disabled {{ !$selectedArtistId ? 'selected' : '' }}>Select the artist you wish to review...</option>
                        @foreach($artists as $art)
                            <option value="{{ $art->id }}" {{ (string)$selectedArtistId === (string)$art->id ? 'selected' : '' }}>
                                {{ $art->name }} ({{ $art->category ?: 'Specialist' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Star Rating Picker (Full & Half Stars) -->
                <div class="mb-4 text-center p-4 rounded-4" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05);">
                    <label class="form-label small fw-bold text-uppercase text-muted-foreground d-block mb-3">Overall Rating <span class="text-danger">*</span></label>
                    
                    <div class="position-relative d-inline-block mb-2" style="user-select: none;">
                        <!-- 5 Star Boxes with Half-Star Fill -->
                        <div class="d-inline-flex gap-2 justify-content-center align-items-center" id="starPickerContainer">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="star-box position-relative d-inline-block" id="star_box_{{ $i }}" style="font-size: 2.8rem; line-height: 1; padding: 0 4px;">
                                <span style="color: rgba(255, 255, 255, 0.15);">★</span>
                                <span class="star-fill" id="star_fill_{{ $i }}" style="position: absolute; top: 0; left: 4px; color: var(--primary); overflow: hidden; width: 100%; white-space: nowrap; pointer-events: none; transition: width 0.15s ease;">★</span>
                            </div>
                            @endfor
                        </div>

                        <!-- Range Slider Overlay for Seamless Half-Star Click and Touch Sliders -->
                        <input type="range" min="0.5" max="5" step="0.5" value="5" id="ratingRange" 
                               style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;"
                               oninput="setRating(parseFloat(this.value))"
                               title="Click or drag to select whole or half stars">
                    </div>

                    <!-- Live Rating Score and Label -->
                    <div id="rating_text" class="fw-bold text-gold mb-3" style="font-size: 1.25rem; letter-spacing: 0.5px;">
                        5.0 - Exceptional!
                    </div>

                    <!-- Quick Half-Star Badges -->
                    <div class="d-flex justify-content-center flex-wrap gap-1 align-items-center">
                        <span class="small text-muted me-2" style="font-size:0.75rem;">Select:</span>
                        @foreach([1.0, 2.0, 3.0, 3.5, 4.0, 4.5, 5.0] as $preset)
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill preset-btn" 
                                id="preset_btn_{{ str_replace('.', '_', number_format($preset, 1)) }}"
                                style="font-size: 0.75rem; border-color: rgba(255,255,255,0.15); color: var(--muted-foreground);"
                                onclick="setRating({{ $preset }})">
                            {{ number_format($preset, 1) }} ★
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Comment Input -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase text-muted-foreground">Your Feedback & Comments</label>
                    <textarea name="comment" id="review_comment" class="form-control p-3" rows="3" placeholder="Tell us about your experience, styling results, atmosphere, or specialist..." style="background: var(--input); border: 1px solid var(--border); color: white; border-radius: 12px;"></textarea>
                    <div class="form-text small text-muted">Keep it constructive and respectful. Your review may be shown publicly on the shop portfolio.</div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-3 justify-content-end pt-2">
                    <button type="button" class="btn btn-muted px-4 py-2" data-bs-toggle="collapse" data-bs-target="#newReviewCard">Cancel</button>
                    <button type="submit" class="btn btn-gold px-5 py-2 fw-bold shadow-sm" id="btnPublishReview">
                        Publish Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- My Published Reviews Section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-white mb-0" style="font-family: var(--font-montserrat);">My Published Reviews</h4>
            <p class="small text-muted mb-0">Reviews you have shared with the Pedro's Artisan community.</p>
        </div>
        <span class="badge bg-dark border border-secondary text-white px-3 py-2 rounded-pill small">
            {{ $myReviews->count() }} Total Review{{ $myReviews->count() == 1 ? '' : 's' }}
        </span>
    </div>

    @if($myReviews->isNotEmpty())
    <div class="row g-4 mb-5">
        @foreach($myReviews as $r)
        <div class="col-md-6">
            <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between" style="border-radius: 20px; border: 1px solid rgba(255,255,255,0.08); background: rgba(18,18,18,0.7);">
                <div>
                    <!-- Review Top Bar -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            @if(!empty($r->artist->image_url))
                                <img src="{{ $r->artist->image_url }}" alt="{{ $r->artist->name }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--primary);">
                            @else
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(212,175,55,0.2); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700; border: 1.5px solid var(--primary);">
                                    {{ substr($r->artist->name ?? 'A', 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="fw-bold text-white mb-0" style="font-size: 1rem;">{{ $r->artist->name ?? 'Artisan Specialist' }}</h6>
                                <span class="badge bg-gold-subtle text-gold px-2 py-0 mt-1" style="font-size: 0.65rem;">
                                    {{ $r->artist->category ?? 'Specialist' }}
                                </span>
                            </div>
                        </div>

                        <!-- Delete Review Button -->
                        <form action="{{ route('customer.reviews.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?')" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger p-1 rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;" title="Delete Review">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            </button>
                        </form>
                    </div>

                    <!-- Stars & Date -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                        {!! render_rating_stars($r->rating) !!}
                        <div class="small text-muted" style="font-size: 0.78rem;">
                            {{ $r->created_at->format('M j, Y') }}
                        </div>
                    </div>

                    <!-- Session context tag if any -->
                    @if(!empty($r->appointment) && !empty($r->appointment->service))
                    <div class="mb-2">
                        <span class="small text-muted" style="font-size: 0.75rem;">
                            ✂️ Service: <strong class="text-white">{{ $r->appointment->service->name }}</strong>
                        </span>
                    </div>
                    @endif

                    <!-- Comment Body -->
                    <p class="small text-foreground mb-0" style="line-height: 1.6; font-style: italic;">
                        "{{ $r->comment ?: 'No written comment provided with this rating.' }}"
                    </p>
                </div>

                <!-- Footer Tag -->
                <div class="mt-3 pt-3 border-top border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small" style="font-size:0.68rem;">
                        ✓ Verified Client Review
                    </span>
                    <a href="{{ route('customer.artists.portfolio', $r->artist_id) }}" class="small text-gold text-decoration-none fw-bold" style="font-size:0.75rem;">
                        View Artist Portfolio →
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @elseif($pendingReviews->isEmpty())
    <!-- Empty State -->
    <div class="card p-5 text-center my-4 border-0 shadow-sm" style="background: rgba(18,18,18,0.5); border: 1px dashed rgba(255,255,255,0.1) !important; border-radius: 24px;">
        <div class="d-inline-flex p-3 rounded-circle mb-3 mx-auto" style="background: rgba(212,175,55,0.1); color: var(--primary);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:48px;height:48px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
        </div>
        <h4 class="fw-bold text-white mb-2">No Reviews Submitted Yet</h4>
        <p class="text-muted small mx-auto mb-4" style="max-width: 480px;">
            You haven't reviewed any artist sessions yet. Once you complete appointments or visit any of our creative specialists, you can share your rating and review here!
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-gold px-4 py-2 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#newReviewCard">
                Write a Review
            </button>
            <a href="{{ route('customer.book') }}" class="btn btn-outline-gold px-4 py-2 fw-bold">
                Book an Appointment
            </a>
        </div>
    </div>
    @endif
</div>

<style>
.star-btn:hover {
    transform: scale(1.2);
}
.bg-gold-subtle {
    background: rgba(212, 175, 55, 0.12);
    color: var(--primary);
}
</style>

<script>
let currentRating = 5.0;
const ratingLabels = {
    0.5: '0.5 - Very Poor',
    1.0: '1.0 - Poor',
    1.5: '1.5 - Disappointing',
    2.0: '2.0 - Below Average',
    2.5: '2.5 - Average',
    3.0: '3.0 - Satisfactory',
    3.5: '3.5 - Good Experience',
    4.0: '4.0 - Very Good',
    4.5: '4.5 - Outstanding!',
    5.0: '5.0 - Exceptional!'
};

function setRating(val) {
    currentRating = parseFloat(val);
    document.getElementById('review_rating').value = currentRating.toFixed(1);
    const range = document.getElementById('ratingRange');
    if (range) range.value = currentRating;

    updateStarUI(currentRating);

    const textElem = document.getElementById('rating_text');
    if (textElem) {
        textElem.textContent = ratingLabels[currentRating] || (currentRating.toFixed(1) + ' Stars');
    }

    // Highlight active preset badge
    const presets = [1.0, 2.0, 3.0, 3.5, 4.0, 4.5, 5.0];
    presets.forEach(p => {
        const btnId = 'preset_btn_' + p.toFixed(1).replace('.', '_');
        const btn = document.getElementById(btnId);
        if (btn) {
            if (p === currentRating) {
                btn.style.background = 'var(--primary)';
                btn.style.color = '#000';
                btn.style.fontWeight = '700';
                btn.style.borderColor = 'var(--primary)';
            } else {
                btn.style.background = 'transparent';
                btn.style.color = 'var(--muted-foreground)';
                btn.style.fontWeight = 'normal';
                btn.style.borderColor = 'rgba(255,255,255,0.15)';
            }
        }
    });
}

function updateStarUI(val) {
    for (let i = 1; i <= 5; i++) {
        const fill = document.getElementById('star_fill_' + i);
        if (fill) {
            if (val >= i) {
                fill.style.width = '100%';
            } else if (val >= i - 0.5) {
                fill.style.width = '50%';
            } else {
                fill.style.width = '0%';
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setRating(5.0);
});

function selectPendingAppointment(apptId, artistId, artistName, serviceName) {
    document.getElementById('review_appointment_id').value = apptId;
    const artistSelect = document.getElementById('review_artist_id');
    if (artistSelect) {
        artistSelect.value = artistId;
    }
    const banner = document.getElementById('session_context_banner');
    const label = document.getElementById('session_context_text');
    if (banner && label) {
        label.textContent = serviceName + ' with ' + artistName;
        banner.style.display = 'flex';
    }
    const formCard = document.getElementById('newReviewCard');
    if (formCard) {
        const collapse = bootstrap.Collapse.getOrCreateInstance(formCard);
        collapse.show();
        formCard.scrollIntoView({ behavior: 'smooth' });
    }
    document.getElementById('review_comment').focus();
}

function clearSessionContext() {
    document.getElementById('review_appointment_id').value = '';
    const banner = document.getElementById('session_context_banner');
    if (banner) {
        banner.style.display = 'none';
    }
}
</script>

<?php render_customer_layout_end(); ?>
