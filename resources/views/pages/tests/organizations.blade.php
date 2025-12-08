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
  <title>Organizations</title>
  <style>
    .filters { margin: 20px 0; }
    .org-item { border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
    .pagination { margin: 20px 0; }
    .pagination a, .pagination span { padding: 5px 10px; margin: 0 2px; border: 1px solid #ddd; display: inline-block; }
    .pagination .active { background: #333; color: white; }
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
  
  <h1>Organizations</h1>
  
  <div id="loading" style="display: none;">
    <p>Loading...</p>
  </div>

  <div id="error-box" style="display: none;">
    <p id="error-message"></p>
  </div>

  <hr>

  <!-- Search -->
  <div class="filters">
    <form method="GET" action="{{ route('organizations.all') }}" id="search-form">
      <label>Search:</label>
      <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Search organizations...">
    </form>
  </div>

  <hr>

  <!-- Organizations List -->
  <div id="organizations-list">
    @if($organizations->count() > 0)
      <p>Found {{ $organizations->total() }} organization(s)</p>
      
      @foreach($organizations as $org)
        <div class="org-item">
          <h3>{{ $org->name }}</h3>
          <p><strong>Handle:</strong> @({{ $org->handle }})</p>
          <p><strong>Motto:</strong> {{ $org->motto }}</p>
          <p><strong>Total Quests:</strong> {{ $org->quests_count }}</p>
          <p><strong>Total Members:</strong> {{ $org->members_count }}</p>
          <p>{{ Str::limit($org->desc, 200) }}</p>
        </div>
      @endforeach
      
      <!-- Pagination -->
      <div class="pagination">
        {{ $organizations->appends(request()->query())->links() }}
      </div>
    @else
      <p>No organizations found.</p>
    @endif
  </div>
  
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])
  
  <script>
    // Live search with debounce
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        searchForm.submit();
      }, 500); // Wait 500ms after user stops typing
    });
  </script>
</body>
</html>