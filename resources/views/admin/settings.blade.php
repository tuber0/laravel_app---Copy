<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'settings';
render_admin_layout_start($page, 'Profile Settings');
?>

<div class="dashboard-container">
  <div class="mb-5">
    <h1 class="page-title mb-0">Admin Profile</h1>
    <p class="small text-muted-foreground mb-0">Update your administrative credentials and personal details.</p>
  </div>

  @if(session('success'))
    <div class="alert alert-success bg-dark-subtle border-success text-success mb-4 rounded-4 small fw-bold animate-fade-in shadow-sm">
        {{ session('success') }}
    </div>
  @endif

  <div class="row g-4">
    <div class="col-lg-8">
      <!-- Details Form -->
      <div class="glass-card p-4 mx-auto mb-4" style="border-radius: 24px;">
        <h5 class="fw-bold mb-4 text-primary">Personal Details</h5>
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <!-- Profile Picture Section -->
          <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom" style="border-color:rgba(255,255,255,0.05) !important">
            <div class="position-relative">
              <img id="avatarPreview" 
                   src="{{ Auth::user()->profile_picture ? asset('storage/'.Auth::user()->profile_picture).'?v='.time() : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=D4AF37&color=0B0B0B&size=128' }}" 
                   class="profile-avatar shadow" style="width:100px;height:100px; border-radius:50%; object-fit:cover; border: 2px solid var(--primary);" alt="Profile">
              <label for="profile_picture" class="position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                     style="width:32px;height:32px;cursor:pointer;border:2px solid var(--background); background: var(--primary); color: #000;">
                <span style="font-size:0.8rem"><?= icon('camera') ?? '📷' ?></span>
              </label>
              <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*" onchange="previewImage(this)">
            </div>
            <div>
               <h6 class="mb-1 fw-bold text-foreground">Avatar</h6>
               <p class="text-muted-foreground mb-0" style="font-size:0.8rem">JPG, GIF or PNG. Max size 2MB.</p>
               @error('profile_picture')
                 <div class="text-danger mt-1 fw-bold" style="font-size:0.75rem">{{ $message }}</div>
               @enderror
            </div>
          </div>

          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-muted-foreground">Full Name</label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Admin Name">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-bold text-muted-foreground">Email Address</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="admin@pedrosartisan.com">
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-bold text-muted-foreground">Phone Number</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="+63 000 000 0000">
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 mt-4 d-flex justify-content-end border-top pt-4">
              <button type="submit" class="btn btn-gold px-5 py-2 fw-bold">Update Profile</button>
            </div>
          </div>
        </form>
      </div>

      <!-- Password Form -->
      <div class="glass-card p-4 mx-auto" style="border-radius: 24px;">
        <h5 class="fw-bold mb-4 text-primary">Security Settings</h5>
        <form action="{{ route('admin.settings.password') }}" method="POST">
          @csrf
          <div class="row g-4">
            <div class="col-md-12">
              <label class="form-label small fw-bold text-muted-foreground">Current Password</label>
              <div class="password-group">
                  <input type="password" id="curr_pwd_admin" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="••••••••">
                  <button type="button" class="toggle-password" onclick="togglePasswordVisibility('curr_pwd_admin', this)" tabindex="-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                  </button>
              </div>
              @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold text-muted-foreground">New Password</label>
              <div class="password-group">
                  <input type="password" id="new_pwd_admin" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="••••••••">
                  <button type="button" class="toggle-password" onclick="togglePasswordVisibility('new_pwd_admin', this)" tabindex="-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                  </button>
              </div>
              @error('new_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-bold text-muted-foreground">Confirm Password</label>
              <div class="password-group">
                  <input type="password" id="new_pwd_conf_admin" name="new_password_confirmation" class="form-control" placeholder="••••••••">
                  <button type="button" class="toggle-password" onclick="togglePasswordVisibility('new_pwd_conf_admin', this)" tabindex="-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                  </button>
              </div>
            </div>

            <div class="col-12 mt-4 d-flex justify-content-end border-top pt-4">
              <button type="submit" class="btn btn-muted px-5 py-2 fw-bold" style="color:var(--foreground)">Change Password</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Info Column -->
    <div class="col-lg-4">
        <div class="glass-card p-4 h-100" style="border-radius: 24px;">
          <h5 class="fw-bold mb-3 text-foreground">Access Level</h5>
          <div class="d-flex align-items-center gap-3 mb-4">
             <div class="bg-primary text-dark rounded-circle d-flex justify-content-center align-items-center" style="width: 40px; height: 40px; font-weight: 800;">
                 A
             </div>
             <div>
                 <h6 class="mb-0 fw-bold">Administrator</h6>
                 <p class="small text-muted mb-0">Unrestricted system access</p>
             </div>
          </div>
          <hr style="border-color: rgba(255,255,255,0.05)">
          <h6 class="fw-bold text-primary mt-4 mb-2">Security Notice</h6>
          <p class="small text-muted-foreground mb-0">Never share your administrator login credentials. Use strong, unique passwords for maximum system security.</p>
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

<?php render_admin_layout_end(); ?>
