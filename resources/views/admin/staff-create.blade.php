<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'staff-create';
render_admin_layout_start($page, 'Create Personnel Account');
?>

<div class="dashboard-container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="glass-card p-5">
                <div class="text-center mb-5">
                    <div class="icon-circle mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:40px;color:var(--primary)"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>
                    </div>
                    <h2 class="fw-bold" style="color:var(--foreground)">Register New Personnel</h2>
                    <p class="small" style="color:var(--muted-foreground)">Creating this account will automatically generate a linked professional Artisan profile.</p>
                </div>

                @if(session('success'))
                <div class="alert alert-success bg-dark-subtle border-success text-success mb-4" role="alert">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('warning'))
                <div class="alert alert-warning bg-dark-subtle border-warning text-warning mb-4" role="alert">
                    {{ session('warning') }}
                </div>
                @endif

                <form action="{{ route('admin.staff.store') }}" method="POST">
                    @csrf
                    @if(request('from') === 'artist')
                        <input type="hidden" name="redirect_to_artist" value="1">
                    @endif
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase" style="color:var(--muted-foreground)">Full Name</label>
                        <input type="text" name="name" class="form-control py-3 px-4 @error('name') is-invalid @enderror" placeholder="Enter personnel name" required value="{{ old('name') }}">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase" style="color:var(--muted-foreground)">Email Address</label>
                        <input type="email" name="email" class="form-control py-3 px-4 @error('email') is-invalid @enderror" placeholder="email@artisan.com" required value="{{ old('email') }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase" style="color:var(--muted-foreground)">Account Role</label>
                        <select name="role" id="role_select" class="form-select py-3 px-4" required onchange="toggleArtistFields()">
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff / Artisan</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>

                    <div id="artist_fields">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase" style="color:var(--muted-foreground)">Service Category</label>
                            <select name="category" class="form-select py-3 px-4">
                                <option value="" selected disabled>Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            <p class="small text-muted mt-1 mb-0">What department does this personnel belong to?</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase" style="color:var(--muted-foreground)">Primary Specialty</label>
                            <input type="text" name="specialty" class="form-control py-3 px-4" placeholder="e.g. Skin Fade Expert, Realism Art" value="{{ old('specialty') }}">
                            <p class="small text-muted mt-1 mb-0">Their specific professional skill.</p>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase" style="color:var(--muted-foreground)">Password</label>
                            <div class="password-group">
                                <input type="password" id="staff_pwd" name="password" class="form-control py-3 px-4 @error('password') is-invalid @enderror" placeholder="••••••••" required>
                                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('staff_pwd', this)" tabindex="-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                                </button>
                            </div>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase" style="color:var(--muted-foreground)">Confirm Password</label>
                            <div class="password-group">
                                <input type="password" id="staff_pwd_conf" name="password_confirmation" class="form-control py-3 px-4" placeholder="••••••••" required>
                                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('staff_pwd_conf', this)" tabindex="-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gold w-100 py-3 shadow-lg" style="font-weight:700;letter-spacing:1px">
                        Register Personnel & Artist Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-circle {
        width: 80px; height: 80px;
        background: rgba(212, 175, 55, 0.1);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto;
    }
</style>

<script>
    function toggleArtistFields() {
        const role = document.getElementById('role_select').value;
        const artistFields = document.getElementById('artist_fields');
        if (role === 'staff') {
            artistFields.classList.remove('d-none');
            // Re-enable required if necessary, but here they are optional
        } else {
            artistFields.classList.add('d-none');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleArtistFields();
    });
</script>

<?php render_admin_layout_end(); ?>
