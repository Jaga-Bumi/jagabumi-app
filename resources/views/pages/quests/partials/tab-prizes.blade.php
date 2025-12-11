<div id="tab-prizes" class="tab-content hidden">
  <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-2 text-emerald-800 font-medium mb-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
      </svg>
      All prizes are EQUAL for all winners!
    </div>
    <p class="text-sm text-emerald-700">Every approved submission receives the same prizes. No ranking system.</p>
  </div>

  @if($quest->prizes && $quest->prizes->count() > 0)
    <div class="grid md:grid-cols-2 gap-6">
      @foreach($quest->prizes as $prize)
        <div class="border border-border rounded-xl overflow-hidden hover:shadow-lg transition-shadow bg-card">
          @if($prize->image_url)
            <div class="aspect-video bg-muted flex items-center justify-center overflow-hidden">
              <img src="{{ $prize->image_url }}" alt="{{ $prize->name }}" class="w-full h-full object-contain">
            </div>
          @else
            <div class="aspect-video bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
              <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
              </svg>
            </div>
          @endif
          <div class="p-5">
            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold mb-2 
                         {{ $prize->type === 'CERTIFICATE' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' }}">
              {{ $prize->type }}
            </span>
            <h3 class="font-bold text-foreground text-lg mb-2">{{ $prize->name }}</h3>
            @if($prize->description)
              <p class="text-sm text-muted-foreground line-clamp-2">{{ $prize->description }}</p>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="text-center py-12">
      <svg class="w-16 h-16 mx-auto mb-4 text-muted-foreground/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
      </svg>
      <p class="text-muted-foreground">No prizes configured yet</p>
    </div>
  @endif
</div>
