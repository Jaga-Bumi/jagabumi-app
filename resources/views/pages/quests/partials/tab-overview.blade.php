<div id="tab-overview" class="tab-content">
  <!-- Description -->
  <div class="mb-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4">About This Quest</h3>
    <div class="prose max-w-none text-gray-600 whitespace-pre-line">
      {{ $quest->desc }}
    </div>
  </div>

  <!-- Schedule -->
  <div class="mb-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      Schedule
    </h3>

    <div class="grid md:grid-cols-2 gap-4">
      <div class="p-4 bg-gray-50 rounded-lg">
        <p class="text-sm text-gray-500 mb-1">Registration Period</p>
        <p class="font-medium text-gray-900">{{ $quest->registration_start_at->format('M d, Y') }} - {{ $quest->registration_end_at->format('M d, Y') }}</p>
      </div>
      <div class="p-4 bg-gray-50 rounded-lg">
        <p class="text-sm text-gray-500 mb-1">Quest Date & Time</p>
        <p class="font-medium text-gray-900">{{ $quest->quest_start_at->format('M d, Y H:i') }} - {{ $quest->quest_end_at->format('H:i') }}</p>
      </div>
      <div class="p-4 bg-gray-50 rounded-lg">
        <p class="text-sm text-gray-500 mb-1">Judging Period</p>
        <p class="font-medium text-gray-900">{{ $quest->judging_start_at->format('M d, Y') }} - {{ $quest->judging_end_at->format('M d, Y') }}</p>
      </div>
      <div class="p-4 bg-gray-50 rounded-lg">
        <p class="text-sm text-gray-500 mb-1">Prize Distribution</p>
        <p class="font-medium text-gray-900">{{ $quest->prize_distribution_date->format('M d, Y') }}</p>
      </div>
    </div>
  </div>

  <!-- Location -->
  <div class="mb-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
      <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      Location
    </h3>

    <div class="bg-muted/50 rounded-xl p-4 mb-4 border border-border">
      <div class="flex items-start gap-3 mb-4">
        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <div>
          <p class="font-semibold text-foreground text-lg">{{ $quest->location_name }}</p>
          <p class="text-sm text-muted-foreground mt-1">Check-in Radius: {{ $quest->radius_meter }} meters</p>
        </div>
      </div>
    </div>

    <!-- Interactive Map -->
    <div class="rounded-xl overflow-hidden border-2 border-border shadow-lg mb-4">
      <iframe 
        width="100%" 
        height="450" 
        style="border:0" 
        loading="lazy" 
        allowfullscreen
        referrerpolicy="no-referrer-when-downgrade"
        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q={{ $quest->latitude }},{{ $quest->longitude }}&zoom=15&maptype=roadmap">
      </iframe>
    </div>

    <div class="flex gap-3">
      <a href="https://www.google.com/maps?q={{ $quest->latitude }},{{ $quest->longitude }}" target="_blank" rel="noopener noreferrer" 
         class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-all shadow-lg shadow-emerald-500/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        </svg>
        Open in Google Maps
      </a>
      <a href="https://www.google.com/maps/dir/?api=1&destination={{ $quest->latitude }},{{ $quest->longitude }}" target="_blank" rel="noopener noreferrer" 
         class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950 rounded-lg font-medium transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
        Get Directions
      </a>
    </div>
  </div>

  <!-- Liveness Code -->
  @if($quest->liveness_code)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
      <h3 class="text-lg font-bold text-yellow-900 mb-2 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Attendance Liveness Code
      </h3>
      <p class="text-sm text-yellow-800 mb-2">Use this code when checking in at the quest location</p>
      <p class="font-mono font-bold text-2xl text-yellow-900">{{ $quest->liveness_code }}</p>
    </div>
  @endif
</div>
