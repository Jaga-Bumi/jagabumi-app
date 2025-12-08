<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="auth-route" content="{{ route('auth.web3') }}">
  <meta name="logout-route" content="{{ route('logout') }}">
  <meta name="web3auth-client-id" content="{{ config('services.web3auth.client_id') }}">
  <meta name="web3auth-network" content="{{ config('services.web3auth.network') }}">
  <title>{{ $quest->title }} - Quest Detail</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <!-- Navigation -->
  <nav class="bg-white shadow-sm border-b sticky top-0 z-40">
    <div class="container mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-6">
          <a href="{{ route('home') }}" class="text-xl font-bold text-green-600">JagaBumi</a>
          <div class="hidden md:flex items-center gap-4 text-sm">
            <a href="{{ route('quests.all') }}" class="text-gray-600 hover:text-green-600">Quests</a>
            <a href="{{ route('organizations.all') }}" class="text-gray-600 hover:text-green-600">Organizations</a>
            <a href="{{ route('articles.all') }}" class="text-gray-600 hover:text-green-600">Articles</a>
            <a href="{{ route('leaderboard') }}" class="text-gray-600 hover:text-green-600">Leaderboard</a>
          </div>
        </div>
        <div class="flex items-center gap-3">
          @if (auth()->user())
            <a href="{{ route('join-us') }}" class="text-sm text-gray-600 hover:text-green-600">Join Us</a>
            <button id="logout-btn" type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">Logout</button>
          @else
            <button id="auth-btn" type="button" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm">Login / Register</button>
          @endif
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Banner -->
  <div class="relative h-64 md:h-80 lg:h-96">
    @if($quest->banner_url)
      <img src="{{ $quest->banner_url }}" alt="{{ $quest->title }}" class="w-full h-full object-cover">
    @else
      <div class="w-full h-full bg-gradient-to-r from-green-400 to-blue-500"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent"></div>
    
    <!-- Back Button -->
    <div class="absolute top-4 left-4">
      <a href="{{ route('quests.all') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/90 backdrop-blur-sm hover:bg-white rounded-lg text-sm font-medium shadow-lg">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Quests
      </a>
    </div>

    <!-- Share Button -->
    <div class="absolute top-4 right-4">
      <button class="p-2 bg-white/90 backdrop-blur-sm hover:bg-white rounded-lg shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
        </svg>
      </button>
    </div>
  </div>

  <div class="container mx-auto px-4 -mt-16 relative z-10 pb-20">
    <div class="grid lg:grid-cols-4 gap-8">
      <!-- Main Content - Tabs Section (3 columns) -->
      <div class="lg:col-span-3 space-y-6">
        <!-- Title Card -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex flex-wrap gap-2 mb-4">
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">{{ $quest->status }}</span>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">{{ $quest->quest_participants_count }}/{{ $quest->participant_limit }} Joined</span>
          </div>

          <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $quest->title }}</h1>

          <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('organizations.all') }}" class="flex items-center gap-2 hover:text-green-600">
              @if($quest->organization->logo_img)
                <img src="{{ $quest->organization->logo_img }}" alt="{{ $quest->organization->name }}" class="w-8 h-8 rounded-full">
              @else
                <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white font-bold">
                  {{ substr($quest->organization->name, 0, 1) }}
                </div>
              @endif
              <span class="font-medium">{{ $quest->organization->name }}</span>
            </a>
          </div>

          <div class="flex flex-wrap gap-4 text-sm text-gray-600">
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              {{ $quest->location_name }}
            </span>
          </div>

          <!-- Progress Bar -->
          <div class="mt-6">
            <div class="flex justify-between text-sm mb-2">
              <span class="text-gray-600">Participants</span>
              <span class="text-green-600 font-medium">{{ number_format(($quest->quest_participants_count / $quest->participant_limit) * 100, 0) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($quest->quest_participants_count / $quest->participant_limit) * 100 }}%"></div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
          <div class="bg-gradient-to-r from-green-50 to-blue-50 border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto" id="tab-nav">
              <button class="tab-button active px-8 py-5 text-sm font-semibold border-b-3 border-green-600 text-green-600 whitespace-nowrap transition-all hover:bg-white/50" data-tab="overview">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span>Overview</span>
                </div>
              </button>
              <button class="tab-button px-8 py-5 text-sm font-semibold border-b-3 border-transparent text-gray-500 hover:text-gray-700 hover:bg-white/50 whitespace-nowrap transition-all" data-tab="prizes">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                  </svg>
                  <span>Prizes</span>
                </div>
              </button>
              <button class="tab-button px-8 py-5 text-sm font-semibold border-b-3 border-transparent text-gray-500 hover:text-gray-700 hover:bg-white/50 whitespace-nowrap transition-all" data-tab="managers">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  <span>Managers</span>
                </div>
              </button>
              <button class="tab-button px-8 py-5 text-sm font-semibold border-b-3 border-transparent text-gray-500 hover:text-gray-700 hover:bg-white/50 whitespace-nowrap transition-all" data-tab="submissions">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  <span>Submissions</span>
                </div>
              </button>
              @if($userParticipation)
                <button class="tab-button px-8 py-5 text-sm font-semibold border-b-3 border-transparent text-gray-500 hover:text-gray-700 hover:bg-white/50 whitespace-nowrap transition-all" data-tab="dashboard">
                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Dashboard</span>
                  </div>
                </button>
              @else
                <button class="tab-button px-8 py-5 text-sm font-medium border-b-3 border-transparent text-gray-300 cursor-not-allowed whitespace-nowrap" disabled>
                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Dashboard</span>
                  </div>
                </button>
              @endif
            </nav>
          </div>

          <div class="p-8 bg-gradient-to-br from-white to-gray-50 min-h-[500px]">
            @include('pages.tests.quests.partials.tab-overview', ['quest' => $quest])
            @include('pages.tests.quests.partials.tab-prizes', ['quest' => $quest])
            @include('pages.tests.quests.partials.tab-managers', ['quest' => $quest])
            @include('pages.tests.quests.partials.tab-submissions', ['quest' => $quest, 'submissions' => $submissions])
            @if($userParticipation)
              @include('pages.tests.quests.partials.tab-dashboard', ['quest' => $quest, 'userParticipation' => $userParticipation])
            @endif
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Join Card -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="text-center mb-6">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent mb-2">
              Join This Quest
            </h2>
            <p class="text-gray-600">and make a difference</p>
          </div>

          <div id="message-box" style="display: none;" class="mb-4"></div>

          @php
            $now = now();
            $regOpen = $now->gte($quest->registration_start_at);
            $regClosed = $now->gt($quest->registration_end_at);
            $isFull = $quest->quest_participants_count >= $quest->participant_limit;
          @endphp

          @if(auth()->check())
            @if($userParticipation)
              <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-2 text-green-800 font-medium mb-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  You're Registered!
                </div>
                <p class="text-sm text-green-700">Status: <strong>{{ $userParticipation->status }}</strong></p>
                <p class="text-sm text-green-700">Joined: {{ $userParticipation->joined_at->format('M d, Y H:i') }}</p>
              </div>

              @if($userParticipation->status === 'REGISTERED' && !$regClosed)
                <button id="cancel-btn" data-quest-id="{{ $quest->id }}" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                  Cancel Registration
                </button>
                <p class="text-xs text-gray-500 mt-2 text-center">You can cancel before {{ $quest->registration_end_at->format('M d, Y H:i') }}</p>
              @endif
            @else
              @if($regOpen && !$regClosed && !$isFull)
                <button id="join-btn" data-quest-id="{{ $quest->id }}" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors text-lg">
                  Join Quest
                </button>
              @elseif(!$regOpen)
                <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                  Registration Not Started
                </button>
              @elseif($regClosed)
                <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                  Registration Closed
                </button>
              @elseif($isFull)
                <button disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                  Quest Full
                </button>
              @endif
            @endif
          @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
              <p class="text-sm text-blue-800 text-center font-medium">Please login to join this quest</p>
            </div>
            <button id="auth-btn-2" type="button" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
              Login / Register
            </button>
          @endif

          <div class="pt-6 mt-6 border-t border-gray-200 space-y-3">
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-600">Spots Left</span>
              <span class="font-semibold text-gray-900">{{ $quest->participant_limit - $quest->quest_participants_count }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-600">Winners Limit</span>
              <span class="font-semibold text-gray-900">{{ $quest->winner_limit }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-600">Quest Date</span>
              <span class="font-semibold text-gray-900">{{ $quest->quest_start_at->format('M d, Y') }}</span>
            </div>
          </div>
        </div>

        <!-- Timeline Card -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Timeline
          </h3>

          <div class="space-y-4">
            @php
              $timelineItems = [
                ['date' => $quest->registration_start_at, 'event' => 'Registration Opens', 'completed' => now()->gte($quest->registration_start_at)],
                ['date' => $quest->registration_end_at, 'event' => 'Registration Closes', 'completed' => now()->gte($quest->registration_end_at)],
                ['date' => $quest->quest_start_at, 'event' => 'Quest Starts', 'completed' => now()->gte($quest->quest_start_at)],
                ['date' => $quest->quest_end_at, 'event' => 'Quest Ends', 'completed' => now()->gte($quest->quest_end_at)],
                ['date' => $quest->judging_end_at, 'event' => 'Judging Ends', 'completed' => now()->gte($quest->judging_end_at)],
                ['date' => $quest->prize_distribution_date, 'event' => 'Prize Distribution', 'completed' => now()->gte($quest->prize_distribution_date)],
              ];
            @endphp

            @foreach($timelineItems as $item)
              <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $item['completed'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                  @if($item['completed'])
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  @endif
                </div>
                <div class="flex-1 pb-4 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                  <p class="font-medium {{ $item['completed'] ? 'text-gray-900' : 'text-gray-500' }}">{{ $item['event'] }}</p>
                  <p class="text-sm text-gray-500">{{ $item['date']->format('M d, Y') }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>

  @vite(['resources/js/auth.js', 'resources/js/logout.js'])

  <script>
    // Tab switching
    const tabButtons = document.querySelectorAll('.tab-button:not([disabled])');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
      button.addEventListener('click', () => {
        const tabId = button.dataset.tab;
        
        // Update button styles with enhanced active state
        tabButtons.forEach(btn => {
          btn.classList.remove('active', 'border-green-600', 'text-green-600', 'bg-white/50');
          btn.classList.add('border-transparent', 'text-gray-500');
        });
        button.classList.add('active', 'border-green-600', 'text-green-600', 'bg-white/50');
        button.classList.remove('border-transparent', 'text-gray-500');

        // Show/hide content
        tabContents.forEach(content => {
          if (content.id === 'tab-' + tabId) {
            content.classList.remove('hidden');
          } else {
            content.classList.add('hidden');
          }
        });
      });
    });

    // Join Quest
    const joinBtn = document.getElementById('join-btn');
    if (joinBtn) {
      joinBtn.addEventListener('click', async function() {
        const questId = this.dataset.questId;
        this.disabled = true;
        this.textContent = 'Joining...';

        try {
          const response = await fetch(`/quests/${questId}/join`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          });

          const data = await response.json();
          const messageBox = document.getElementById('message-box');
          messageBox.style.display = 'block';
          messageBox.className = data.success 
            ? 'bg-green-50 border border-green-200 rounded-lg p-4 text-green-800' 
            : 'bg-red-50 border border-red-200 rounded-lg p-4 text-red-800';
          messageBox.textContent = data.message;

          if (data.success) {
            setTimeout(() => location.reload(), 1500);
          } else {
            this.disabled = false;
            this.textContent = 'Join Quest';
          }
        } catch (error) {
          const messageBox = document.getElementById('message-box');
          messageBox.style.display = 'block';
          messageBox.className = 'bg-red-50 border border-red-200 rounded-lg p-4 text-red-800';
          messageBox.textContent = 'Failed to join quest. Please try again.';
          this.disabled = false;
          this.textContent = 'Join Quest';
        }
      });
    }

    // Cancel Participation
    const cancelBtn = document.getElementById('cancel-btn');
    if (cancelBtn) {
      cancelBtn.addEventListener('click', async function() {
        if (!confirm('Are you sure you want to cancel your registration?')) return;

        const questId = this.dataset.questId;
        this.disabled = true;
        this.textContent = 'Cancelling...';

        try {
          const response = await fetch(`/quests/${questId}/cancel`, {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          });

          const data = await response.json();
          const messageBox = document.getElementById('message-box');
          messageBox.style.display = 'block';
          messageBox.className = data.success 
            ? 'bg-green-50 border border-green-200 rounded-lg p-4 text-green-800' 
            : 'bg-red-50 border border-red-200 rounded-lg p-4 text-red-800';
          messageBox.textContent = data.message;

          if (data.success) {
            setTimeout(() => location.reload(), 1500);
          } else {
            this.disabled = false;
            this.textContent = 'Cancel Registration';
          }
        } catch (error) {
          const messageBox = document.getElementById('message-box');
          messageBox.style.display = 'block';
          messageBox.className = 'bg-red-50 border border-red-200 rounded-lg p-4 text-red-800';
          messageBox.textContent = 'Failed to cancel. Please try again.';
          this.disabled = false;
          this.textContent = 'Cancel Registration';
        }
      });
    }

    // Auth button
    const authBtn2 = document.getElementById('auth-btn-2');
    if (authBtn2) {
      authBtn2.addEventListener('click', () => {
        document.getElementById('auth-btn').click();
      });
    }
  </script>
</body>
</html>
