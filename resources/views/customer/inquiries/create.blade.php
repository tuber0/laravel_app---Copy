<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'inquiries';
render_customer_layout_start($page, 'Submit Custom Inquiry');
?>

<div class="container-fluid py-4 px-md-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= route('customer.inquiries.index') ?>" class="btn btn-sm btn-outline-secondary me-3" style="border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; padding: 0;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="h3 fw-bold text-white mb-0">Submit Custom Inquiry</h1>
            <p class="text-muted small mb-0">Provide details, reference images, and receive a formal price quote before booking.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-white mb-4" style="border-radius: 12px;">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card p-4 p-md-5" style="border-radius: 20px; border: 1px solid var(--border); background: rgba(255,255,255,0.02);">
                <form action="<?= route('customer.inquiries.store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Artist Selection -->
                    <div class="mb-4">
                        <label class="form-label text-gold text-uppercase fw-bold small" style="letter-spacing: 1px;">1. Select Artist / Artisan <span class="text-danger">*</span></label>
                        <select name="artist_id" id="artist_id" class="form-select bg-dark text-white border-secondary" required style="border-radius: 10px; padding: 0.75rem 1rem;">
                            <option value="" disabled <?= empty($selectedArtistId) ? 'selected' : '' ?>>-- Choose an Artist --</option>
                            @foreach($artists as $artist)
                                <option value="{{ $artist->id }}" <?= $selectedArtistId == $artist->id ? 'selected' : '' ?>>
                                    {{ $artist->name }} ({{ $artist->category }} &bull; Starting: ₱{{ number_format($artist->starting_rate ?? 300, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">The quotation will be personally crafted and assessed by this artist.</div>
                    </div>

                    <!-- Service Type / Category -->
                    <div class="mb-4">
                        <label class="form-label text-gold text-uppercase fw-bold small" style="letter-spacing: 1px;">2. Service Type <span class="text-danger">*</span></label>
                        <div class="row g-2 mb-2">
                            <div class="col-4">
                                <label class="w-100 p-2 text-center border rounded-3 service-type-pill text-white cursor-pointer" style="border-color: rgba(255,255,255,0.15) !important;">
                                    <input type="radio" name="service_type" value="Custom Tattoo" class="d-none" required checked>
                                    <span class="d-block fw-bold small">💉 Tattoo</span>
                                </label>
                            </div>
                            <div class="col-4">
                                <label class="w-100 p-2 text-center border rounded-3 service-type-pill text-white cursor-pointer" style="border-color: rgba(255,255,255,0.15) !important;">
                                    <input type="radio" name="service_type" value="Body Piercing" class="d-none">
                                    <span class="d-block fw-bold small">💎 Piercing</span>
                                </label>
                            </div>
                            <div class="col-4">
                                <label class="w-100 p-2 text-center border rounded-3 service-type-pill text-white cursor-pointer" style="border-color: rgba(255,255,255,0.15) !important;">
                                    <input type="radio" name="service_type" value="Barber & Styling" class="d-none">
                                    <span class="d-block fw-bold small">✂️ Barber</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="form-label text-gold text-uppercase fw-bold small" style="letter-spacing: 1px;">3. Project Description & Placement Details <span class="text-danger">*</span></label>
                        <textarea name="description" rows="5" class="form-control bg-dark text-white border-secondary" required placeholder="Describe your concept, approximate dimensions (e.g. 4x4 inches on forearm), color palette preferences, or specific haircut/piercing requirements..." style="border-radius: 10px; padding: 0.75rem 1rem;">{{ old('description') }}</textarea>
                        <div class="form-text text-muted">More details allow the artist to provide an accurate estimate of duration and cost.</div>
                    </div>

                    <!-- Reference Images -->
                    <div class="mb-4">
                        <label class="form-label text-gold text-uppercase fw-bold small" style="letter-spacing: 1px;">4. Reference Images / Inspiration (Optional)</label>
                        <div class="p-3 border border-dashed rounded-3 text-center" style="border-color: rgba(212, 175, 55, 0.4) !important; background: rgba(0,0,0,0.2);">
                            <input type="file" name="reference_images[]" id="reference_images" class="form-control bg-transparent text-white border-0" multiple accept="image/*" onchange="previewImages(event)">
                            <div class="text-muted small mt-2">You can select up to 5 images (JPG, PNG, WebP up to 5MB each).</div>
                        </div>
                        <div id="image-previews" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>

                    <!-- Preferred Date & Budget -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-gold text-uppercase fw-bold small" style="letter-spacing: 1px;">5. Preferred Date</label>
                            <input type="date" name="preferred_date" min="<?= date('Y-m-d') ?>" value="{{ old('preferred_date') }}" class="form-control bg-dark text-white border-secondary" style="border-radius: 10px; padding: 0.75rem 1rem;">
                            <div class="form-text text-muted">When would you ideally like to schedule this?</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-gold text-uppercase fw-bold small" style="letter-spacing: 1px;">6. Estimated Budget (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-secondary bg-opacity-25 text-gold border-secondary">₱</span>
                                <input type="number" step="50" min="0" name="budget" value="{{ old('budget') }}" placeholder="e.g. 2500" class="form-control bg-dark text-white border-secondary" style="border-radius: 0 10px 10px 0; padding: 0.75rem 1rem;">
                            </div>
                            <div class="form-text text-muted">Helps the artist recommend sizing and detail complexity.</div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-3 pt-3 border-top border-secondary border-opacity-25">
                        <a href="<?= route('customer.inquiries.index') ?>" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 10px;">Cancel</a>
                        <button type="submit" class="btn btn-gold px-5 py-2 fw-bold" style="border-radius: 10px; font-size: 1.05rem;">
                            Send Inquiry to Artist 🚀
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.service-type-pill {
    transition: all 0.2s ease;
    background: rgba(255,255,255,0.03);
}
.service-type-pill:hover {
    border-color: var(--primary) !important;
    background: rgba(212, 175, 55, 0.08);
}
.service-type-pill input:checked + span {
    color: var(--primary);
}
.service-type-pill:has(input:checked) {
    border-color: var(--primary) !important;
    background: rgba(212, 175, 55, 0.15);
}
</style>

<script>
function previewImages(event) {
    const container = document.getElementById('image-previews');
    container.innerHTML = '';
    const files = event.target.files;
    if (files) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '70px';
                img.style.height = '70px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '8px';
                img.style.border = '1px solid var(--primary)';
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    }
}
</script>

<?php render_customer_layout_end(); ?>
