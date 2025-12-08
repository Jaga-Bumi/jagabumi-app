<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Organization Requests - Admin</title>
</head>
<body>
  <nav>
    <a href="{{ route('home') }}">Home</a> |
    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a> |
    <a href="{{ route('admin.organization-requests') }}">Org Requests</a> |
    <a href="{{ route('admin.quests') }}">Quests</a>
  </nav>

  <h1>Organization Requests</h1>

  @if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
  @endif

  @if(session('error'))
    <p style="color: red;">{{ session('error') }}</p>
  @endif

  <hr>

  <!-- Filter -->
  <form method="GET" action="{{ route('admin.organization-requests') }}">
    <label>Status:</label>
    <select name="status" onchange="this.form.submit()">
      <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
      <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
      <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Approved</option>
      <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
    </select>
    @if(request('status') && request('status') !== 'all')
      <a href="{{ route('admin.organization-requests') }}">Clear</a>
    @endif
  </form>

  <hr>

  @if($requests->count() > 0)
    <p>Found {{ $requests->total() }} request(s)</p>

    @foreach($requests as $req)
      <div style="border: 1px solid #ddd; padding: 10px; margin: 10px 0;">
        <h3>{{ $req->organization_name }} ({{ $req->status }})</h3>
        <p>by {{ $req->user->name }} (@{{ $req->user->handle ?? 'N/A' }})</p>
        <p><strong>Type:</strong> {{ $req->organization_type }}</p>
        <p><strong>Description:</strong> {{ Str::limit($req->organization_description, 150) }}</p>
        
        <details>
          <summary>Show Full Details</summary>
          <div style="margin-top: 10px;">
            <p><strong>Full Description:</strong><br>{{ $req->organization_description }}</p>
            <p><strong>Reason:</strong><br>{{ $req->reason }}</p>
            <p><strong>Planned Activities:</strong><br>{{ $req->planned_activities }}</p>
            
            <p><strong>Social Links:</strong></p>
            <ul>
              @if($req->website_url)<li><a href="{{ $req->website_url }}" target="_blank">Website</a></li>@endif
              @if($req->instagram_url)<li><a href="{{ $req->instagram_url }}" target="_blank">Instagram</a></li>@endif
              @if($req->x_url)<li><a href="{{ $req->x_url }}" target="_blank">X/Twitter</a></li>@endif
              @if($req->facebook_url)<li><a href="{{ $req->facebook_url }}" target="_blank">Facebook</a></li>@endif
              @if(!$req->website_url && !$req->instagram_url && !$req->x_url && !$req->facebook_url)
                <li>No social links</li>
              @endif
            </ul>

            <p><strong>User Email:</strong> {{ $req->user->email }}</p>
            <p><strong>User Wallet:</strong> {{ $req->user->wallet_address ?? 'N/A' }}</p>
            <p><strong>Submitted:</strong> {{ $req->created_at->format('M d, Y H:i') }} ({{ $req->created_at->diffForHumans() }})</p>
            
            @if($req->admin_notes)
              <p><strong>Admin Notes:</strong><br>{{ $req->admin_notes }}</p>
            @endif
            
            @if($req->responded_at)
              <p><strong>Responded:</strong> {{ $req->responded_at->format('M d, Y H:i') }}</p>
            @endif
          </div>
        </details>

        @if($req->status === 'PENDING')
          <hr>
          
          <form method="POST" action="{{ route('admin.organization-requests.approve', $req->id) }}" style="display: inline-block; margin-right: 10px;" onsubmit="return confirm('Approve this request?')">
            @csrf
            <label>Admin Notes:</label><br>
            <textarea name="admin_notes" rows="2" style="width: 300px;"></textarea><br>
            <button type="submit">Approve</button>
          </form>

          <form method="POST" action="{{ route('admin.organization-requests.reject', $req->id) }}" style="display: inline-block;" onsubmit="return confirm('Reject this request?')">
            @csrf
            <label>Rejection Reason:</label><br>
            <textarea name="admin_notes" rows="2" style="width: 300px;"></textarea><br>
            <button type="submit">Reject</button>
          </form>
        @endif
      </div>
    @endforeach

    <hr>
    <p>{{ $requests->appends(request()->query())->links() }}</p>
  @else
    <p>No organization requests found.</p>
  @endif
</body>
</html>
