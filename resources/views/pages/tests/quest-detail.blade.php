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
  <style>
    .quest-detail { max-width: 800px; margin: 20px auto; }
    .quest-banner { width: 100%; max-height: 400px; object-fit: cover; }
    .quest-info { border: 1px solid #ddd; padding: 20px; margin: 20px 0; }
    .countdown { font-size: 24px; font-weight: bold; margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff; }
    .countdown.urgent { border-left-color: #dc3545; background: #fff5f5; }
    .btn { padding: 10px 20px; border: none; cursor: pointer; font-size: 16px; margin: 5px; }
    .btn-join { background: #28a745; color: white; }
    .btn-cancel { background: #dc3545; color: white; }
    .btn-disabled { background: #6c757d; color: white; cursor: not-allowed; }
    .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
    .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .message.info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 14px; font-weight: bold; }
    .status-active { background: #28a745; color: white; }
    .status-ended { background: #6c757d; color: white; }
    .status-registered { background: #17a2b8; color: white; }
  </style>
</head>
<body>
  <nav>
    <a href="{{ route('home') }}">Home</a> |
    <a href="{{ route('quests.all') }}">Quests</a> |
    <a href="{{ route('organizations.all') }}">Organizations</a> |
    <a href="{{ route('articles.all') }}">Articles</a> |
    <a href="{{ route('leaderboard') }}">Leaderboard</a> |
    @if (auth()->user())
      <a href="{{ route('join-us') }}">Join Us</a> |
      <button id="logout-btn" type="button">Logout</button>
    @else
      <button id="auth-btn" type="button">Login / Register</button>
    @endif
  </nav>

  <div class="quest-detail">
    <h1>{{ $quest->title }}</h1>

    @if($quest->banner_url)
      <img src="{{ $quest->banner_url }}" alt="{{ $quest->title }}" class="quest-banner">
    @endif

    <div class="quest-info">
      <p><strong>Status:</strong> <span class="status-badge status-{{ strtolower($quest->status) }}">{{ $quest->status }}</span></p>
      <p><strong>Organization:</strong> {{ $quest->organization->name ?? 'N/A' }} @({{ $quest->organization->handle ?? 'N/A' }})</p>
      <p><strong>Location:</strong> {{ $quest->location_name }}</p>
      <p><strong>Participants:</strong> {{ $quest->quest_participants_count }}/{{ $quest->participant_limit }}</p>
      <p><strong>Winners Limit:</strong> {{ $quest->winner_limit }}</p>
    </div>

    <!-- Countdown Timer -->
    @php
      $now = now();
      $regOpen = $now->gte($quest->registration_start_at);
      $regClosed = $now->gt($quest->registration_end_at);
      $canCancel = !$regClosed; // Can cancel anytime during registration period
    @endphp

    @if(!$regOpen)
      <div class="countdown" id="countdown-container" data-target="{{ $quest->registration_start_at->timestamp }}" data-type="start">
        <p><strong>Registration Opens In:</strong></p>
        <div id="countdown">Calculating...</div>
      </div>
    @elseif(!$regClosed)
      <div class="countdown" id="countdown-container" data-target="{{ $quest->registration_end_at->timestamp }}" data-type="end">
        <p><strong>Registration Closes In:</strong></p>
        <div id="countdown">Calculating...</div>
      </div>
    @else
      <div class="message info">
        <strong>Registration has ended.</strong>
      </div>
    @endif

    <!-- Messages -->
    <div id="message-box" style="display: none;"></div>

    <!-- Action Buttons -->
    @if(auth()->check())
      @if($userParticipation)
        <div class="message info">
          <strong>You are registered for this quest!</strong>
          <br>Status: <span class="status-badge status-registered">{{ $userParticipation->status }}</span>
          <br>Joined at: {{ $userParticipation->joined_at->format('M d, Y H:i') }}
        </div>

        @if($userParticipation->status === 'REGISTERED' && $canCancel)
          <button id="cancel-btn" class="btn btn-cancel" data-quest-id="{{ $quest->id }}">
            Cancel Registration
          </button>
          <p style="color: #6c757d; font-size: 14px;">* You can cancel anytime before registration ends ({{ $quest->registration_end_at->format('M d, Y H:i') }})</p>
        @elseif($userParticipation->status === 'REGISTERED' && !$canCancel)
          <div class="message info">
            Registration period has ended. You cannot cancel anymore.
          </div>
        @endif
      @else
        @if($regOpen && !$regClosed)
          @if($quest->quest_participants_count < $quest->participant_limit)
            <button id="join-btn" class="btn btn-join" data-quest-id="{{ $quest->id }}">
              Join Quest
            </button>
          @else
            <button class="btn btn-disabled" disabled>
              Quest Full ({{ $quest->participant_limit }}/{{ $quest->participant_limit }})
            </button>
          @endif
        @elseif(!$regOpen)
          <button class="btn btn-disabled" disabled>
            Registration Not Started
          </button>
        @elseif($regClosed)
          <button class="btn btn-disabled" disabled>
            Registration Closed
          </button>
        @endif
      @endif
    @else
      <div class="message info">
        <strong>Please login to join this quest.</strong>
      </div>
      <button id="auth-btn-2" type="button" class="btn btn-join">Login / Register</button>
    @endif

    <hr>

    <div class="quest-info">
      <h3>Description</h3>
      <p>{{ $quest->desc }}</p>

      <h3>Quest Timeline</h3>
      <p><strong>Registration:</strong> {{ $quest->registration_start_at->format('M d, Y H:i') }} - {{ $quest->registration_end_at->format('M d, Y H:i') }}</p>
      <p><strong>Quest Period:</strong> {{ $quest->quest_start_at->format('M d, Y H:i') }} - {{ $quest->quest_end_at->format('M d, Y H:i') }}</p>
      <p><strong>Judging Period:</strong> {{ $quest->judging_start_at->format('M d, Y H:i') }} - {{ $quest->judging_end_at->format('M d, Y H:i') }}</p>
      <p><strong>Prize Distribution:</strong> {{ $quest->prize_distribution_date->format('M d, Y') }}</p>
    </div>
  </div>

  @vite(['resources/js/auth.js', 'resources/js/logout.js'])

  <script>
    // Countdown Timer
    const countdownContainer = document.getElementById('countdown-container');
    if (countdownContainer) {
      const targetTimestamp = parseInt(countdownContainer.dataset.target);
      const countdownType = countdownContainer.dataset.type;

      function updateCountdown() {
        const now = Math.floor(Date.now() / 1000);
        const diff = targetTimestamp - now;

        if (diff <= 0) {
          document.getElementById('countdown').innerHTML = countdownType === 'start' ? 'Registration is now open!' : 'Registration has ended!';
          setTimeout(() => location.reload(), 2000);
          return;
        }

        const days = Math.floor(diff / 86400);
        const hours = Math.floor((diff % 86400) / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        const seconds = diff % 60;

        let html = '';
        if (days > 0) html += `${days}d `;
        if (hours > 0 || days > 0) html += `${hours}h `;
        if (minutes > 0 || hours > 0 || days > 0) html += `${minutes}m `;
        html += `${seconds}s`;

        document.getElementById('countdown').innerHTML = html;

        // Make it urgent if less than 1 hour
        if (diff < 3600) {
          countdownContainer.classList.add('urgent');
        }
      }

      updateCountdown();
      setInterval(updateCountdown, 1000);
    }

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
          messageBox.className = 'message ' + (data.success ? 'success' : 'error');
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
          messageBox.className = 'message error';
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
          messageBox.className = 'message ' + (data.success ? 'success' : 'error');
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
          messageBox.className = 'message error';
          messageBox.textContent = 'Failed to cancel registration. Please try again.';
          this.disabled = false;
          this.textContent = 'Cancel Registration';
        }
      });
    }

    // Auth button for non-logged in users
    const authBtn2 = document.getElementById('auth-btn-2');
    if (authBtn2) {
      authBtn2.addEventListener('click', () => {
        const authBtn = document.getElementById('auth-btn');
        if (authBtn) authBtn.click();
      });
    }
  </script>
</body>
</html>