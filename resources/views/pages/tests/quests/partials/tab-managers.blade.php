<div id="tab-managers" class="tab-content hidden">
  <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    Organization Managers
  </h3>

  <div class="space-y-4">
    @if($quest->organization->members && $quest->organization->members->count() > 0)
      @foreach($quest->organization->members as $member)
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 bg-gray-50 rounded-xl">
          <div class="flex items-center gap-4 flex-1">
            @if($member->user->avatar_url)
              <img src="{{ $member->user->avatar_url }}" alt="{{ $member->user->name }}" class="w-16 h-16 rounded-full">
            @else
              <div class="w-16 h-16 rounded-full bg-green-600 flex items-center justify-center text-white text-2xl font-bold">
                {{ substr($member->user->name, 0, 1) }}
              </div>
            @endif
            <div class="flex-1">
              <h4 class="font-bold text-gray-900">{{ $member->user->name }}</h4>
              <p class="text-sm text-gray-600 capitalize">{{ $member->role }}</p>
              <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-500">
                @if($member->user->email)
                  <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $member->user->email }}
                  </span>
                @endif
              </div>
            </div>
          </div>
          <div class="flex gap-2">
            @if($member->user->email)
              <a href="mailto:{{ $member->user->email }}" 
                 class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email
              </a>
            @endif
          </div>
        </div>
      @endforeach
    @else
      <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p class="text-gray-500">No managers found</p>
      </div>
    @endif
  </div>

  <!-- Organization Contact -->
  @if($quest->organization->org_email || $quest->organization->website_url || $quest->organization->instagram_url || $quest->organization->x_url || $quest->organization->facebook_url)
    <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-xl">
      <h4 class="font-bold text-gray-900 mb-4">Organization Contact</h4>
      <div class="space-y-2 text-sm">
        @if($quest->organization->org_email)
          <p class="flex items-center gap-2 text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <a href="mailto:{{ $quest->organization->org_email }}" class="hover:text-blue-600">{{ $quest->organization->org_email }}</a>
          </p>
        @endif
        @if($quest->organization->website_url)
          <p class="flex items-center gap-2 text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
            </svg>
            <a href="{{ $quest->organization->website_url }}" target="_blank" class="hover:text-blue-600">{{ $quest->organization->website_url }}</a>
          </p>
        @endif
        <div class="flex gap-3 mt-3">
          @if($quest->organization->instagram_url)
            <a href="{{ $quest->organization->instagram_url }}" target="_blank" class="text-pink-600 hover:text-pink-700">Instagram</a>
          @endif
          @if($quest->organization->x_url)
            <a href="{{ $quest->organization->x_url }}" target="_blank" class="text-gray-600 hover:text-gray-700">X</a>
          @endif
          @if($quest->organization->facebook_url)
            <a href="{{ $quest->organization->facebook_url }}" target="_blank" class="text-blue-600 hover:text-blue-700">Facebook</a>
          @endif
        </div>
      </div>
    </div>
  @endif
</div>
