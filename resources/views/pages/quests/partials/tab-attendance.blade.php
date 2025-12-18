<div id="tab-attendance" class="tab-content hidden">
  @php
    $now = now();
    $questActive = $now->between($quest->quest_start_at, $quest->quest_end_at);
    $lastAttendance = $userParticipation->questAttendances()->orderBy('created_at', 'desc')->first();
    // Check if CURRENT USER has submitted proof (not all submissions)
    $hasSubmission = $userParticipation && in_array($userParticipation->status, ['COMPLETED', 'APPROVED', 'REJECTED']);
    
    // If has submission, can't check-in anymore, but can still check-out if last action was check-in
    $canCheckIn = !$hasSubmission && (!$lastAttendance || $lastAttendance->type === 'CHECK_OUT');
    $canCheckOut = $lastAttendance && $lastAttendance->type === 'CHECK_IN';
  @endphp

  <!-- Success/Error Messages -->
  @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-200 rounded-xl text-emerald-800 font-medium">
      {{ session('success') }}
    </div>
  @endif
  
  @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-800 font-medium">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif

  <!-- Submission Notice -->
  @if($hasSubmission)
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
      <div class="flex items-center gap-2 text-amber-800 font-semibold mb-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Bukti Submitted
      </div>
      <p class="text-sm text-amber-700">Anda telah mengirimkan bukti Anda. Kehadiran sekarang dinonaktifkan. {{ $canCheckOut ? 'You can still check-out if you haven\'t already.' : '' }}</p>
    </div>
  @endif

  <!-- Hidden Quest Data for JavaScript -->
  <input type="hidden" id="quest-latitude" value="{{ $quest->latitude }}">
  <input type="hidden" id="quest-longitude" value="{{ $quest->longitude }}">
  <input type="hidden" id="quest-radius" value="{{ $quest->radius_meter ?? 100 }}">

  <!-- Attendance Section -->
  <div class="mb-8">
    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
      <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Kehadiran Check-in/out
    </h3>

    @if($questActive)
      <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Check-in Card -->
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-6 border-2 {{ $canCheckIn ? 'border-emerald-300' : 'border-gray-300 opacity-50' }}">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-emerald-600 flex items-center justify-center">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
            </div>
            <div>
              <h4 class="font-bold text-emerald-900 text-lg">Check-In</h4>
              <p class="text-sm text-emerald-700">Tandai kedatangan Anda</p>
            </div>
          </div>
          @if($canCheckIn)
            <button onclick="openAttendanceModal('CHECK_IN')" class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors">
              Check-In Sekarang
            </button>
          @elseif($hasSubmission)
            <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
              Disabled (Bukti Submitted)
            </button>
          @else
            <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
              Sudah Checked-In
            </button>
          @endif
        </div>

        <!-- Check-out Card -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 border-2 {{ $canCheckOut && !$hasSubmission ? 'border-red-300' : ($canCheckOut ? 'border-amber-300' : 'border-gray-300 opacity-50') }}">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
            </div>
            <div>
              <h4 class="font-bold text-red-900 text-lg">Check-Out</h4>
              <p class="text-sm text-red-700">Tandai keberangkatan Anda</p>
            </div>
          </div>
          @if($canCheckOut)
            <button onclick="openAttendanceModal('CHECK_OUT')" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
              Check-Out Sekarang
            </button>
          @elseif($hasSubmission && $lastAttendance && $lastAttendance->type === 'CHECK_OUT')
            <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
              Sudah Checked-Out
            </button>
          @else
            <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
              Harus Check-In Dulu
            </button>
          @endif
        </div>
      </div>

      <!-- Current Status -->
      @if($lastAttendance)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
          <div class="flex items-center gap-2 text-blue-900 font-semibold mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Status Sekarang
          </div>
          <p class="text-blue-800">Aksi Terakhir: <strong>{{ $lastAttendance->type }}</strong> at {{ $lastAttendance->created_at->format('M d, Y H:i') }}</p>
          @if($lastAttendance->distance_from_quest_location)
            <p class="text-sm text-blue-700 mt-1">Jarak dari Lokasi: {{ number_format($lastAttendance->distance_from_quest_location, 2) }}m</p>
          @endif
        </div>
      @endif

      <!-- Attendance History -->
      <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          History Kehadiran
        </h4>
        @php
          $attendances = $userParticipation->questAttendances()->orderBy('created_at', 'desc')->get();
        @endphp
        @if($attendances->count() > 0)
          <div class="space-y-3">
            @foreach($attendances as $attendance)
              <div class="p-3 bg-gray-50 rounded-lg">
                <div class="flex items-start gap-4">
                  <div class="w-10 h-10 rounded-full {{ $attendance->type === 'CHECK_IN' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center flex-shrink-0">
                    @if($attendance->type === 'CHECK_IN')
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                      </svg>
                    @else
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H7"/>
                      </svg>
                    @endif
                  </div>
                  <div class="flex-1">
                    <div class="flex items-center justify-between">
                      <p class="font-semibold text-gray-900">{{ $attendance->type }}</p>
                      @if($attendance->is_valid_location)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Lokasi Valid</span>
                      @elseif($attendance->distance_from_quest_location)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Di Luar Jangkauan</span>
                      @endif
                    </div>
                    <p class="text-sm text-gray-600">{{ $attendance->created_at->format('M d, Y H:i:s') }}</p>
                    @if($attendance->distance_from_quest_location)
                      <p class="text-xs text-gray-500">Distance: {{ number_format($attendance->distance_from_quest_location, 0) }}m</p>
                    @endif
                    @if($attendance->notes)
                      <p class="text-xs text-gray-500 mt-1 italic">"{{ $attendance->notes }}"</p>
                    @endif
                  </div>
                </div>
                
                {{-- Proof Photo --}}
                @if($attendance->proof_photo_url)
                  <div class="mt-3 pl-14">
                    <button type="button" onclick="showPhotoModal('{{ $attendance->proof_photo_url }}')" class="group">
                      <img src="{{ $attendance->proof_photo_url }}" alt="Proof Photo" class="w-24 h-24 object-cover rounded-lg border border-gray-200 group-hover:border-emerald-500 transition-colors">
                      <p class="text-xs text-gray-500 mt-1 group-hover:text-emerald-600 transition-colors">Click untuk lihat</p>
                    </button>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @else
          <p class="text-gray-500 text-center py-4">Belum ada catatan kehadiran</p>
        @endif
      </div>
    @else
      <div class="text-center py-12 bg-gray-50 rounded-xl">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-600 font-medium">Quest saat ini tidak aktif.</p>
        <p class="text-sm text-gray-500 mt-2">Periode Aktif: {{ $quest->quest_start_at->format('M d, Y H:i') }} - {{ $quest->quest_end_at->format('M d, Y H:i') }}</p>
      </div>
    @endif
  </div>
</div>

<!-- Attendance Modal with Camera Capture -->
<div id="attendance-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeAttendanceModal(event)">
  <div class="bg-white rounded-2xl p-6 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-2xl font-bold" id="modal-title">Check-In</h3>
      <button type="button" onclick="closeAttendanceModal()" class="p-2 hover:bg-gray-100 rounded-full">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    
    <!-- Location Status -->
    <div id="location-status" class="mb-4">
      <div class="flex items-center gap-2 text-gray-600">
        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        <span>Memverifikasi lokasi Anda...</span>
      </div>
    </div>
    
    <!-- Attendance Form -->
    <form id="attendance-form" method="POST" action="" enctype="multipart/form-data" class="hidden">
      @csrf
      <input type="hidden" name="type" id="attendance-type">
      <input type="hidden" name="latitude" id="user-latitude">
      <input type="hidden" name="longitude" id="user-longitude">
      <input type="hidden" name="liveness_code" id="liveness-code-input">
      
      <!-- Photo Capture Section -->
      <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Bukti Foto <span class="text-red-500">*</span>
        </label>
        <p class="text-xs text-gray-500 mb-3">Ambil foto untuk memverifikasi kehadiran Anda di lokasi tersebut.</p>
        
        <!-- Camera Preview Container -->
        <div id="camera-container" class="relative rounded-xl overflow-hidden border-2 border-dashed border-gray-300 bg-gray-50">
          <!-- Camera Preview (before capture) -->
          <div id="camera-preview-wrapper" class="relative hidden">
            <video id="camera-preview" autoplay playsinline class="w-full rounded-lg" style="max-height: 300px; object-fit: cover;"></video>
            <button type="button" id="capture-btn" onclick="capturePhoto()" class="absolute bottom-4 left-1/2 -translate-x-1/2 w-16 h-16 bg-white rounded-full border-4 border-emerald-500 flex items-center justify-center shadow-lg hover:scale-105 transition-transform">
              <div class="w-12 h-12 bg-emerald-500 rounded-full"></div>
            </button>
          </div>
          
          <!-- Captured Photo Preview -->
          <div id="captured-preview" class="hidden">
            <img id="captured-image" class="w-full rounded-lg" style="max-height: 300px; object-fit: cover;">
            <button type="button" onclick="retakePhoto()" class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-2 bg-white text-gray-700 rounded-lg shadow-lg font-semibold hover:bg-gray-50 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              Ambil Ulang
            </button>
          </div>
          
          <!-- Start Camera Button (initial state) -->
          <div id="camera-start" class="flex flex-col items-center justify-center py-8">
            <button type="button" onclick="startCamera()" class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-3 hover:bg-emerald-100 transition-colors">
              <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </button>
            <span class="text-sm font-medium text-gray-700">Tap untuk buka camera</span>
            <span class="text-xs text-gray-500 mt-1">atau upload foto</span>
          </div>
          
          <!-- Error message -->
          <div id="camera-error" class="hidden px-4 py-3 bg-red-50 text-red-700 text-sm"></div>
        </div>
        
        <!-- Hidden canvas for capture -->
        <canvas id="capture-canvas" class="hidden"></canvas>
        
        <!-- File input (hidden, receives captured photo) -->
        <input type="file" name="proof_photo" id="proof-photo-input" accept="image/*" capture="environment" class="hidden" required>
        
        <!-- Or Upload Button -->
        <div class="mt-3 text-center">
          <button type="button" onclick="document.getElementById('file-upload-input').click()" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
            Atau upload dari galeri →
          </button>
          <input type="file" id="file-upload-input" accept="image/*" class="hidden" onchange="handleFileUpload(event)">
        </div>
        
        <!-- Photo status indicator -->
        <div id="photo-status" class="mt-2 text-center hidden">
          <span class="inline-flex items-center gap-1 text-emerald-600 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Foto berhasil diambil!
          </span>
        </div>
      </div>
      
      <!-- Liveness Code -->
      @if($quest->liveness_code)
        <div class="mb-4">
          <label for="liveness-code" class="block text-sm font-semibold text-gray-700 mb-2">
            Liveness Code <span class="text-red-500">*</span>
          </label>
          <p class="text-xs text-gray-500 mb-2">Masukkan kode yang ditampilkan di lokasi tersebut</p>
          <input type="text" id="liveness-code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Enter code" required>
        </div>
      @endif
      
      <!-- Optional Notes -->
      <div class="mb-6">
        <label for="attendance-notes" class="block text-sm font-semibold text-gray-700 mb-2">
          Catatan
        </label>
        <textarea name="notes" id="attendance-notes" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none" placeholder="Any additional notes..."></textarea>
      </div>

      <div class="flex gap-3">
        <button type="button" onclick="closeAttendanceModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold">
          Batal
        </button>
        <button type="submit" id="submit-btn" class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" disabled>
          Terima
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Photo View Modal -->
<div id="photo-modal" class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50" onclick="closePhotoModal()">
  <div class="relative max-w-4xl max-h-[90vh] mx-4">
    <button onclick="closePhotoModal()" class="absolute -top-10 right-0 p-2 text-white hover:text-gray-300 transition-colors">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
    <img id="photo-modal-image" src="" alt="Proof Photo" class="max-w-full max-h-[90vh] rounded-lg object-contain" onclick="event.stopPropagation()">
  </div>
</div>

@push('scripts')
<script>
let attendanceType = '';
let cameraStream = null;
let photoTaken = false;

function openAttendanceModal(type) {
  attendanceType = type;
  photoTaken = false;
  
  // Reset UI
  document.getElementById('attendance-modal').classList.remove('hidden');
  document.getElementById('attendance-modal').classList.add('flex');
  document.getElementById('modal-title').textContent = type === 'CHECK_IN' ? 'Check-In' : 'Check-Out';
  document.getElementById('attendance-type').value = type;
  document.getElementById('attendance-form').classList.add('hidden');
  document.getElementById('submit-btn').disabled = true;
  document.getElementById('photo-status').classList.add('hidden');
  
  // Reset camera UI
  document.getElementById('camera-start').classList.remove('hidden');
  document.getElementById('camera-preview-wrapper').classList.add('hidden');
  document.getElementById('captured-preview').classList.add('hidden');
  document.getElementById('camera-error').classList.add('hidden');
  
  // Set form action
  const form = document.getElementById('attendance-form');
  const questId = {{ $quest->id }};
  form.action = type === 'CHECK_IN' 
    ? `/quests/${questId}/attendance/check-in` 
    : `/quests/${questId}/attendance/check-out`;
  
  // Get user location
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const userLat = position.coords.latitude;
        const userLon = position.coords.longitude;
        const questLat = parseFloat(document.getElementById('quest-latitude').value);
        const questLon = parseFloat(document.getElementById('quest-longitude').value);
        const questRadius = parseFloat(document.getElementById('quest-radius').value);
        
        const distance = calculateDistance(userLat, userLon, questLat, questLon);
        
        document.getElementById('user-latitude').value = userLat;
        document.getElementById('user-longitude').value = userLon;
        
        const statusDiv = document.getElementById('location-status');
        const formDiv = document.getElementById('attendance-form');
        
        if (distance <= questRadius) {
          statusDiv.innerHTML = `
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div>
                <p class="text-emerald-800 font-semibold text-sm">Location Verified!</p>
                <p class="text-xs text-emerald-600">${distance.toFixed(0)}m from location (within ${questRadius}m)</p>
              </div>
            </div>
          `;
          formDiv.classList.remove('hidden');
        } else {
          statusDiv.innerHTML = `
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg flex items-center gap-2">
              <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <div>
                <p class="text-amber-800 font-semibold text-sm">Outside Target Area</p>
                <p class="text-xs text-amber-600">${distance.toFixed(0)}m away (max: ${questRadius}m). You can still check-in/out.</p>
              </div>
            </div>
          `;
          formDiv.classList.remove('hidden');
        }
      },
      (error) => {
        document.getElementById('location-status').innerHTML = `
          <div class="p-3 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
              <p class="text-red-800 font-semibold text-sm">Location Access Denied</p>
              <p class="text-xs text-red-600">Please enable location services to continue.</p>
            </div>
          </div>
        `;
      }
    );
  }
}

async function startCamera() {
  try {
    document.getElementById('camera-start').classList.add('hidden');
    document.getElementById('camera-error').classList.add('hidden');
    
    // Try to get camera access - prefer back camera on mobile
    cameraStream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'environment' },
      audio: false
    });
    
    const video = document.getElementById('camera-preview');
    video.srcObject = cameraStream;
    
    document.getElementById('camera-preview-wrapper').classList.remove('hidden');
  } catch (error) {
    console.error('Camera error:', error);
    document.getElementById('camera-error').textContent = 'Camera access denied. Please use the upload option instead.';
    document.getElementById('camera-error').classList.remove('hidden');
    document.getElementById('camera-start').classList.remove('hidden');
  }
}

function capturePhoto() {
  const video = document.getElementById('camera-preview');
  const canvas = document.getElementById('capture-canvas');
  const ctx = canvas.getContext('2d');
  
  // Set canvas size to video dimensions
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  
  // Draw video frame to canvas
  ctx.drawImage(video, 0, 0);
  
  // Convert to blob and set to file input
  canvas.toBlob((blob) => {
    const file = new File([blob], 'attendance_photo.jpg', { type: 'image/jpeg' });
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    document.getElementById('proof-photo-input').files = dataTransfer.files;
    
    // Show captured image
    document.getElementById('captured-image').src = canvas.toDataURL('image/jpeg');
    document.getElementById('camera-preview-wrapper').classList.add('hidden');
    document.getElementById('captured-preview').classList.remove('hidden');
    
    // Stop camera
    stopCamera();
    
    // Enable submit button
    photoTaken = true;
    updateSubmitButton();
  }, 'image/jpeg', 0.8);
}

function retakePhoto() {
  photoTaken = false;
  document.getElementById('captured-preview').classList.add('hidden');
  document.getElementById('photo-status').classList.add('hidden');
  updateSubmitButton();
  startCamera();
}

function handleFileUpload(event) {
  const file = event.target.files[0];
  if (file) {
    // Copy to proof photo input
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    document.getElementById('proof-photo-input').files = dataTransfer.files;
    
    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
      document.getElementById('captured-image').src = e.target.result;
      document.getElementById('camera-start').classList.add('hidden');
      document.getElementById('camera-preview-wrapper').classList.add('hidden');
      document.getElementById('captured-preview').classList.remove('hidden');
      
      // Stop camera if running
      stopCamera();
      
      photoTaken = true;
      updateSubmitButton();
    };
    reader.readAsDataURL(file);
  }
}

function stopCamera() {
  if (cameraStream) {
    cameraStream.getTracks().forEach(track => track.stop());
    cameraStream = null;
  }
}

function updateSubmitButton() {
  const btn = document.getElementById('submit-btn');
  const photoStatus = document.getElementById('photo-status');
  
  if (photoTaken) {
    btn.disabled = false;
    photoStatus.classList.remove('hidden');
  } else {
    btn.disabled = true;
    photoStatus.classList.add('hidden');
  }
}

function closeAttendanceModal(event) {
  if (!event || event.target.id === 'attendance-modal') {
    document.getElementById('attendance-modal').classList.add('hidden');
    document.getElementById('attendance-modal').classList.remove('flex');
    const livenessInput = document.getElementById('liveness-code');
    if (livenessInput) livenessInput.value = '';
    document.getElementById('attendance-notes').value = '';
    
    // Stop camera
    stopCamera();
    
    // Reset photo state
    photoTaken = false;
    updateSubmitButton();
  }
}

document.getElementById('attendance-form')?.addEventListener('submit', function(e) {
  const livenessCodeInput = document.getElementById('liveness-code');
  if (livenessCodeInput) {
    const code = livenessCodeInput.value;
    document.getElementById('liveness-code-input').value = code;
  }
  
  // Verify photo is captured
  const photoInput = document.getElementById('proof-photo-input');
  if (!photoInput.files || photoInput.files.length === 0) {
    e.preventDefault();
    alert('Please capture or upload a photo first.');
    return false;
  }
});

function calculateDistance(lat1, lon1, lat2, lon2) {
  const R = 6371e3;
  const φ1 = lat1 * Math.PI / 180;
  const φ2 = lat2 * Math.PI / 180;
  const Δφ = (lat2 - lat1) * Math.PI / 180;
  const Δλ = (lon2 - lon1) * Math.PI / 180;

  const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  return R * c;
}

function showPhotoModal(url) {
  document.getElementById('photo-modal-image').src = url;
  document.getElementById('photo-modal').classList.remove('hidden');
  document.getElementById('photo-modal').classList.add('flex');
}

function closePhotoModal() {
  document.getElementById('photo-modal').classList.add('hidden');
  document.getElementById('photo-modal').classList.remove('flex');
}
</script>
@endpush

