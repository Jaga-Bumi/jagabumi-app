<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="auth-route" content="{{ route('auth.web3') }}">
  <meta name="logout-route" content="{{ route('logout') }}">
  <meta name="web3auth-client-id" content="{{ config('services.web3auth.client_id') }}">
  <meta name="web3auth-network" content="{{ config('services.web3auth.network') }}">
  <title>Leaderboard</title>
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

  <h1>Leaderboard</h1>
  
  <div id="loading" style="display: none;">
    <p>Loading...</p>
  </div>

  <div id="error-box" style="display: none;">
    <p id="error-message"></p>
  </div>
  
  <hr>

  <h2>Top Users - Most Completed Quests:</h2>
  @if($topUsers->count() > 0)
    <ol>
      @foreach($topUsers as $user)
        <li>
          <strong>{{ $user->name }}</strong> @({{ $user->handle }})<br>
          Completed Quests: {{ $user->completed_quests_count }}
        </li>
      @endforeach
    </ol>
  @else
    <p>No users with completed quests yet.</p>
  @endif

  <hr>

  <h2>Top Organizations - Most Quests Created:</h2>
  @if($topOrganizations->count() > 0)
    <ol>
      @foreach($topOrganizations as $org)
        <li>
          <strong>{{ $org->name }}</strong> @({{ $org->handle }})<br>
          Total Quests: {{ $org->quests_count }}
        </li>
      @endforeach
    </ol>
  @else
    <p>No organizations have created quests yet.</p>
  @endif

  <hr>

  <h2>Most Participated Quests:</h2>
  @if($topQuests->count() > 0)
    <ol>
      @foreach($topQuests as $quest)
        <li>
          <strong>{{ $quest->title }}</strong><br>
          Organizer: {{ $quest->organization->name ?? 'N/A' }} @({{ $quest->organization->handle ?? 'N/A' }})<br>
          Status: {{ $quest->status }}<br>
          Total Participants: {{ $quest->quest_participants_count }}
        </li>
      @endforeach
    </ol>
  @else
    <p>No quests with participants yet.</p>
  @endif
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])

</body>
</html>