<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'settings';
render_customer_layout_start($page, 'Profile Settings');
?>

<div class="p-3">
  <div class="mb-4">
    <h2 class="page-title mb-1">Profile Settings</h2>
    <p class="page-subtitle mb-0">Update your personal information and profile picture</p>
  </div>

  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4" style="background:rgba(16,185,129,0.1);color:#10b981">
        {{ session('success') }}
    </div>
  @endif

  <div class="row">
    <div class="col-lg-8">
      <div class="glass-card p-4">
        <form action="{{ route('customer.settings.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <!-- Profile Picture Section -->
          <div class="d-flex align-items-center gap-4 mb-5 pb-4 border-bottom" style="border-color:rgba(255,255,255,0.05) !important">
            <div class="position-relative">
              <img id="avatarPreview" 
                   src="{{ Auth::user()->profile_picture ? asset('storage/'.Auth::user()->profile_picture).'?v='.time() : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=D4AF37&color=0B0B0B&size=128' }}" 
                   class="profile-avatar shadow" style="width:100px;height:100px" alt="Profile">
              <label for="profile_picture" class="position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                     style="width:32px;height:32px;cursor:pointer;border:2px solid var(--background); background: var(--primary); color: #000;">
                <span style="font-size:0.8rem"><?= icon('camera') ?></span>
              </label>
              <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*" onchange="previewImage(this)">
            </div>
            <div>
               <h6 class="mb-1" style="font-weight:700">Profile Picture</h6>
               <p class="text-muted-custom mb-0" style="font-size:0.8rem">JPG, GIF or PNG. Max size 2MB.</p>
               @error('profile_picture')
                 <div class="text-danger-custom mt-1" style="font-size:0.75rem">{{ $message }}</div>
               @enderror
            </div>
          </div>

          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label text-muted-custom small fw-bold text-uppercase" style="letter-spacing:0.05em">Full Name</label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Your Name">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label text-muted-custom small fw-bold text-uppercase d-flex justify-content-between align-items-center" style="letter-spacing:0.05em">
                  Email Address
                  @if($user->hasVerifiedEmail())
                      <span class="badge bg-success rounded-pill px-2 py-1 shadow-sm" style="font-size:0.65rem;font-family:var(--font-montserrat)">Verified</span>
                  @else
                      <span class="badge bg-danger rounded-pill px-2 py-1 shadow-sm" style="font-size:0.65rem;font-family:var(--font-montserrat)">Not Verified</span>
                  @endif
              </label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="email@example.com">
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              @if(!$user->hasVerifiedEmail())
                <div class="mt-2 text-end">
                    <button type="button" onclick="document.getElementById('send-verification-form').submit();" class="btn btn-sm btn-outline-warning rounded-pill px-3" style="font-size: 0.7rem; border-color: var(--primary); color: var(--primary); font-weight: 600;">
                        Send Verification Email
                    </button>
                </div>
              @endif
            </div>

            <div class="col-md-6">
              <label class="form-label text-muted-custom small fw-bold text-uppercase" style="letter-spacing:0.05em">Phone Number</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="+63 000 000 0000">
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 mt-5">
              <button type="submit" class="btn btn-gold px-5 py-2 shadow-lg">Save Changes</button>
            </div>
          </div>
        </form>
      </div>

      <div class="glass-card p-4 mt-4">
        <h6 style="font-weight:700" class="mb-4">Change Password</h6>
        <form action="{{ route('customer.settings.password') }}" method="POST">
          @csrf
          <div class="row g-4">
            <div class="col-md-12">
              <label class="form-label text-muted-custom small fw-bold text-uppercase" style="letter-spacing:0.05em">Current Password</label>
              <div class="password-group">
                <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="••••••••">
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('current_password', this)" tabindex="-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                </button>
              </div>
              @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6">
              <label class="form-label text-muted-custom small fw-bold text-uppercase" style="letter-spacing:0.05em">New Password</label>
              <div class="password-group">
                <input type="password" id="new_password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="••••••••">
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('new_password', this)" tabindex="-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                </button>
              </div>
              @error('new_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label text-muted-custom small fw-bold text-uppercase" style="letter-spacing:0.05em">Confirm Password</label>
              <div class="password-group">
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" placeholder="••••••••">
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('new_password_confirmation', this)" tabindex="-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                </button>
              </div>
            </div>

            <div class="col-12 mt-4">
              <button type="submit" class="btn btn-gold px-5 py-2 shadow-sm">Update Password</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Send Verification Form (Hidden) -->
    <form id="send-verification-form" action="{{ route('verification.send') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Security Info Card -->
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="glass-card p-4">
          <h6 style="font-family:var(--font-bebas);letter-spacing:1px;color:var(--primary);font-size:1.2rem" class="mb-3">Security Tip</h6>
          <p class="text-muted-custom" style="font-size:0.85rem;line-height:1.6">Ensure your account is secure by using a unique email address and keeping your contact information up-to-date. You can now update your password directly from this page.</p>
          <hr style="border-color:rgba(255,255,255,0.05)">
          <div class="d-flex align-items-center gap-3">
             <div style="font-size:1.5rem">🛡️</div>
             <div style="font-size:0.8rem;color:var(--muted-foreground)">Two-factor authentication is currently managed via email verification.</div>
          </div>
       </div>
    </div>
  </div>
</div>

<script>
function previewImage(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('avatarPreview').src = e.target.result;
    }
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php render_customer_layout_end(); ?>
