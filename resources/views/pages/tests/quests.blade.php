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
  <title>All Quests</title>
  <style>
    .filters { margin: 20px 0; }
    .filter-group { display: inline-block; margin-right: 15px; }
    .quest-item { border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
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
  
  <h1>All Quests</h1>
  
  <div id="loading" style="display: none;">
    <p>Loading...</p>
  </div>

  <div id="error-box" style="display: none;">
    <p id="error-message"></p>
  </div>

  <hr>

  <!-- Filters -->
  <div class="filters">
    <form method="GET" action="{{ route('quests.all') }}" id="filter-form">
      <div class="filter-group">
        <label>Search:</label>
        <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Search quests...">
      </div>
      
      <div class="filter-group">
        <label>Status:</label>
        <select name="status" onchange="this.form.submit()">
          <option value="">All Active</option>
          <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
          <option value="ENDED" {{ request('status') === 'ENDED' ? 'selected' : '' }}>Ended</option>
          <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Approved</option>
          <option value="IN REVIEW" {{ request('status') === 'IN REVIEW' ? 'selected' : '' }}>In Review</option>
          <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Sort by:</label>
        <select name="sort" onchange="this.form.submit()">
          <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
          <option value="ending_soon" {{ request('sort') === 'ending_soon' ? 'selected' : '' }}>Ending Soon</option>
        </select>
      </div>
    </form>
  </div>

  <hr>

  <!-- Quests List -->
  <div id="quests-list">
    @if($quests->count() > 0)
      <p>Found {{ $quests->total() }} quest(s)</p>
      
      @foreach($quests as $quest)
        <div class="quest-item">
          <h3>{{ $quest->title }}</h3>
          <p><strong>Organization:</strong> {{ $quest->organization->name ?? 'N/A' }}</p>
          <p><strong>Status:</strong> {{ $quest->status }}</p>
          <p><strong>Location:</strong> {{ $quest->location_name }}</p>
          <p><strong>Quest Period:</strong> {{ $quest->quest_start_at->format('M d, Y') }} - {{ $quest->quest_end_at->format('M d, Y') }}</p>
          <p><strong>Participants:</strong> {{ $quest->participant_limit }} max | <strong>Winners:</strong> {{ $quest->winner_limit }}</p>
          <p>{{ Str::limit($quest->desc, 150) }}</p>
        </div>
      @endforeach
      
      <!-- Pagination -->
      <div class="pagination">
        {{ $quests->appends(request()->query())->links() }}
      </div>
    @else
      <p>No quests found.</p>
    @endif
  </div>
  
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])
  
  <script>
    // Live search with debounce
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    const filterForm = document.getElementById('filter-form');
    
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        filterForm.submit();
      }, 500); // Wait 500ms after user stops typing
    });
  </script>
</body>
</html>
