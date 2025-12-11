<div id="tab-attendance" class="tab-content hidden">
  @php
    $now = now();
    $questActive = $now->between($quest->quest_start_at, $quest->quest_end_at);
    $lastAttendance = $userParticipation->questAttendances()->orderBy('created_at', 'desc')->first();
    $hasSubmission = $submissions && $submissions->count() > 0;
    
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
        Proof Submitted
      </div>
      <p class="text-sm text-amber-700">You have submitted your proof. Attendance is now disabled. {{ $canCheckOut ? 'You can still check-out if you haven\'t already.' : '' }}</p>
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
      Attendance Check-in/out
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
              <p class="text-sm text-emerald-700">Mark your arrival</p>
            </div>
          </div>
          @if($canCheckIn)
            <button onclick="openAttendanceModal('CHECK_IN')" class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors">
              Check-In Now
            </button>
          @elseif($hasSubmission)
            <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
              Disabled (Proof Submitted)
            </button>
          @else
            <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
              Already Checked-In
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
              <p class="text-sm text-red-700">Mark your departure</p>
            </div>
          </div>
          @if($canCheckOut)
            <button onclick="openAttendanceModal('CHECK_OUT')" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
              Check-Out Now
            </button>
          @elseif($hasSubmission && $lastAttendance && $lastAttendance->type === 'CHECK_OUT')
            <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
              Already Checked-Out
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
            <p class="text-sm text-blue-700 mt-1">Distance from location: {{ number_format($lastAttendance->distance_from_quest_location, 2) }}m</p>
          @endif
        </div>
      @endif

      <!-- Attendance History -->
      <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Attendance History
        </h4>
        @php
          $attendances = $userParticipation->questAttendances()->orderBy('created_at', 'desc')->get();
        @endphp
        @if($attendances->count() > 0)
          <div class="space-y-3">
            @foreach($attendances as $attendance)
              <div class="flex items-start gap-4 p-3 bg-gray-50 rounded-lg">
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
                  <p class="font-semibold text-gray-900">{{ $attendance->type }}</p>
                  <p class="text-sm text-gray-600">{{ $attendance->created_at->format('M d, Y H:i:s') }}</p>
                  @if($attendance->distance_from_quest_location)
                    <p class="text-xs text-gray-500">Distance: {{ number_format($attendance->distance_from_quest_location, 2) }}m</p>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-gray-500 text-center py-4">No attendance records yet</p>
        @endif
      </div>
    @else
      <div class="text-center py-12 bg-gray-50 rounded-xl">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-600 font-medium">Quest is not currently active</p>
        <p class="text-sm text-gray-500 mt-2">Active period: {{ $quest->quest_start_at->format('M d, Y H:i') }} - {{ $quest->quest_end_at->format('M d, Y H:i') }}</p>
      </div>
    @endif
  </div>
</div>

<!-- Attendance Modal -->
<div id="attendance-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeAttendanceModal(event)">
  <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4" onclick="event.stopPropagation()">
    <h3 class="text-2xl font-bold mb-4" id="modal-title">Check-In</h3>
    <div id="location-status" class="mb-6">
      <p class="text-gray-600">Verifying your location...</p>
    </div>
    <form id="attendance-form" method="POST" action="" class="hidden">
      @csrf
      <input type="hidden" name="type" id="attendance-type">
      <input type="hidden" name="latitude" id="user-latitude">
      <input type="hidden" name="longitude" id="user-longitude">
      <input type="hidden" name="liveness_code" id="liveness-code-input">
      
      <div class="mb-6">
        <label for="liveness-code" class="block text-sm font-medium text-gray-700 mb-2">Enter Liveness Code</label>
        <input type="text" id="liveness-code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Enter code" required>
      </div>

      <div class="flex gap-3">
        <button type="button" onclick="closeAttendanceModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold">
          Cancel
        </button>
        <button type="submit" class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg">
          Confirm
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
let attendanceType = '';

function openAttendanceModal(type) {
  attendanceType = type;
  document.getElementById('attendance-modal').classList.remove('hidden');
  document.getElementById('attendance-modal').classList.add('flex');
  document.getElementById('modal-title').textContent = type === 'CHECK_IN' ? 'Check-In' : 'Check-Out';
  document.getElementById('attendance-type').value = type;
  
  // Set the correct form action based on type
  const form = document.getElementById('attendance-form');
  const questId = {{ $quest->id }};
  if (type === 'CHECK_IN') {
    form.action = `/quests/${questId}/attendance/check-in`;
  } else {
    form.action = `/quests/${questId}/attendance/check-out`;
  }
  
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
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
              <p class="text-emerald-800 font-semibold">✓ Location Verified</p>
              <p class="text-sm text-emerald-600 mt-1">You are ${distance.toFixed(2)}m from the quest location (within ${questRadius}m radius)</p>
            </div>
          `;
          formDiv.classList.remove('hidden');
        } else {
          statusDiv.innerHTML = `
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
              <p class="text-red-800 font-semibold">✗ Location Out of Range</p>
              <p class="text-sm text-red-600 mt-1">You are ${distance.toFixed(2)}m from the quest location. Must be within ${questRadius}m.</p>
            </div>
          `;
          formDiv.classList.add('hidden');
        }
      },
      (error) => {
        document.getElementById('location-status').innerHTML = `
          <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 font-semibold">Location access denied</p>
            <p class="text-sm text-red-600 mt-1">Please enable location services to check in/out.</p>
          </div>
        `;
      }
    );
  }
}

function closeAttendanceModal(event) {
  if (!event || event.target.id === 'attendance-modal') {
    document.getElementById('attendance-modal').classList.add('hidden');
    document.getElementById('attendance-modal').classList.remove('flex');
    document.getElementById('liveness-code').value = '';
  }
}

document.getElementById('attendance-form')?.addEventListener('submit', function(e) {
  const code = document.getElementById('liveness-code').value;
  document.getElementById('liveness-code-input').value = code;
});

function calculateDistance(lat1, lon1, lat2, lon2) {
  const R = 6371e3; // Earth radius in meters
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
</script>
@endpush
