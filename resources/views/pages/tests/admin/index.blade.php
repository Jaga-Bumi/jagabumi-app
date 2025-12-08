<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin Dashboard</title>
</head>
<body>
  <nav>
    <a href="{{ route('home') }}">Home</a> |
    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a> |
    <a href="{{ route('admin.organization-requests') }}">Org Requests</a> |
    <a href="{{ route('admin.quests') }}">Quests</a>
  </nav>

  <h1>Admin Dashboard</h1>

  <hr>

  <h2>Summary</h2>
  <ul>
    <li>Pending Organization Requests: <strong>{{ $pendingOrgRequests }}</strong></li>
    <li>Quests In Review: <strong>{{ $questsInReview }}</strong></li>
    <li>Total Users: <strong>{{ $totalUsers }}</strong></li>
    <li>Total Organizations: <strong>{{ $totalOrganizations }}</strong></li>
  </ul>

  <hr>

  @if($recentOrgRequests->count() > 0)
    <h2>Recent Organization Requests</h2>
    <ul>
      @foreach($recentOrgRequests as $req)
        <li>
          <strong>{{ $req->organization_name }}</strong> - {{ $req->organization_type }}<br>
          by {{ $req->user->name }} - Status: {{ $req->status }}<br>
          {{ $req->created_at->diffForHumans() }}
        </li>
      @endforeach
    </ul>
  @endif

  <hr>

  @if($recentQuests->count() > 0)
    <h2>Recent Quests</h2>
    <ul>
      @foreach($recentQuests as $quest)
        <li>
          <strong>{{ $quest->title }}</strong><br>
          by {{ $quest->organization->name ?? 'N/A' }} - Status: {{ $quest->status }}<br>
          {{ $quest->created_at->diffForHumans() }}
        </li>
      @endforeach
    </ul>
  @endif
</body>
</html>
