<div id="tab-dashboard" class="tab-content hidden">
  @php
    $now = now();
    $questActive = $now->between($quest->quest_start_at, $quest->quest_end_at);
    $lastAttendance = $userParticipation->questAttendances()->orderBy('created_at', 'desc')->first();
    $canCheckIn = !$lastAttendance || $lastAttendance->type === 'CHECK_OUT';
    $canCheckOut = $lastAttendance && $lastAttendance->type === 'CHECK_IN';
  @endphp

  <!-- Success/Error Messages -->
  @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-xl text-green-800 font-medium">
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

  <!-- Hidden Quest Data for JavaScript -->
  <input type="hidden" id="quest-latitude" value="{{ $quest->latitude }}">
  <input type="hidden" id="quest-longitude" value="{{ $quest->longitude }}">
  <input type="hidden" id="quest-radius" value="{{ $quest->radius_meter ?? 100 }}">

  <!-- Attendance Section -->
  <div class="mb-8">
    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
      <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Attendance Check-in/out
    </h3>

    @if($questActive)
      <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Check-in Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border-2 {{ $canCheckIn ? 'border-green-300' : 'border-gray-300 opacity-50' }}">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-green-600 flex items-center justify-center">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
            </div>
            <div>
              <h4 class="font-bold text-green-900 text-lg">Check-In</h4>
              <p class="text-sm text-green-700">Mark your arrival</p>
            </div>
          </div>
          @if($canCheckIn)
            <button onclick="openAttendanceModal('CHECK_IN')" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
              Check-In Now
            </button>
          @else
            <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
              Already Checked-In
            </button>
          @endif
        </div>

        <!-- Check-out Card -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 border-2 {{ $canCheckOut ? 'border-red-300' : 'border-gray-300 opacity-50' }}">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
            </div>
            <div>
              <h4 class="font-bold text-red-900 text-lg">Check-Out</h4>
              <p class="text-sm text-red-700">Mark your departure</p>
            </div>
          </div>
          @if($canCheckOut)
            <button onclick="openAttendanceModal('CHECK_OUT')" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
              Check-Out Now
            </button>
          @else
            <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
              Must Check-In First
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
            Current Status
          </div>
          <p class="text-blue-800">Last action: <strong>{{ $lastAttendance->type }}</strong> at {{ $lastAttendance->created_at->format('M d, Y H:i') }}</p>
          @if($lastAttendance->distance_from_quest_location)
            <p class="text-blue-800">Distance: <strong>{{ number_format($lastAttendance->distance_from_quest_location, 1) }}m</strong> 
              @if($lastAttendance->is_valid_location)
                <span class="text-green-600">✓ Valid</span>
              @else
                <span class="text-red-600">⚠ Outside radius</span>
              @endif
            </p>
          @endif
        </div>
      @endif
    @else
      <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-yellow-800 font-medium mb-2">Quest Not Active</p>
        <p class="text-sm text-yellow-700">Quest period: {{ $quest->quest_start_at->format('M d, Y H:i') }} - {{ $quest->quest_end_at->format('M d, Y H:i') }}</p>
      </div>
    @endif
  </div>

  <!-- Attendance History -->
  <div>
    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
      <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Attendance History
    </h3>

    @php
      $attendances = $userParticipation->questAttendances()->orderBy('created_at', 'desc')->get();
    @endphp

    @if($attendances->count() > 0)
      <div class="space-y-4">
        @foreach($attendances as $attendance)
          <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full {{ $attendance->type === 'CHECK_IN' ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 {{ $attendance->type === 'CHECK_IN' ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  @if($attendance->type === 'CHECK_IN')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                  @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  @endif
                </svg>
              </div>

              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <h4 class="font-bold text-gray-900 text-lg">{{ $attendance->type }}</h4>
                  @if($attendance->is_valid_location)
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Valid Location</span>
                  @else
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Outside Radius</span>
                  @endif
                </div>

                <p class="text-sm text-gray-600 mb-2">
                  <strong>Time:</strong> {{ $attendance->created_at->format('M d, Y H:i:s') }} ({{ $attendance->created_at->diffForHumans() }})
                </p>

                @if($attendance->distance_from_quest_location)
                  <p class="text-sm text-gray-600 mb-2">
                    <strong>Distance:</strong> {{ number_format($attendance->distance_from_quest_location, 1) }} meters from quest location
                  </p>
                @endif

                <p class="text-sm text-gray-600 mb-3">
                  <strong>Location:</strong> {{ $attendance->proof_latitude }}, {{ $attendance->proof_longitude }}
                </p>

                @if($attendance->notes)
                  <p class="text-sm text-gray-700 bg-gray-50 rounded p-2 mb-3">
                    <strong>Notes:</strong> {{ $attendance->notes }}
                  </p>
                @endif

                @if($attendance->proof_photo_url)
                  <button onclick="showAttendancePhoto('{{ $attendance->proof_photo_url }}')" 
                          class="inline-flex items-center gap-2 text-sm text-green-600 hover:text-green-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    View Proof Photo
                  </button>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-12 bg-gray-50 rounded-xl">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-500">No attendance records yet</p>
      </div>
    @endif
  </div>

  <!-- Submit Proof Section (below attendance) -->
  @if($userParticipation->status === 'REGISTERED')
    <div class="mt-10 pt-10 border-t border-gray-200">
      <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        Submit Your Final Proof
      </h3>

      <form id="submit-proof-form" class="space-y-4" enctype="multipart/form-data">
        @csrf
        
        <!-- Video Upload -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Proof Video <span class="text-red-500">*</span>
          </label>
          <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-500 transition-colors cursor-pointer" id="video-upload-area">
            <input type="file" name="video" id="video-input" accept="video/*" class="hidden" required>
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-600 mb-1">Click to upload video</p>
            <p class="text-xs text-gray-500">MP4, MOV, AVI up to 100MB</p>
          </div>
          <div id="video-preview" class="mt-2 hidden">
            <p class="text-sm text-gray-600">Selected: <span id="video-name" class="font-medium"></span></p>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Description <span class="text-red-500">*</span>
          </label>
          <textarea 
            id="description" 
            name="description" 
            rows="6" 
            required
            minlength="50"
            maxlength="10000"
            placeholder="Describe your contribution to this quest... (minimum 50 characters)"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
          ></textarea>
          <p class="text-xs text-gray-500 mt-1"><span id="char-count">0</span> / 10000 characters (min: 50)</p>
        </div>

        <!-- Submit Button -->
        <div id="submit-message" class="hidden"></div>
        <button 
          type="submit" 
          id="submit-btn"
          class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
          Submit Proof
        </button>
      </form>
    </div>
  @endif
</div>

<!-- Attendance Modal -->
<div id="attendance-modal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[95vh] overflow-hidden shadow-2xl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 p-6 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div id="modal-icon" class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-2xl font-bold text-white" id="modal-title">Check-In</h3>
          <p class="text-green-100 text-sm">Record your attendance</p>
        </div>
      </div>
      <button onclick="closeAttendanceModal()" class="text-white/80 hover:text-white transition-colors">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Content -->
    <div class="p-6 overflow-y-auto" style="max-height: calc(95vh - 180px);">
      <form id="attendance-form" method="POST" action="" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" id="attendance-type">
        <input type="hidden" name="latitude" id="attendance-latitude">
        <input type="hidden" name="longitude" id="attendance-longitude">

        <!-- Location Status -->
        <div id="location-status" class="mb-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-xl">
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            <p class="text-sm text-blue-800 font-medium">Getting your location...</p>
          </div>
        </div>

        <!-- Camera Capture -->
        <div class="mb-6">
          <label class="block text-sm font-bold text-gray-900 mb-3">
            Proof Photo <span class="text-red-500">*</span>
          </label>
          <div class="space-y-3">
            <video id="camera-preview" autoplay playsinline class="w-full rounded-xl bg-gray-900 hidden shadow-lg"></video>
            <canvas id="camera-canvas" class="hidden"></canvas>
            <img id="captured-photo" class="w-full rounded-xl hidden shadow-lg border-4 border-green-500">
            
            <div class="flex gap-3">
              <button type="button" id="start-camera-btn" onclick="startCamera()" class="flex-1 px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-semibold shadow-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Start Camera
              </button>
              <button type="button" id="capture-btn" onclick="capturePhoto()" class="hidden flex-1 px-5 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl font-semibold shadow-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                </svg>
                📸 Capture
              </button>
              <button type="button" id="retake-btn" onclick="retakePhoto()" class="hidden flex-1 px-5 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white rounded-xl font-semibold shadow-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Retake
              </button>
            </div>
          </div>
          <input type="file" name="proof_photo" id="proof-photo-input" accept="image/*" class="hidden">
        </div>

        <!-- Notes -->
        <div class="mb-6">
          <label for="attendance-notes" class="block text-sm font-bold text-gray-900 mb-3">
            Notes <span class="text-gray-400 font-normal">(Optional)</span>
          </label>
          <textarea 
            id="attendance-notes" 
            name="notes" 
            rows="3"
            placeholder="Any additional notes about your attendance..."
            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
          ></textarea>
        </div>

        <div id="attendance-message" class="mb-4 hidden"></div>

        <button 
          type="submit" 
          id="attendance-submit-btn"
          class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg text-lg flex items-center justify-center gap-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Submit Attendance
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Photo View Modal -->
<div id="photo-modal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4" onclick="closePhotoModal()">
  <div class="relative max-w-4xl w-full">
    <button onclick="closePhotoModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
    <img id="photo-view" src="" class="w-full h-auto rounded-lg">
  </div>
</div>

<script>
let cameraStream = null;
let capturedBlob = null;

// Video upload handling
const videoInput = document.getElementById('video-input');
const videoUploadArea = document.getElementById('video-upload-area');
const videoPreview = document.getElementById('video-preview');
const videoName = document.getElementById('video-name');

if (videoUploadArea) {
  videoUploadArea.addEventListener('click', () => videoInput.click());

  videoInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
      videoName.textContent = file.name;
      videoPreview.classList.remove('hidden');
    }
  });
}

// Character count
const descriptionTextarea = document.getElementById('description');
const charCount = document.getElementById('char-count');

if (descriptionTextarea) {
  descriptionTextarea.addEventListener('input', () => {
    charCount.textContent = descriptionTextarea.value.length;
  });
}

// Attendance Modal
function openAttendanceModal(type) {
  const modal = document.getElementById('attendance-modal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  document.getElementById('attendance-type').value = type;
  document.getElementById('modal-title').textContent = type === 'CHECK_IN' ? 'Check-In' : 'Check-Out';
  
  // Set form action based on type
  const form = document.getElementById('attendance-form');
  const questId = {{ $quest->id }};
  const endpoint = type === 'CHECK_IN' ? `/quests/${questId}/attendance/check-in` : `/quests/${questId}/attendance/check-out`;
  form.action = endpoint;
  
  getLocation();
  console.log('Opening modal for:', type, 'with endpoint:', endpoint);
}

function closeAttendanceModal() {
  const modal = document.getElementById('attendance-modal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  if (cameraStream) {
    cameraStream.getTracks().forEach(track => track.stop());
    cameraStream = null;
  }
  document.getElementById('attendance-form').reset();
  document.getElementById('camera-preview').classList.add('hidden');
  document.getElementById('captured-photo').classList.add('hidden');
  document.getElementById('start-camera-btn').classList.remove('hidden');
  document.getElementById('capture-btn').classList.add('hidden');
  document.getElementById('retake-btn').classList.add('hidden');
  document.getElementById('attendance-message').classList.add('hidden');
  console.log('Modal closed');
}

// Haversine formula to calculate distance
function calculateDistance(lat1, lon1, lat2, lon2) {
  const earthRadius = 6371000; // meters
  const lat1Rad = lat1 * Math.PI / 180;
  const lat2Rad = lat2 * Math.PI / 180;
  const deltaLat = (lat2 - lat1) * Math.PI / 180;
  const deltaLon = (lon2 - lon1) * Math.PI / 180;

  const a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
           Math.cos(lat1Rad) * Math.cos(lat2Rad) *
           Math.sin(deltaLon / 2) * Math.sin(deltaLon / 2);

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  const distance = earthRadius * c;

  return distance; // returns distance in meters
}

function getLocation() {
  const statusDiv = document.getElementById('location-status');
  const questLat = parseFloat(document.getElementById('quest-latitude').value);
  const questLon = parseFloat(document.getElementById('quest-longitude').value);
  const questRadius = parseFloat(document.getElementById('quest-radius').value);
  
  if (navigator.geolocation) {
    console.log('Getting location...');
    console.log('Quest location:', questLat, questLon, 'Radius:', questRadius);
    
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        // Store with maximum precision (15 decimal places)
        document.getElementById('attendance-latitude').value = lat.toFixed(15);
        document.getElementById('attendance-longitude').value = lon.toFixed(15);
        
        // Calculate distance from quest location
        const distance = calculateDistance(questLat, questLon, lat, lon);
        const isValid = distance <= questRadius;
        
        console.log('User location (high precision):', lat.toFixed(15), lon.toFixed(15));
        console.log('Distance from quest:', distance.toFixed(6), 'meters');
        console.log('Is valid:', isValid);
        
        if (isValid) {
          statusDiv.className = 'mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-xl';
          statusDiv.innerHTML = '<div class="flex items-center gap-3"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div><p class="text-sm text-green-800 font-bold">✓ Location Valid!</p><p class="text-xs text-green-700">You are ' + distance.toFixed(2) + 'm from quest location (within ' + questRadius + 'm radius)</p></div></div>';
        } else {
          statusDiv.className = 'mb-6 p-4 bg-yellow-50 border-2 border-yellow-200 rounded-xl';
          statusDiv.innerHTML = '<div class="flex items-center gap-3"><svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><div><p class="text-sm text-yellow-800 font-bold">⚠ Location Outside Radius</p><p class="text-xs text-yellow-700">You are ' + distance.toFixed(2) + 'm away (limit: ' + questRadius + 'm). <strong>You can still submit</strong>, but it will be marked as outside radius.</p></div></div>';
        }
      },
      (error) => {
        statusDiv.className = 'mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-xl';
        statusDiv.innerHTML = '<div class="flex items-center gap-3"><svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-sm text-red-800 font-medium">⚠ Failed to get location. Please enable location services.</p></div>';
        console.error('Location error:', error);
      }
    );
  } else {
    statusDiv.className = 'mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-xl';
    statusDiv.innerHTML = '<div class="flex items-center gap-3"><svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-sm text-red-800 font-medium">⚠ Geolocation not supported by your browser.</p></div>';
    console.error('Geolocation not supported');
  }
}

async function startCamera() {
  try {
    console.log('Starting camera...');
    cameraStream = await navigator.mediaDevices.getUserMedia({ 
      video: { facingMode: 'environment' }, 
      audio: false 
    });
    
    const preview = document.getElementById('camera-preview');
    preview.srcObject = cameraStream;
    preview.classList.remove('hidden');
    
    document.getElementById('start-camera-btn').classList.add('hidden');
    document.getElementById('capture-btn').classList.remove('hidden');
    console.log('Camera started');
  } catch (error) {
    console.error('Camera error:', error);
    alert('Failed to access camera: ' + error.message);
  }
}

function capturePhoto() {
  console.log('Capturing photo...');
  const video = document.getElementById('camera-preview');
  const canvas = document.getElementById('camera-canvas');
  const capturedImg = document.getElementById('captured-photo');
  
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  canvas.getContext('2d').drawImage(video, 0, 0);
  
  canvas.toBlob((blob) => {
    capturedBlob = blob;
    capturedImg.src = URL.createObjectURL(blob);
    capturedImg.classList.remove('hidden');
    
    video.classList.add('hidden');
    document.getElementById('capture-btn').classList.add('hidden');
    document.getElementById('retake-btn').classList.remove('hidden');
    
    if (cameraStream) {
      cameraStream.getTracks().forEach(track => track.stop());
      cameraStream = null;
    }
    console.log('Photo captured, size:', blob.size);
  }, 'image/jpeg', 0.95);
}

function retakePhoto() {
  console.log('Retaking photo...');
  document.getElementById('captured-photo').classList.add('hidden');
  document.getElementById('retake-btn').classList.add('hidden');
  document.getElementById('start-camera-btn').classList.remove('hidden');
  capturedBlob = null;
}

// Attendance Form Submission - Use FormData and fetch
const attendanceForm = document.getElementById('attendance-form');
if (attendanceForm) {
  attendanceForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    console.log('Submitting attendance form...');
    
    if (!capturedBlob) {
      alert('Please capture a photo first!');
      console.error('No photo captured');
      return false;
    }
    
    const latitude = document.getElementById('attendance-latitude').value;
    const longitude = document.getElementById('attendance-longitude').value;
    
    if (!latitude || !longitude) {
      alert('Location not available. Please allow location access and try again.');
      console.error('No location data');
      return false;
    }
    
    // Show loading state
    const btn = document.getElementById('attendance-submit-btn');
    const messageDiv = document.getElementById('attendance-message');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-6 w-6 text-white inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
    
    try {
      // Create FormData and add all form fields
      const formData = new FormData();
      formData.append('_token', document.querySelector('input[name="_token"]').value);
      formData.append('latitude', latitude);
      formData.append('longitude', longitude);
      formData.append('notes', document.getElementById('attendance-notes').value || '');
      
      // Add photo blob as file
      const file = new File([capturedBlob], 'attendance_' + Date.now() + '.jpg', { type: 'image/jpeg' });
      formData.append('proof_photo', file);
      
      console.log('Submitting to:', attendanceForm.action);
      
      // Submit via fetch
      const response = await fetch(attendanceForm.action, {
        method: 'POST',
        body: formData,
        redirect: 'manual' // Handle redirects manually
      });
      
      console.log('Response status:', response.status);
      console.log('Response type:', response.type);
      
      // Handle redirect (302, 301, etc) - might be auth redirect
      if (response.type === 'opaqueredirect' || response.status === 302 || response.status === 301) {
        messageDiv.classList.remove('hidden');
        messageDiv.className = 'mb-4 p-4 bg-yellow-50 border-2 border-yellow-200 rounded-xl text-yellow-800 font-medium';
        setTimeout(() => location.reload(), 1500);
        return;
      }
      
      // Handle unauthorized
      if (response.status === 401 || response.status === 403) {
        messageDiv.classList.remove('hidden');
        messageDiv.className = 'mb-4 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-800 font-medium';
        messageDiv.textContent = 'You must be logged in to submit attendance.';
        
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Submit Attendance';
        return;
      }
      
      // Check if response is JSON or HTML
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        const data = await response.json();
        
        messageDiv.classList.remove('hidden');
        messageDiv.className = data.success 
          ? 'mb-4 p-4 bg-green-50 border-2 border-green-200 rounded-xl text-green-800 font-medium'
          : 'mb-4 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-800 font-medium';
        messageDiv.textContent = data.message || 'Attendance recorded!';
        
        if (data.success || response.ok) {
          setTimeout(() => location.reload(), 1500);
        } else {
          btn.disabled = false;
          btn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Submit Attendance';
        }
      } else {
        // HTML response - parse errors from redirect back
        const text = await response.text();
        console.log('HTML Response received');
        
        if (response.ok) {
          messageDiv.classList.remove('hidden');
          messageDiv.className = 'mb-4 p-4 bg-green-50 border-2 border-green-200 rounded-xl text-green-800 font-medium';
          messageDiv.textContent = 'Attendance recorded successfully!';
          setTimeout(() => location.reload(), 1500);
        } else {
          // Extract error message from HTML if possible
          const parser = new DOMParser();
          const doc = parser.parseFromString(text, 'text/html');
          const errorMsg = doc.querySelector('.error-message, .alert-danger, [class*="error"]');
          
          messageDiv.classList.remove('hidden');
          messageDiv.className = 'mb-4 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-800 font-medium';
          messageDiv.textContent = errorMsg ? errorMsg.textContent.trim() : 'Failed to submit attendance. Status: ' + response.status;
          
          btn.disabled = false;
          btn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Submit Attendance';
        }
      }
    } catch (error) {
      console.error('Attendance submission error:', error);
      messageDiv.classList.remove('hidden');
      messageDiv.className = 'mb-4 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-800 font-medium';
      messageDiv.textContent = 'Failed to submit attendance. Please try again.';
      
      btn.disabled = false;
      btn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Submit Attendance';
    }
  });
}

// Submit Proof Form
const submitProofForm = document.getElementById('submit-proof-form');
if (submitProofForm) {
  submitProofForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn = document.getElementById('submit-btn');
    const submitMessage = document.getElementById('submit-message');
    const formData = new FormData(submitProofForm);

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Uploading...';

    try {
      const response = await fetch('/quests/{{ $quest->id }}/submit-proof', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
      });

      const data = await response.json();

      submitMessage.classList.remove('hidden');
      submitMessage.className = data.success 
        ? 'bg-green-50 border border-green-200 rounded-lg p-4 text-green-800' 
        : 'bg-red-50 border border-red-200 rounded-lg p-4 text-red-800';
      submitMessage.textContent = data.message;

      if (data.success) {
        setTimeout(() => location.reload(), 2000);
      } else {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg> Submit Proof';
      }
    } catch (error) {
      submitMessage.classList.remove('hidden');
      submitMessage.className = 'bg-red-50 border border-red-200 rounded-lg p-4 text-red-800';
      submitMessage.textContent = 'Failed to submit. Please try again.';
      
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg> Submit Proof';
    }
  });
}

function showAttendancePhoto(url) {
  document.getElementById('photo-view').src = url;
  document.getElementById('photo-modal').classList.remove('hidden');
}

function closePhotoModal() {
  document.getElementById('photo-modal').classList.add('hidden');
}
</script>


