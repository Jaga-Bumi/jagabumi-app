<div id="tab-prizes" class="tab-content hidden">
  <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
    <div class="flex items-center gap-2 text-green-800 font-medium mb-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
      </svg>
      All prizes are EQUAL for all winners!
    </div>
    <p class="text-sm text-green-700">Every approved submission receives the same prizes. No ranking system.</p>
  </div>

  @if($quest->prizes && $quest->prizes->count() > 0)
    <div class="grid md:grid-cols-2 gap-6">
      @foreach($quest->prizes as $prize)
        <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
          @if($prize->image_url)
            <div class="aspect-square bg-gray-100">
              <img src="{{ $prize->image_url }}" alt="{{ $prize->name }}" class="w-full h-full object-cover">
            </div>
          @else
            <div class="aspect-square bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
              <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
              </svg>
            </div>
          @endif
          <div class="p-4">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium mb-2 
                         {{ $prize->type === 'CERTIFICATE' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
              {{ $prize->type }}
            </span>
            <h3 class="font-bold text-gray-900 mb-2">{{ $prize->name }}</h3>
            @if($prize->description)
              <p class="text-sm text-gray-600">{{ $prize->description }}</p>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="text-center py-12">
      <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
      </svg>
      <p class="text-gray-500">No prizes configured yet</p>
    </div>
  @endif
</div>
