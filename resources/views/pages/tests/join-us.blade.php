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
  <title>Join Us - Organization Request</title>
  <style>
    .status-box { padding: 15px; margin: 20px 0; border: 1px solid #ddd; }
    .status-pending { background-color: #fff3cd; border-color: #ffc107; }
    .status-approved { background-color: #d4edda; border-color: #28a745; }
    .status-rejected { background-color: #f8d7da; border-color: #dc3545; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input, .form-group textarea { width: 100%; max-width: 500px; padding: 8px; }
    .form-group textarea { min-height: 100px; }
    .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
    .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
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
  
  <h1>Join Us as an Organization</h1>
  
  <div id="loading" style="display: none;">
    <p>Loading...</p>
  </div>

  <div id="error-box" style="display: none;">
    <p id="error-message"></p>
  </div>

  <hr>

  @if(!auth()->user())
    <p><strong>Please login to submit an organization request.</strong></p>
    <button id="auth-btn-2" type="button">Login / Register</button>
  @else
    <!-- Success/Error Messages -->
    @if(session('success'))
      <div class="message success">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="message error">
        {{ session('error') }}
      </div>
    @endif

    @if($errors->any())
      <div class="message error">
        <ul style="margin: 0; padding-left: 20px;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Check latest request status -->
    @if($latestRequest)
      @if($latestRequest->status === 'PENDING')
        <!-- Show pending status -->
        <div class="status-box status-pending">
          <h3>Your Request is Pending</h3>
          <p><strong>Organization Name:</strong> {{ $latestRequest->organization_name }}</p>
          <p><strong>Status:</strong> <strong>PENDING</strong></p>
          <p><strong>Submitted:</strong> {{ $latestRequest->created_at->format('M d, Y H:i') }}</p>
          <p><em>Your request is currently being reviewed by our admin team. Please wait for approval.</em></p>
        </div>
      @elseif($latestRequest->status === 'APPROVED')
        <!-- Show approved status - cannot submit again -->
        <div class="status-box status-approved">
          <h3>✓ Your Request Has Been Approved!</h3>
          <p><strong>Organization Name:</strong> {{ $latestRequest->organization_name }}</p>
          <p><strong>Status:</strong> <strong>APPROVED</strong></p>
          <p><strong>Submitted:</strong> {{ $latestRequest->created_at->format('M d, Y H:i') }}</p>
          @if($latestRequest->responded_at)
            <p><strong>Approved:</strong> {{ $latestRequest->responded_at->format('M d, Y H:i') }}</p>
          @endif
          <p><em>Congratulations! Your request to create organization "{{ $latestRequest->organization_name }}" has been approved.</em></p>
          <p><em>You can now proceed to create your organization.</em></p>
        </div>
      @elseif($latestRequest->status === 'REJECTED')
        <!-- Show rejected status and allow new submission -->
        <div class="status-box status-rejected">
          <h3>Your Previous Request Was Rejected</h3>
          <p><strong>Organization Name:</strong> {{ $latestRequest->organization_name }}</p>
          <p><strong>Status:</strong> <strong>REJECTED</strong></p>
          <p><strong>Submitted:</strong> {{ $latestRequest->created_at->format('M d, Y H:i') }}</p>
          @if($latestRequest->responded_at)
            <p><strong>Rejected:</strong> {{ $latestRequest->responded_at->format('M d, Y H:i') }}</p>
          @endif
          <p><em>Unfortunately, your previous request was rejected. You can submit a new request below.</em></p>
        </div>
        <hr>
      @endif
    @endif

    <!-- Request Form - Only show if can submit -->
    @if($canSubmit)
      <h2>{{ $latestRequest && $latestRequest->status === 'REJECTED' ? 'Submit New Organization Request' : 'Submit Organization Request' }}</h2>
      <p>Fill out the form below to request creating an organization on our platform.</p>
      
      <form method="POST" action="{{ route('join-us.store') }}">
        @csrf
        
        <div class="form-group">
          <label for="organization_name">Organization Name *</label>
          <input type="text" id="organization_name" name="organization_name" value="{{ old('organization_name') }}" required maxlength="30">
        </div>

        <div class="form-group">
          <label for="organization_description">Organization Description *</label>
          <textarea id="organization_description" name="organization_description" required>{{ old('organization_description') }}</textarea>
        </div>

        <div class="form-group">
          <label for="phone_number">Phone Number *</label>
          <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required maxlength="20">
        </div>

        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
          <label for="reason">Reason for Creating Organization *</label>
          <textarea id="reason" name="reason" required>{{ old('reason') }}</textarea>
        </div>

        <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">Submit Request</button>
      </form>
    @endif
  @endif
  
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])
  
  <script>
    // Add auth button functionality for second button if needed
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