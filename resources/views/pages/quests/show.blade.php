@extends('layouts.main')

@section('title', $quest->title . ' - JagaBumi')

@section('content')
<!-- Hero Banner -->
<div class="relative h-48 sm:h-64 md:h-80">
  @if($quest->banner_url)
    <img src="{{ asset($quest->banner_url) }}" alt="{{ $quest->title }}" class="w-full h-full object-cover">
  @else
    <div class="w-full h-full bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600"></div>
  @endif
  <div class="absolute inset-0 bg-gradient-to-t from-background/50 via-background/20 to-transparent"></div>
  
  <!-- Back Button -->
  <div class="absolute top-4 left-4">
    <a href="{{ route('quests.all') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-background/80 backdrop-blur-md hover:bg-background/90 rounded-lg text-sm font-medium transition-all shadow-lg">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
      </svg>
      Balik ke Quests
    </a>
  </div>
</div>

<div class="container mx-auto px-4 -mt-12 relative z-10 pb-20">
  <div class="max-w-7xl mx-auto">
    <div class="grid lg:grid-cols-4 gap-6">
      <!-- Main Content - 3 columns -->
      <div class="lg:col-span-3 space-y-6">
        <!-- Title Card -->
        <div class="glass-card rounded-2xl shadow-xl p-6 md:p-8 border border-border/50">
          <div class="flex flex-wrap gap-2 mb-4">
            <span class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $quest->status === 'ACTIVE' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' }}">
              {{ $quest->status }}
            </span>
            <span class="px-3 py-1.5 bg-muted/80 rounded-lg text-sm font-medium border border-border">
              {{ $quest->quest_participants_count }}/{{ $quest->participant_limit }} Joined
            </span>
          </div>

          <h1 class="text-3xl md:text-4xl font-bold mb-4 bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400 bg-clip-text text-transparent leading-tight">
            {{ $quest->title }}
          </h1>

          <a href="{{ route('organizations.all') }}" class="inline-flex items-center gap-3 mb-6 group">
            @if($quest->organization->logo_img)
              <img src="{{ asset('OrganizationStorage/Logo/' . $quest->organization->logo_img) }}" alt="{{ $quest->organization->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-emerald-500/20 group-hover:ring-emerald-500/40 transition-all">
            @else
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white font-bold text-lg ring-2 ring-emerald-500/20 group-hover:ring-emerald-500/40 transition-all">
                {{ substr($quest->organization->name, 0, 1) }}
              </div>
            @endif
            <span class="font-semibold group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $quest->organization->name }}</span>
          </a>

          <div class="flex flex-wrap gap-4 text-sm text-muted-foreground mb-6">
            <span class="flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              {{ $quest->location_name }}
            </span>
          </div>

          <!-- Progress Bar -->
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="font-medium text-muted-foreground">Peserta</span>
              <span class="font-bold text-emerald-600">{{ number_format(($quest->quest_participants_count / $quest->participant_limit) * 100, 0) }}%</span>
            </div>
            <div class="w-full bg-muted/50 rounded-full h-3 overflow-hidden border border-border">
              <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-500 shadow-lg shadow-emerald-500/30" style="width: {{ ($quest->quest_participants_count / $quest->participant_limit) * 100 }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-muted-foreground">
              <span>{{ $quest->quest_participants_count }} peserta</span>
              <span>{{ $quest->participant_limit - $quest->quest_participants_count }} tempat tersisa</span>
            </div>
          </div>
        </div>

        <!-- Tabs Section -->
        <div class="glass-card rounded-2xl shadow-xl border border-border/50">
          <div class="border-b border-border bg-muted/30">
            <nav class="flex overflow-x-auto scrollbar-hide" id="tab-nav">
              <button class="tab-button active flex-shrink-0 px-6 py-4 text-sm font-semibold border-b-2 border-emerald-500 text-emerald-600 dark:text-emerald-400 transition-all hover:bg-muted/50" data-tab="overview">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span>Overview</span>
                </div>
              </button>
              <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-semibold border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50 transition-all" data-tab="prizes">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                  </svg>
                  <span>Hadiah</span>
                </div>
              </button>
              <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-semibold border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50 transition-all" data-tab="managers">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  <span>Manajer</span>
                </div>
              </button>
              <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-semibold border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50 transition-all" data-tab="submissions">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  <span>Submissions</span>
                </div>
              </button>
              @if($userParticipation)
                <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-semibold border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50 transition-all" data-tab="attendance">
                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Kehadiran</span>
                  </div>
                </button>
                <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-semibold border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:bg-muted/50 transition-all" data-tab="dashboard">
                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Dashboard</span>
                  </div>
                </button>
              @else
                <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-muted-foreground/50 cursor-not-allowed" disabled>
                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Kehadiran</span>
                  </div>
                </button>
                <button class="tab-button flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-muted-foreground/50 cursor-not-allowed" disabled>
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

          <div class="p-6 md:p-8 min-h-[400px]">
            @include('pages.quests.partials.tab-overview', ['quest' => $quest])
            @include('pages.quests.partials.tab-prizes', ['quest' => $quest])
            @include('pages.quests.partials.tab-managers', ['quest' => $quest])
            @include('pages.quests.partials.tab-submissions', ['quest' => $quest, 'submissions' => $submissions])
            @if($userParticipation)
              @include('pages.quests.partials.tab-attendance', ['quest' => $quest, 'userParticipation' => $userParticipation])
              @include('pages.quests.partials.tab-dashboard', ['quest' => $quest, 'userParticipation' => $userParticipation])
            @endif
          </div>
        </div>
      </div>

      <!-- Sidebar - 1 column (smaller) -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Join Card -->
        <div class="glass-card rounded-2xl shadow-xl p-5 border border-border/50">
          <div class="text-center mb-5">
            <h2 class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400 bg-clip-text text-transparent mb-1.5">
              Ikut Quest Ini
            </h2>
            <p class="text-xs text-muted-foreground">Jadilah bagian dari perubahan ini</p>
          </div>

          <div id="message-box" style="display: none;" class="mb-4"></div>

          @php
            $now = now();
            $regOpen = $now->gte($quest->registration_start_at);
            $regClosed = $now->gt($quest->registration_end_at);
            $isFull = $quest->quest_participants_count >= $quest->participant_limit;
            $isOrgMember = auth()->check() && \App\Models\OrganizationMember::where('organization_id', $quest->org_id)
              ->where('user_id', auth()->id())
              ->where('status', 'ACTIVE')
              ->exists();
          @endphp

          @if(auth()->check())
            @if($isOrgMember)
              <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-3.5 mb-4">
                <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 font-medium mb-1.5 text-sm">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Anggota Organisasi
                </div>
                <p class="text-xs text-foreground">Anda adalah anggota organisasi ini dan tidak dapat bergabung dalam quest ini sebagai peserta.</p>
              </div>
            @elseif($userParticipation)
              <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3.5 mb-4">
                <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium mb-1.5 text-sm">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Anda Terdaftar!
                </div>
                <p class="text-xs text-foreground mb-0.5">Status: <strong class="text-emerald-600">{{ $userParticipation->status }}</strong></p>
                <p class="text-xs text-muted-foreground">Joined: {{ $userParticipation->joined_at->format('M d, Y H:i') }}</p>
              </div>

              @if($userParticipation->status === 'REGISTERED' && !$regClosed)
                <button id="cancel-btn" data-quest-id="{{ $quest->id }}" class="w-full px-3 py-2.5 bg-destructive hover:bg-destructive/90 text-destructive-foreground font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl text-sm">
                  Batal Registrasi
                </button>
                <p class="text-[10px] text-muted-foreground mt-1.5 text-center">Anda dapat batal sebelum {{ $quest->registration_end_at->format('M d, Y H:i') }}</p>
              @endif
            @else
              @if($regOpen && !$regClosed && !$isFull)
                <button id="join-btn" data-quest-id="{{ $quest->id }}" class="w-full px-3 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl transition-all text-base shadow-lg hover:shadow-xl shadow-emerald-500/30 hover:shadow-emerald-500/50">
                  Ikut Quest
                </button>
              @elseif(!$regOpen)
                <button disabled class="w-full px-3 py-2.5 bg-muted text-muted-foreground font-semibold rounded-xl cursor-not-allowed opacity-60 text-sm">
                  Registrasi Belum Mulai
                </button>
                <p class="text-[10px] text-muted-foreground mt-1.5 text-center">Opens {{ $quest->registration_start_at->format('M d, Y') }}</p>
              @elseif($regClosed)
                <button disabled class="w-full px-3 py-2.5 bg-muted text-muted-foreground font-semibold rounded-xl cursor-not-allowed opacity-60 text-sm">
                  Registrasi Ditutup
                </button>
              @elseif($isFull)
                <button disabled class="w-full px-3 py-2.5 bg-muted text-muted-foreground font-semibold rounded-xl cursor-not-allowed opacity-60 text-sm">
                  Quest Full
                </button>
              @endif
            @endif
          @else
            <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-3.5 mb-4">
              <p class="text-xs text-foreground text-center font-medium">Tolong login untuk mengikuti quest ini</p>
            </div>
            <button id="auth-btn-2" type="button" class="w-full px-3 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl shadow-emerald-500/30 hover:shadow-emerald-500/50 text-base">
              Login / Register
            </button>
          @endif

          <div class="pt-5 mt-5 border-t border-border space-y-2.5">
            <div class="flex items-center justify-between text-xs">
              <span class="text-muted-foreground">Tempat Tersisa</span>
              <span class="font-bold text-foreground">{{ $quest->participant_limit - $quest->quest_participants_count }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
              <span class="text-muted-foreground">Limit Pemenang</span>
              <span class="font-bold text-foreground">{{ $quest->winner_limit }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
              <span class="text-muted-foreground">Tanggal Mulai Quest</span>
              <span class="font-bold text-foreground">{{ $quest->quest_start_at->format('M d, Y') }}</span>
            </div>
          </div>
        </div>

        <!-- Timeline Card -->
        <div class="glass-card rounded-2xl shadow-xl p-5 border border-border/50">
          <h3 class="text-base font-bold mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
              <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 {{ $item['completed'] ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 ring-2 ring-emerald-500/30' : 'bg-muted text-muted-foreground ring-2 ring-border' }}">
                  @if($item['completed'])
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  @endif
                </div>
                <div class="flex-1 {{ !$loop->last ? 'pb-4 border-l-2 border-border ml-3 -mt-3 pt-3 pl-2' : '' }}">
                  <p class="font-semibold text-xs {{ $item['completed'] ? 'text-foreground' : 'text-muted-foreground' }}">{{ $item['event'] }}</p>
                  <p class="text-[10px] text-muted-foreground mt-0.5">{{ $item['date']->format('M d, Y') }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Tab switching
  const tabButtons = document.querySelectorAll('.tab-button:not([disabled])');
  const tabContents = document.querySelectorAll('.tab-content');

  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      const tabId = button.dataset.tab;
      
      // Update button styles
      tabButtons.forEach(btn => {
        btn.classList.remove('active', 'border-emerald-500', 'text-emerald-600', 'dark:text-emerald-400');
        btn.classList.add('border-transparent', 'text-muted-foreground');
      });
      button.classList.add('active', 'border-emerald-500', 'text-emerald-600', 'dark:text-emerald-400');
      button.classList.remove('border-transparent', 'text-muted-foreground');

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
          ? 'bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 text-foreground' 
          : 'bg-destructive/10 border border-destructive/20 rounded-xl p-4 text-destructive';
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
        messageBox.className = 'bg-destructive/10 border border-destructive/20 rounded-xl p-4 text-destructive';
        messageBox.textContent = 'Gagal bergabung quest. Silakan coba lagi.';
        this.disabled = false;
        this.textContent = 'Ikut Quest';
      }
    });
  }

  // Cancel Participation
  const cancelBtn = document.getElementById('cancel-btn');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', async function() {
      if (!confirm('Apakah Anda yakin ingin membatalkan pendaftaran Anda?')) return;

      const questId = this.dataset.questId;
      this.disabled = true;
      this.textContent = 'Membatalkan...';

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
          ? 'bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 text-foreground' 
          : 'bg-destructive/10 border border-destructive/20 rounded-xl p-4 text-destructive';
        messageBox.textContent = data.message;

        if (data.success) {
          setTimeout(() => location.reload(), 1500);
        } else {
          this.disabled = false;
          this.textContent = 'Batal Registrasi';
        }
      } catch (error) {
        const messageBox = document.getElementById('message-box');
        messageBox.style.display = 'block';
        messageBox.className = 'bg-destructive/10 border border-destructive/20 rounded-xl p-4 text-destructive';
        messageBox.textContent = 'Pembatalan gagal. Silakan coba lagi.';
        this.disabled = false;
        this.textContent = 'Batal Registrasi';
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

  // Share Quest
  function shareQuest() {
    const url = window.location.href;
    const title = '{{ $quest->title }}';
    
    if (navigator.share) {
      navigator.share({
        title: title,
        url: url
      });
    } else {
      navigator.clipboard.writeText(url);
      alert('Link disalin ke clipboard!');
    }
  }
</script>
@endpush
