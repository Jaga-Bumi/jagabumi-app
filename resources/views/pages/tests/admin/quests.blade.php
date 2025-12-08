<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Quests - Admin</title>
</head>
<body>
  <nav>
    <a href="{{ route('home') }}">Home</a> |
    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a> |
    <a href="{{ route('admin.organization-requests') }}">Org Requests</a> |
    <a href="{{ route('admin.quests') }}">Quests</a>
  </nav>

  <h1>Quests Management</h1>

  @if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
  @endif

  @if(session('error'))
    <p style="color: red;">{{ session('error') }}</p>
  @endif

  <hr>

  <!-- Filter -->
  <form method="GET" action="{{ route('admin.quests') }}">
    <label>Status:</label>
    <select name="status" onchange="this.form.submit()">
      <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
      <option value="IN_REVIEW" {{ request('status') === 'IN_REVIEW' ? 'selected' : '' }}>In Review</option>
      <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Approved</option>
      <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
      <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
      <option value="ENDED" {{ request('status') === 'ENDED' ? 'selected' : '' }}>Ended</option>
      <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
    </select>
    @if(request('status') && request('status') !== 'all')
      <a href="{{ route('admin.quests') }}">Clear</a>
    @endif
  </form>

  <hr>

  @if($quests->count() > 0)
    <p>Found {{ $quests->total() }} quest(s)</p>

    @foreach($quests as $quest)
      <div style="border: 1px solid #ddd; padding: 10px; margin: 10px 0;">
        <h3>{{ $quest->title }} ({{ $quest->status }})</h3>
        <p>by {{ $quest->organization->name ?? 'N/A' }}</p>
        <p><strong>Location:</strong> {{ $quest->location_name }}</p>
        <p><strong>Participants:</strong> {{ $quest->participant_limit }} max | <strong>Winners:</strong> {{ $quest->winner_limit }}</p>
        <p>{{ Str::limit($quest->desc, 150) }}</p>
        
        <details>
          <summary>Show Full Details</summary>
          <div style="margin-top: 10px;">
            <p><strong>Full Description:</strong><br>{{ $quest->desc }}</p>
            
            @if($quest->banner_url)
              <p><strong>Banner:</strong><br><img src="{{ $quest->banner_url }}" alt="Banner" style="max-width: 300px;"></p>
            @endif

            <p><strong>Timeline:</strong></p>
            <ul>
              <li>Registration: {{ $quest->registration_start_at->format('M d, Y H:i') }} - {{ $quest->registration_end_at->format('M d, Y H:i') }}</li>
              <li>Quest: {{ $quest->quest_start_at->format('M d, Y H:i') }} - {{ $quest->quest_end_at->format('M d, Y H:i') }}</li>
              <li>Judging: {{ $quest->judging_start_at->format('M d, Y H:i') }} - {{ $quest->judging_end_at->format('M d, Y H:i') }}</li>
              <li>Prize Distribution: {{ $quest->prize_distribution_date->format('M d, Y') }}</li>
            </ul>

            <p><strong>Location:</strong> {{ $quest->latitude }}, {{ $quest->longitude }} (Radius: {{ $quest->radius_meter }}m)</p>
            @if($quest->liveness_code)
              <p><strong>Liveness Code:</strong> {{ $quest->liveness_code }}</p>
            @endif

            @if($quest->prizes->count() > 0)
              <p><strong>Prizes:</strong></p>
              <ul>
                @foreach($quest->prizes as $prize)
                  <li>{{ $prize->name }} ({{ $prize->type }})</li>
                @endforeach
              </ul>
            @endif

            <p><strong>Created:</strong> {{ $quest->created_at->format('M d, Y H:i') }} ({{ $quest->created_at->diffForHumans() }})</p>
            @if($quest->approval_date)
              <p><strong>Approved:</strong> {{ $quest->approval_date->format('M d, Y H:i') }}</p>
            @endif

            <p><a href="{{ route('quests.detail', $quest->slug) }}" target="_blank">View Public Page</a></p>
          </div>
        </details>

        @if($quest->status === 'IN REVIEW')
          <hr>
          <form method="POST" action="{{ route('admin.quests.approve', $quest->id) }}" style="display: inline-block; margin-right: 10px;" onsubmit="return confirm('Approve this quest?')">
            @csrf
            <button type="submit">Approve</button>
          </form>

          <form method="POST" action="{{ route('admin.quests.reject', $quest->id) }}" style="display: inline-block;" onsubmit="return confirm('Reject this quest?')">
            @csrf
            <button type="submit">Reject</button>
          </form>
        @endif
      </div>
    @endforeach

    <hr>
    <p>{{ $quests->appends(request()->query())->links() }}</p>
  @else
    <p>No quests found.</p>
  @endif
</body>
</html>
