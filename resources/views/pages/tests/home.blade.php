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
  <title>Test Home</title>
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

  <h1>Test Home</h1>
  
  <div id="loading" style="display: none;">
    <p>Loading...</p>
  </div>

  <div id="error-box" style="display: none;">
    <p id="error-message"></p>
  </div>

  <hr>
  
  <h2>User Info:</h2>
  @if (auth()->user())
    <div id="user-info">
      <p>Welcome, {{ auth()->user()->name }}!</p>
      <p>Email: {{ auth()->user()->email }}</p>
      <p>Wallet: {{ auth()->user()->wallet_address ?? 'N/A' }}</p>
    </div>
  @else
    <p>Please login to see your information</p>
  @endif

  <hr>

  <h2>Top 3 Quests:</h2>
  @if($top3Quests->count() > 0)
    <ul>
      @foreach($top3Quests as $quest)
        <li>
          <strong>{{ $quest->title }}</strong><br>
          Organizer: {{ $quest->organization->name ?? 'N/A' }} @({{ $quest->organization->handle ?? 'N/A' }})<br>
          Location: {{ $quest->location_name }}<br>
          Participants: {{ $quest->quest_participants_count }}/{{ $quest->participant_limit }}<br>
          Starts in: {{ $quest->quest_start_at->diffForHumans() }}<br>
          Status: {{ $quest->status }}
        </li>
      @endforeach
    </ul>
  @else
    <p>No quests available</p>
  @endif

  <hr>

  <h2>Top 3 Organizations:</h2>
  @if($top3Orgs->count() > 0)
    <ul>
      @foreach($top3Orgs as $org)
        <li>
          <strong>{{ $org->name }}</strong> @({{ $org->handle }})<br>
          {{ $org->motto }}
        </li>
      @endforeach
    </ul>
  @else
    <p>No organizations available</p>
  @endif
  
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])
</body>
</html>