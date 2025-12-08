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
  <title>Articles</title>
  <style>
    .filters { margin: 20px 0; }
    .filter-group { display: inline-block; margin-right: 15px; }
    .article-item { border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
    .author-info { display: flex; align-items: center; margin-bottom: 10px; }
    .author-avatar { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; object-fit: cover; }
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
  
  <h1>Articles</h1>
  
  <div id="loading" style="display: none;">
    <p>Loading...</p>
  </div>

  <div id="error-box" style="display: none;">
    <p id="error-message"></p>
  </div>

  <hr>

  <!-- Filters -->
  <div class="filters">
    <form method="GET" action="{{ route('articles.all') }}" id="filter-form">
      <div class="filter-group">
        <label>Search:</label>
        <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Search articles...">
      </div>
      
      <div class="filter-group">
        <label>Sort by:</label>
        <select name="sort" onchange="this.form.submit()">
          <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
          <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
          <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
          <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
        </select>
      </div>
    </form>
  </div>

  <hr>

  <!-- Articles List -->
  <div id="articles-list">
    @if($articles->count() > 0)
      <p>Found {{ $articles->total() }} article(s)</p>
      
      @foreach($articles as $article)
        <div class="article-item">
          <div class="author-info">
            @if($article->organization)
              <img src="{{ $article->organization->logo_img ? '/storage/OrganizationStorage/Logo/' . $article->organization->logo_img : 'https://via.placeholder.com/40' }}" alt="Org Logo" class="author-avatar">
              <div>
                <strong>{{ $article->organization->name }}</strong><br>
                <small>@({{ $article->organization->handle }})</small>
              </div>
            @elseif($article->user)
              <img src="{{ $article->user->avatar_url ?? 'https://via.placeholder.com/40' }}" alt="User Avatar" class="author-avatar">
              <div>
                <strong>{{ $article->user->name }}</strong><br>
                <small>@({{ $article->user->handle }})</small>
              </div>
            @else
              <img src="https://via.placeholder.com/40" alt="Avatar" class="author-avatar">
              <div>
                <strong>Anonymous</strong>
              </div>
            @endif
          </div>
          
          <h3>{{ $article->title }}</h3>
          <p><small>{{ $article->created_at->format('M d, Y') }}</small></p>
          <p>{{ Str::limit(strip_tags($article->body), 200) }}...</p>
        </div>
      @endforeach
      
      <!-- Pagination -->
      <div class="pagination">
        {{ $articles->appends(request()->query())->links() }}
      </div>
    @else
      <p>No articles found.</p>
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