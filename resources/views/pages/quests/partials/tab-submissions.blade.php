<div id="tab-submissions" class="tab-content hidden">
  <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    All Submissions ({{ $submissions->count() }})
  </h3>

  @if($submissions && $submissions->count() > 0)
    <div class="space-y-4">
      @foreach($submissions as $submission)
        <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
          <div class="flex items-start gap-4">
            <!-- Avatar -->
            @if($submission->user && $submission->user->avatar_url)
              <img src="{{ $submission->user->avatar_url }}" alt="{{ $submission->user->name }}" class="w-12 h-12 rounded-full flex-shrink-0">
            @elseif($submission->user)
              <div class="w-12 h-12 rounded-full bg-green-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                {{ substr($submission->user->name, 0, 1) }}
              </div>
            @else
              <div class="w-12 h-12 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold flex-shrink-0">
                ?
              </div>
            @endif

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap mb-2">
                <h4 class="font-bold text-gray-900">{{ $submission->user->name ?? 'Unknown User' }}</h4>
                @if($submission->status === 'APPROVED')
                  <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Approved</span>
                @elseif($submission->status === 'REJECTED')
                  <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</span>
                @elseif($submission->status === 'COMPLETED')
                  <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending Review</span>
                @endif
              </div>

              <!-- Description Preview -->
              <p class="text-sm text-gray-600 mb-2">
                @if($submission->description && strlen($submission->description) > 150)
                  {{ substr($submission->description, 0, 150) }}...
                @else
                  {{ $submission->description ?? 'No description' }}
                @endif
              </p>

              <p class="text-xs text-gray-500 mb-3">
                Submitted: {{ $submission->submission_date ? $submission->submission_date->format('M d, Y H:i') : 'N/A' }}
              </p>

              <!-- View More Button -->
              <button 
                onclick="showSubmissionDetail({{ $submission->id }})" 
                class="inline-flex items-center gap-1 text-sm text-green-600 hover:text-green-700 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Details
              </button>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="text-center py-12">
      <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="text-gray-500">No submissions yet</p>
    </div>
  @endif
</div>

<!-- Submission Detail Modal (outside tab to prevent clipping) -->
<div id="submission-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[100] hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
    <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between">
      <h3 class="text-lg font-bold text-gray-900">Submission Details</h3>
      <button onclick="closeSubmissionModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <div id="submission-modal-content" class="p-6">
      <!-- Content will be loaded here -->
      <div class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div>
        <p class="text-gray-500 mt-4">Loading...</p>
      </div>
    </div>
  </div>
</div>

<script>
  function showSubmissionDetail(submissionId) {
    const modal = document.getElementById('submission-modal');
    const content = document.getElementById('submission-modal-content');
    
    modal.classList.remove('hidden');
    
    // Find submission data
    const submission = @json($submissions).find(s => s.id === submissionId);
    
    if (submission) {
      content.innerHTML = `
        <div class="space-y-6">
          <!-- User Info -->
          <div class="flex items-center gap-4">
            ${submission.user.avatar_url 
              ? `<img src="${submission.user.avatar_url}" alt="${submission.user.name}" class="w-16 h-16 rounded-full">`
              : `<div class="w-16 h-16 rounded-full bg-green-600 flex items-center justify-center text-white text-2xl font-bold">${submission.user.name.charAt(0)}</div>`
            }
            <div>
              <h4 class="font-bold text-gray-900 text-lg">${submission.user.name}</h4>
              <p class="text-sm text-gray-500">${submission.user.wallet_address}</p>
            </div>
          </div>

          <!-- Status -->
          <div>
            <p class="text-sm text-gray-600 mb-2">Status</p>
            ${submission.status === 'APPROVED' 
              ? '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Approved</span>'
              : submission.status === 'REJECTED'
              ? '<span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">Rejected</span>'
              : '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">Pending Review</span>'
            }
          </div>

          <!-- Submission Date -->
          <div>
            <p class="text-sm text-gray-600 mb-1">Submitted</p>
            <p class="text-gray-900">${new Date(submission.submission_date).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
          </div>

          <!-- Video -->
          ${submission.video_url ? `
            <div>
              <p class="text-sm text-gray-600 mb-2">Proof Video</p>
              <video controls class="w-full rounded-lg bg-black" controlsList="nodownload">
                <source src="${submission.video_url}" type="video/mp4">
                Your browser does not support the video tag.
              </video>
            </div>
          ` : ''}

          <!-- Description -->
          <div>
            <p class="text-sm text-gray-600 mb-2">Description</p>
            <div class="bg-gray-50 rounded-lg p-4 text-gray-900 whitespace-pre-line">
              ${submission.description || 'No description provided'}
            </div>
          </div>
        </div>
      `;
    }
  }

  function closeSubmissionModal() {
    document.getElementById('submission-modal').classList.add('hidden');
  }

  // Close modal on outside click
  document.getElementById('submission-modal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeSubmissionModal();
    }
  });
</script>
