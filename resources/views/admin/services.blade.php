<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'services';
render_admin_layout_start($page, 'Services');

$cat_filter = request('cat', 'All');
$filtered = ($cat_filter === 'All') 
    ? $services 
    : $services->where('category', $cat_filter);

// Default categories if list is empty
$displayCategories = $categories->count() > 0 ? $categories : collect(['Barbering', 'Braiding', 'Tattoo', 'Piercing', 'Others']);
?>

<div class="dashboard-container">
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="page-title mb-0">Services Portfolio</h1>
            <p class="small text-muted-foreground mb-0">Control your catalog, pricing, and service durations.</p>
        </div>
        <button class="btn btn-gold px-4" data-bs-toggle="collapse" data-bs-target="#addServiceForm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;margin-right:8px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            NEW SERVICE
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success bg-dark-subtle border-success text-success mb-4 rounded-4 small fw-bold animate-fade-in shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Category Filter Bar -->
    <div class="d-flex gap-2 overflow-auto pb-3 mb-5 no-scrollbar">
        <a href="{{ route('admin.services.index') }}" class="day-btn {{ $cat_filter === 'All' ? 'active' : '' }}">ALL SERVICES</a>
        @foreach ($displayCategories as $cat)
        <a href="?cat={{ urlencode($cat) }}" class="day-btn {{ $cat_filter === $cat ? 'active' : '' }}">{{ strtoupper($cat) }}</a>
        @endforeach
    </div>

    <!-- Add Service Form -->
    <div class="collapse mb-5" id="addServiceForm">
        <div class="glass-card p-4 mx-auto" style="max-width: 900px; border-radius: 24px;">
            <h5 class="fw-bold mb-4 text-primary">Define New Offering</h5>
            <form action="{{ route('admin.services.store') }}" method="POST" class="row g-4">
                @csrf
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted-foreground">Service Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Signature Luxury Fade" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted-foreground">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="" disabled selected>Select category...</option>
                        @foreach($displayCategories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted-foreground">Price (₱)</label>
                    <input type="number" name="price" class="form-control" min="0" step="0.01" placeholder="0.00" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted-foreground">Duration</label>
                    <input type="text" name="duration" class="form-control" placeholder="e.g. 45 min" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted-foreground">Status</label>
                    <select class="form-control"><option>Active</option></select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold text-muted-foreground">Description (Optional)</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Tell clients what to expect..."></textarea>
                </div>
                <div class="col-12 d-flex gap-3 pt-2">
                    <button type="submit" class="btn btn-gold px-5 py-2 fw-bold">CREATE SERVICE</button>
                    <button type="button" class="btn btn-muted px-4 py-2" data-bs-toggle="collapse" data-bs-target="#addServiceForm">CANCEL</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="row g-4">
        @forelse ($filtered as $s)
        <div class="col-md-6 col-lg-4">
            <div class="glass-card service-card p-4 h-100 d-flex flex-column">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <span class="category-badge mb-2 d-inline-block">{{ strtoupper($s->category) }}</span>
                        <h5 class="fw-bold mb-1 text-foreground" style="font-size: 1.25rem;">{{ $s->name }}</h5>
                        <p class="small text-muted mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.5rem;">
                            {{ $s->description ?: 'No description provided.' }}
                        </p>
                    </div>
                </div>

                <div class="mt-auto pt-4 border-top">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <div class="duration-info">
                            <div class="small fw-bold text-muted-foreground mb-1">DURATION</div>
                            <div class="fw-bold text-white small">⏱ {{ strtoupper($s->duration) }}</div>
                        </div>
                        <div class="price-info text-end">
                            <div class="small fw-bold text-muted-foreground mb-1">PRICE</div>
                            <div class="fw-black text-primary" style="font-size: 1.4rem;">₱{{ number_format($s->price, 2) }}</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-gold flex-fill py-2 fw-bold" 
                                onclick="openEditModal({{ $s }})">EDIT</button>
                        <form action="{{ route('admin.services.destroy', $s->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Delete this service permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm flex-fill w-100 py-2 fw-bold text-danger border-danger-subtle bg-danger-subtle">DELETE</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center">
            <div class="opacity-30 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width: 80px;"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
            </div>
            <h5 class="fw-bold text-muted">No Services Found</h5>
            <p class="small text-muted">Begin by adding your first shop offering.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card p-0" style="border-radius: 24px; border: 1px solid var(--border);">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-primary">Edit Offering Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editServiceForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted-foreground">Service Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted-foreground">Category</label>
                            <select name="category" id="edit_category" class="form-select" required>
                                @foreach($displayCategories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted-foreground">Price (₱)</label>
                            <input type="number" name="price" id="edit_price" class="form-control" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted-foreground">Duration</label>
                            <input type="text" name="duration" id="edit_duration" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted-foreground">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-3 pt-4 border-top mt-4">
                        <button type="submit" class="btn btn-gold px-5 py-2 fw-bold">SAVE CHANGES</button>
                        <button type="button" class="btn btn-muted px-4 py-2" data-bs-dismiss="modal">CANCEL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .category-badge { font-size: 0.65rem; font-weight: 800; color: var(--primary); background: rgba(212,175,55,0.1); padding: 0.2rem 0.6rem; border-radius: 4px; letter-spacing: 0.5px; }
    .service-card { transition: 0.3s; border-radius: 28px; }
    .service-card:hover { border-color: var(--primary); transform: translateY(-3px); }
    .modal-content { background: var(--card); }
</style>

<script>
function openEditModal(service) {
    const form = document.getElementById('editServiceForm');
    form.action = `/admin/services/${service.id}`;
    
    document.getElementById('edit_name').value = service.name;
    document.getElementById('edit_category').value = service.category;
    document.getElementById('edit_price').value = service.price;
    document.getElementById('edit_duration').value = service.duration;
    document.getElementById('edit_description').value = service.description || '';
    
    new bootstrap.Modal(document.getElementById('editServiceModal')).show();
}
</script>

<?php render_admin_layout_end(); ?>
