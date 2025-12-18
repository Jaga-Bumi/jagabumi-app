<div id="tab-dashboard" class="tab-content hidden">
  @php
    // Use userParticipation for the CURRENT USER's submission (not all submissions)
    $hasSubmission = $userParticipation && in_array($userParticipation->status, ['COMPLETED', 'APPROVED', 'REJECTED']);
    $latestSubmission = $hasSubmission ? $userParticipation : null;
    $canSubmit = $userParticipation && $userParticipation->status === 'REGISTERED';
  @endphp

  <!-- Success/Error Messages -->
  @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 font-medium">
      {{ session('success') }}
    </div>
  @endif
  
  @if($errors->any())
    <div class="mb-6 p-4 bg-destructive/10 border border-destructive/20 rounded-xl text-destructive font-medium">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif

  <!-- Quest Completion Status -->
  <div class="mb-8 p-6 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/20 dark:to-teal-950/20 rounded-2xl border border-emerald-200 dark:border-emerald-800">
    <div class="flex items-start gap-4">
      <div class="w-12 h-12 rounded-full bg-emerald-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div class="flex-1">
        <h3 class="text-xl font-bold text-foreground mb-2">Quest Submission Dashboard</h3>
        <p class="text-muted-foreground mb-3">
          @if($canSubmit)
            Kirimkan bukti penyelesaian akhir Anda, termasuk dokumentasi video dan deskripsi.
          @elseif($hasSubmission)
            Submission Anda telah diterima. Periksa status di bawah ini.
          @else
            Anda perlu mendaftar untuk misi ini terlebih dahulu.
          @endif
        </p>
        
        <!-- Status Badge -->
        @if($hasSubmission)
          <span class="inline-block px-4 py-2 rounded-lg text-sm font-semibold
            {{ $latestSubmission->status === 'APPROVED' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}
            {{ $latestSubmission->status === 'REJECTED' ? 'bg-destructive/10 text-destructive' : '' }}
            {{ $latestSubmission->status === 'COMPLETED' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}">
            {{ $latestSubmission->status }}
          </span>
        @endif
      </div>
    </div>
  </div>

  @if($canSubmit)
    <!-- Submit Final Proof Form -->
    <div class="bg-card border border-border rounded-2xl p-6 md:p-8">
      <h3 class="text-2xl font-bold text-foreground mb-6 flex items-center gap-3">
        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        Submit Bukti Terakhir
      </h3>

      <form id="submit-proof-form" class="space-y-6" enctype="multipart/form-data">
        @csrf
        
        @if($quest->liveness_code)
          <!-- Liveness Code -->
          <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-2 text-amber-800 dark:text-amber-400 font-semibold mb-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              Liveness Code Dibutuhkan
            </div>
            <p class="text-sm text-amber-700 dark:text-amber-300 mb-3">Misi ini mengharuskan Anda berada di lokasi tersebut untuk mendapatkan Liveness Code.</p>
            <label for="liveness_code" class="block text-sm font-medium text-foreground mb-2">
              Enter Liveness Code <span class="text-destructive">*</span>
            </label>
            <input 
              type="text" 
              id="liveness_code" 
              name="liveness_code" 
              required
              placeholder="Enter code dari lokasi"
              class="w-full px-4 py-3 border border-border rounded-lg bg-background text-foreground focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
            >
          </div>
        @endif

        <!-- Video Upload -->
        <div>
          <label class="block text-sm font-medium text-foreground mb-2">
            Bukti Video <span class="text-destructive">*</span>
          </label>
          
          <!-- Upload Area (shown when no video) -->
          <div id="video-upload-area" class="border-2 border-dashed border-border rounded-xl p-8 text-center hover:border-emerald-500 transition-colors cursor-pointer bg-muted/30">
            <input type="file" name="video" id="video-input" accept="video/*" class="hidden" required>
            <svg class="w-16 h-16 mx-auto mb-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <p class="text-foreground font-medium mb-1">Click untuk upload video</p>
            <p class="text-sm text-muted-foreground">MP4, MOV, AVI up to 3GB</p>
          </div>
          
          <!-- Video Preview Player (replaces upload area) -->
          <div id="video-preview" class="hidden">
            <div class="bg-card border border-border rounded-xl overflow-hidden">
              <div class="p-3 bg-muted/50 border-b border-border flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <p class="text-sm text-foreground font-medium">Selected: <span id="video-name"></span></p>
                </div>
                <button type="button" onclick="clearVideo()" class="text-muted-foreground hover:text-destructive transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
              <div class="bg-black">
                <video id="video-player" controls class="w-full max-h-96">
                  Browser Anda tidak mendukung tag video.
                </video>
              </div>
              <div class="p-4 bg-muted/30 border-t border-border">
                <button type="button" onclick="document.getElementById('video-input').click()" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                  Ganti Video
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label for="description" class="block text-sm font-medium text-foreground mb-2">
            Deskripsi <span class="text-destructive">*</span>
          </label>
          <textarea 
            id="description" 
            name="description" 
            rows="6" 
            required
            minlength="50"
            maxlength="10000"
            placeholder="Jelaskan kontribusi Anda pada misi ini secara detail... (minimal 50 karakter)"
            class="w-full px-4 py-3 border border-border rounded-lg bg-background text-foreground focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
          ></textarea>
          <div class="flex justify-between items-center mt-2">
            <p class="text-xs text-muted-foreground">Minimum 50 karakter</p>
            <p class="text-xs text-muted-foreground"><span id="char-count">0</span> / 10,000</p>
          </div>
        </div>

        <!-- Submit Message -->
        <div id="submit-message" class="hidden"></div>

        <!-- Submit Button -->
        <button 
          type="submit" 
          id="submit-btn"
          class="w-full px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-3 shadow-lg hover:shadow-xl">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
          <span id="submit-text">Submit Bukti Terakhir</span>
        </button>
      </form>
    </div>

  @elseif($hasSubmission)
    <!-- Submission Status View -->
    <div class="bg-card border border-border rounded-2xl overflow-hidden">
      <!-- Header -->
      <div class="p-6 border-b border-border">
        <h3 class="text-2xl font-bold text-foreground mb-2">Submission Anda</h3>
        <p class="text-muted-foreground">Submitted on {{ $latestSubmission->created_at->format('F d, Y \a\t H:i') }}</p>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-6">
        <!-- Status -->
        <div>
          <h4 class="font-semibold text-foreground mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Status
          </h4>
          <div class="flex items-center gap-3">
            <span class="inline-block px-4 py-2 rounded-lg text-sm font-bold
              {{ $latestSubmission->status === 'APPROVED' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : '' }}
              {{ $latestSubmission->status === 'REJECTED' ? 'bg-destructive/10 text-destructive border border-destructive/20' : '' }}
              {{ $latestSubmission->status === 'COMPLETED' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800' : '' }}">
              {{ $latestSubmission->status }}
            </span>
            
            @if($latestSubmission->status === 'COMPLETED')
              <span class="text-sm text-muted-foreground">⏳ Ditunggu untuk review</span>
            @elseif($latestSubmission->status === 'APPROVED')
              <span class="text-sm text-emerald-600 dark:text-emerald-400">✓ Selamat! Submission Anda diterima</span>
            @elseif($latestSubmission->status === 'REJECTED')
              <span class="text-sm text-destructive">✗ Submission Anda ditolak</span>
            @endif
          </div>
        </div>

        <!-- Description -->
        <div>
          <h4 class="font-semibold text-foreground mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            Deskripsi Anda
          </h4>
          <div class="p-4 bg-muted/30 rounded-lg border border-border">
            <p class="text-foreground whitespace-pre-wrap">{{ $latestSubmission->description }}</p>
          </div>
        </div>

        <!-- Video -->
        @if($latestSubmission->video_url)
          <div>
            <h4 class="font-semibold text-foreground mb-3 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
              </svg>
              Submitted Video
            </h4>
            <div class="rounded-xl overflow-hidden border border-border bg-black">
              <video controls class="w-full max-h-96">
                <source src="{{ $latestSubmission->video_url }}" type="video/mp4">
                Browser Anda tidak mendukung tag video.
              </video>
            </div>
          </div>
        @endif

        <!-- Rejection Reason -->
        @if($latestSubmission->status === 'REJECTED' && $latestSubmission->rejection_reason)
          <div class="p-4 bg-destructive/10 border border-destructive/20 rounded-xl">
            <h4 class="font-semibold text-destructive mb-2 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Alasan Ditolak
            </h4>
            <p class="text-destructive">{{ $latestSubmission->rejection_reason }}</p>
          </div>
        @endif
      </div>
    </div>
  @else
    <!-- Not Registered -->
    <div class="text-center py-12">
      <svg class="w-20 h-20 mx-auto mb-4 text-muted-foreground/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
      </svg>
      <h3 class="text-xl font-bold text-foreground mb-2">Ikut Quest Dulu</h3>
      <p class="text-muted-foreground mb-6">Anda perlu mendaftar untuk quest ini sebelum dapat mengirimkan bukti.</p>
    </div>
  @endif
</div>

@push('scripts')
<script>
// Clear video function
function clearVideo() {
  const videoInput = document.getElementById('video-input');
  const videoPreview = document.getElementById('video-preview');
  const videoUploadArea = document.getElementById('video-upload-area');
  const videoPlayer = document.getElementById('video-player');
  
  videoInput.value = '';
  videoPreview.classList.add('hidden');
  videoUploadArea.classList.remove('hidden');
  videoPlayer.src = '';
}

document.addEventListener('DOMContentLoaded', function() {
  // Video upload handler
  const videoUploadArea = document.getElementById('video-upload-area');
  const videoInput = document.getElementById('video-input');
  const videoPreview = document.getElementById('video-preview');
  const videoName = document.getElementById('video-name');
  const videoPlayer = document.getElementById('video-player');

  if (videoUploadArea && videoInput) {
    videoUploadArea.addEventListener('click', () => videoInput.click());
    
    videoInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        // Check file size (3GB = 3221225472 bytes)
        if (file.size > 3221225472) {
          alert('Ukuran lebih dari 3GB. Tolong pilih file lebih kecil.');
          videoInput.value = '';
          return;
        }
        
        // Display file name
        videoName.textContent = file.name;
        
        // Create object URL for video preview
        const objectURL = URL.createObjectURL(file);
        videoPlayer.src = objectURL;
        
        // Hide upload area and show preview
        videoUploadArea.classList.add('hidden');
        videoPreview.classList.remove('hidden');
        
        // Clean up object URL when video loads
        videoPlayer.onloadeddata = function() {
          URL.revokeObjectURL(objectURL);
        };
      }
    });
  }

  // Character counter
  const description = document.getElementById('description');
  const charCount = document.getElementById('char-count');
  
  if (description && charCount) {
    description.addEventListener('input', function() {
      charCount.textContent = this.value.length;
    });
  }

  // Form submission
  const form = document.getElementById('submit-proof-form');
  if (form) {
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const submitBtn = document.getElementById('submit-btn');
      const submitText = document.getElementById('submit-text');
      const messageBox = document.getElementById('submit-message');
      
      // Validate liveness code if required
      const livenessCodeInput = document.getElementById('liveness_code');
      if (livenessCodeInput && livenessCodeInput.value.trim() === '') {
        messageBox.className = 'p-4 bg-destructive/10 border border-destructive/20 rounded-xl text-destructive font-medium';
        messageBox.textContent = 'Tolong isi liveness code.';
        messageBox.classList.remove('hidden');
        return;
      }

      // Validate description length
      const desc = description.value.trim();
      if (desc.length < 50) {
        messageBox.className = 'p-4 bg-destructive/10 border border-destructive/20 rounded-xl text-destructive font-medium';
        messageBox.textContent = 'Deskripsi harus minimal 50 karakter.';
        messageBox.classList.remove('hidden');
        return;
      }

      submitBtn.disabled = true;
      submitText.textContent = 'Submitting...';
      
      const formData = new FormData(form);
      
      try {
        const response = await fetch('{{ route("quests.submit-proof", $quest->id) }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: formData
        });
        
        const data = await response.json();
        
        messageBox.classList.remove('hidden');
        if (data.success) {
          messageBox.className = 'p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 font-medium';
          messageBox.textContent = data.message;
          setTimeout(() => location.reload(), 2000);
        } else {
          messageBox.className = 'p-4 bg-destructive/10 border border-destructive/20 rounded-xl text-destructive font-medium';
          messageBox.textContent = data.message || 'Gagal untuk submit bukti. Tolong coba lain.';
          submitBtn.disabled = false;
          submitText.textContent = 'Submit Bukti Terakhir';
        }
      } catch (error) {
        console.error('Submission error:', error);
        messageBox.classList.remove('hidden');
        messageBox.className = 'p-4 bg-destructive/10 border border-destructive/20 rounded-xl text-destructive font-medium';
        messageBox.textContent = 'Sebuah error terjadi. Tolong coba lagi.';
        submitBtn.disabled = false;
        submitText.textContent = 'Submit Bukti Terakhir';
      }
    });
  }
});
</script>
@endpush
