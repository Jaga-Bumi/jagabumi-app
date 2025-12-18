@extends('layouts.main')

@section('title', 'Join Us - Become an Organization Creator')

@section('content')
  {{-- Hero Section --}}
  <section class="py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-secondary/5"></div>
    <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
    
    <div class="container relative z-10 mx-auto px-4">
      <div class="max-w-2xl mx-auto text-center mb-12">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary border border-primary/20 mb-4">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <span class="text-sm font-medium">Jadi Sebuah Creator</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold mb-4">
          Ciptakan Dampak dengan <span class="gradient-text">JagaBumi</span>
        </h1>
        <p class="text-muted-foreground text-lg">
          Bergabunglah dengan platform kami sebagai creator organisasi dan luncurkan misi lingkungan yang menginspirasi ribuan orang.
        </p>
        <p class="text-sm text-muted-foreground mt-4 bg-muted/50 rounded-lg px-4 py-2 inline-block">
          <strong>Note:</strong> Setiap pengguna hanya dapat menjadi Creator untuk 1 organisasi, tetapi dapat menjadi Manajer di beberapa organisasi.
        </p>
      </div>

      {{-- Benefits --}}
      <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto mb-12">
        @php
          $benefits = [
            [
              'title' => 'Buat Quests',
              'description' => 'Rancang dan luncurkan misi lingkungan Anda sendiri',
              'icon' => 'target'
            ],
            [
              'title' => 'Bangun Komunitas',
              'description' => 'Hubung dengan ribuan pejuang lingkungan',
              'icon' => 'users'
            ],
            [
              'title' => 'Lacak Dampak',
              'description' => 'Ukur dan tunjukkan dampak organisasi Anda',
              'icon' => 'chart'
            ]
          ];
        @endphp
        @foreach($benefits as $benefit)
          <div class="bg-card/50 backdrop-blur-sm border border-border rounded-xl p-6 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center mx-auto mb-4">
              @if($benefit['icon'] === 'target')
                <svg class="w-7 h-7 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
              @elseif($benefit['icon'] === 'users')
                <svg class="w-7 h-7 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              @else
                <svg class="w-7 h-7 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              @endif
            </div>
            <h3 class="font-semibold mb-2">{{ $benefit['title'] }}</h3>
            <p class="text-sm text-muted-foreground">{{ $benefit['description'] }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- Main Content --}}
  <section class="py-12 bg-muted/30">
    <div class="container mx-auto px-4 max-w-2xl">
      @guest
        {{-- Not Logged In State --}}
        <div class="bg-card border border-border rounded-xl p-8 text-center">
          <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold mb-2">Login Dibutuhkan</h2>
          <p class="text-muted-foreground mb-6">
            Silakan login untuk mengirimkan permintaan organisasi dan menjadi seorang Creator.
          </p>
          <button id="auth-btn-join" type="button" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium bg-gradient-to-r from-primary to-secondary text-white shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            Login / Register
          </button>
        </div>
      @else
        {{-- Success/Error Messages --}}
        @if(session('success'))
          <div class="bg-primary/10 border border-primary text-primary rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>{{ session('success') }}</p>
          </div>
        @endif

        @if(session('error'))
          <div class="bg-destructive/10 border border-destructive text-destructive rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>{{ session('error') }}</p>
          </div>
        @endif

        {{-- Check latest request status --}}
        @if($latestRequest)
          @if($latestRequest->status === 'PENDING')
            {{-- Pending Status --}}
            <div class="bg-card border-2 border-yellow-500/50 rounded-xl p-6 mb-6">
              <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-yellow-500/10 flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-xl font-bold mb-1">Permohonan Sedang Di Review</h3>
                  <p class="text-sm text-muted-foreground">Permintaan Anda saat ini sedang di review oleh tim admin kami.</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-700 text-xs font-medium">
                  Pending
                </span>
              </div>
              
              <div class="bg-muted/50 rounded-lg p-4 space-y-3">
                <div class="grid sm:grid-cols-2 gap-3 text-sm">
                  <div>
                    <span class="text-muted-foreground block mb-1">Nama Organisasi</span>
                    <span class="font-medium">{{ $latestRequest->organization_name }}</span>
                  </div>
                  <div>
                    <span class="text-muted-foreground block mb-1">Tipe</span>
                    <span class="font-medium">{{ $latestRequest->organization_type }}</span>
                  </div>
                  <div>
                    <span class="text-muted-foreground block mb-1">Submitted</span>
                    <span class="font-medium">{{ $latestRequest->created_at->format('M d, Y H:i') }}</span>
                  </div>
                  <div>
                    <span class="text-muted-foreground block mb-1">Status</span>
                    <span class="font-medium text-yellow-600">Under Review</span>
                  </div>
                </div>
              </div>

              <p class="text-sm text-muted-foreground mt-4 italic">
                Mohon tunggu persetujuan admin. Kami akan memberi tahu Anda melalui email setelah permohonan Anda diproses.
              </p>
            </div>

          @elseif($latestRequest->status === 'APPROVED')
            {{-- Approved Status --}}
            <div class="bg-card border-2 border-primary/50 rounded-xl p-6 mb-6">
              <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-xl font-bold mb-1">Permohonan Disetujui! 🎉</h3>
                  <p class="text-sm text-muted-foreground">Selamat! Permohonan Anda telah disetujui..</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary/20 text-primary text-xs font-medium">
                  Approved
                </span>
              </div>
              
              <div class="bg-primary/5 rounded-lg p-4 space-y-3 mb-4">
                <div class="grid sm:grid-cols-2 gap-3 text-sm">
                  <div>
                    <span class="text-muted-foreground block mb-1">Nama Organisasi</span>
                    <span class="font-medium">{{ $latestRequest->organization_name }}</span>
                  </div>
                  <div>
                    <span class="text-muted-foreground block mb-1">Tipe</span>
                    <span class="font-medium">{{ $latestRequest->organization_type }}</span>
                  </div>
                  @if($latestRequest->responded_at)
                    <div>
                      <span class="text-muted-foreground block mb-1">Disetujui pada</span>
                      <span class="font-medium">{{ $latestRequest->responded_at->format('M d, Y H:i') }}</span>
                    </div>
                  @endif
                  @if($latestRequest->approver)
                    <div>
                      <span class="text-muted-foreground block mb-1">Disetujui oleh</span>
                      <span class="font-medium">{{ $latestRequest->approver->name }}</span>
                    </div>
                  @endif
                </div>
                
                @if($latestRequest->admin_notes)
                  <div>
                    <span class="text-muted-foreground block mb-2 text-sm">Admin Message</span>
                    <div class="bg-card border-l-4 border-primary rounded p-3 text-sm">
                      {{ $latestRequest->admin_notes }}
                    </div>
                  </div>
                @endif
              </div>

              <div class="flex items-center gap-3 text-sm text-muted-foreground bg-muted/50 rounded-lg p-4">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p>Anda dapat melanjutkan untuk membuat organisasi "<strong>{{ $latestRequest->organization_name }}</strong>" dan mulailah memberikan dampak!</p>
              </div>
            </div>

          @elseif($latestRequest->status === 'REJECTED')
            {{-- Rejected Status --}}
            <div class="bg-card border-2 border-destructive/50 rounded-xl p-6 mb-6">
              <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-destructive/10 flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-xl font-bold mb-1">Permohonan Ditolak</h3>
                  <p class="text-sm text-muted-foreground">Sayangnya, permohonan Anda sebelumnya tidak disetujui.</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-destructive/20 text-destructive text-xs font-medium">
                  Rejected
                </span>
              </div>
              
              <div class="bg-destructive/5 rounded-lg p-4 space-y-3 mb-4">
                <div class="grid sm:grid-cols-2 gap-3 text-sm">
                  <div>
                    <span class="text-muted-foreground block mb-1">Nama Organisasi</span>
                    <span class="font-medium">{{ $latestRequest->organization_name }}</span>
                  </div>
                  <div>
                    <span class="text-muted-foreground block mb-1">Tipe</span>
                    <span class="font-medium">{{ $latestRequest->organization_type }}</span>
                  </div>
                  @if($latestRequest->responded_at)
                    <div>
                      <span class="text-muted-foreground block mb-1">Ditolak Pada</span>
                      <span class="font-medium">{{ $latestRequest->responded_at->format('M d, Y H:i') }}</span>
                    </div>
                  @endif
                  @if($latestRequest->approver)
                    <div>
                      <span class="text-muted-foreground block mb-1">Reviewed Oleh</span>
                      <span class="font-medium">{{ $latestRequest->approver->name }}</span>
                    </div>
                  @endif
                </div>
                
                @if($latestRequest->admin_notes)
                  <div>
                    <span class="text-muted-foreground block mb-2 text-sm">Admin Feedback</span>
                    <div class="bg-card border-l-4 border-destructive rounded p-3 text-sm">
                      {{ $latestRequest->admin_notes }}
                    </div>
                  </div>
                @endif
              </div>

              <div class="flex items-center gap-3 text-sm text-muted-foreground bg-muted/50 rounded-lg p-4">
                <svg class="w-5 h-5 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p>Anda dapat mengirimkan permohonan baru di bawah ini. Silakan tinjau feedback dan sampaikan kekhawatiran Anda.</p>
              </div>
            </div>
          @endif
        @endif

        {{-- Request Form - Only show if can submit --}}
        @if($canSubmit)
          <div class="bg-card border border-border rounded-xl p-6">
            <div class="mb-6">
              <h2 class="text-xl font-bold mb-2">
                {{ $latestRequest && $latestRequest->status === 'REJECTED' ? 'Submit New Organization Request' : 'Organization Application' }}
              </h2>
              <p class="text-sm text-muted-foreground">
                Isi formulir di bawah ini untuk mengajukan permohonan pembuatan organisasi di platform kami. Semua kolom yang ditandai dengan * wajib diisi.
              </p>
            </div>

            <form id="join-us-form" method="POST" action="{{ route('join-us.store') }}" class="space-y-6">
              @csrf
              
              {{-- Organization Name --}}
              <div>
                <label for="organization_name" class="block text-sm font-medium mb-2">
                  Nama Organisasi <span class="text-destructive">*</span>
                </label>
                <input 
                  type="text" 
                  id="organization_name" 
                  name="organization_name" 
                  value="{{ old('organization_name') }}" 
                  required 
                  maxlength="30"
                  class="w-full px-4 py-3 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary @error('organization_name') border-destructive @enderror"
                  placeholder="e.g., Green Earth Foundation"
                />
                <p class="text-xs text-muted-foreground mt-1">3-30 karakter. Huruf, angka, spasi, hyphen, dan underscore.</p>
                @error('organization_name')
                  <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- Organization Type --}}
              <div>
                <label for="organization_type" class="block text-sm font-medium mb-2">
                  Tipe Organisasi <span class="text-destructive">*</span>
                </label>
                <select 
                  id="organization_type" 
                  name="organization_type" 
                  required
                  class="w-full px-4 py-3 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary @error('organization_type') border-destructive @enderror"
                >
                  <option value="">-- Pilih Tipe --</option>
                  <option value="NGO" {{ old('organization_type') === 'NGO' ? 'selected' : '' }}>NGO / Non-Profit</option>
                  <option value="Community Group" {{ old('organization_type') === 'Community Group' ? 'selected' : '' }}>Community Group</option>
                  <option value="Educational" {{ old('organization_type') === 'Educational' ? 'selected' : '' }}>Educational Institution</option>
                  <option value="Corporate CSR" {{ old('organization_type') === 'Corporate CSR' ? 'selected' : '' }}>Corporate CSR</option>
                  <option value="Environmental" {{ old('organization_type') === 'Environmental' ? 'selected' : '' }}>Environmental Organization</option>
                  <option value="Other" {{ old('organization_type') === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('organization_type')
                  <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- Organization Description --}}
              <div>
                <label for="organization_description" class="block text-sm font-medium mb-2">
                  Deskripsi Organisasi <span class="text-destructive">*</span>
                </label>
                <textarea 
                  id="organization_description" 
                  name="organization_description" 
                  required 
                  rows="4"
                  class="w-full px-4 py-3 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary @error('organization_description') border-destructive @enderror"
                  placeholder="Deskripsikan misi dan tujuan organisasi Anda..."
                >{{ old('organization_description') }}</textarea>
                <p class="text-xs text-muted-foreground mt-1">50-1000 karakter. Deskripsikan misi dan tujuan organisasi Anda.</p>
                @error('organization_description')
                  <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- Planned Activities --}}
              <div>
                <label for="planned_activities" class="block text-sm font-medium mb-2">
                  Rencana Kegiatan <span class="text-destructive">*</span>
                </label>
                <textarea 
                  id="planned_activities" 
                  name="planned_activities" 
                  required 
                  rows="4"
                  class="w-full px-4 py-3 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary @error('planned_activities') border-destructive @enderror"
                  placeholder="Kegiatan atau quest apa yang akan Anda selenggarakan?"
                >{{ old('planned_activities') }}</textarea>
                <p class="text-xs text-muted-foreground mt-1">50-1000 karakter. Kegiatan atau quest apa yang akan Anda selenggarakan?</p>
                @error('planned_activities')
                  <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- Reason --}}
              <div>
                <label for="reason" class="block text-sm font-medium mb-2">
                  Alasan untuk Membuat Organisasi <span class="text-destructive">*</span>
                </label>
                <textarea 
                  id="reason" 
                  name="reason" 
                  required 
                  rows="3"
                  class="w-full px-4 py-3 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary @error('reason') border-destructive @enderror"
                  placeholder="Mengapa Anda ingin membuat organisasi ini di platform kami?"
                >{{ old('reason') }}</textarea>
                <p class="text-xs text-muted-foreground mt-1">50-500 karakter. Mengapa Anda ingin membuat organisasi ini di platform kami?</p>
                @error('reason')
                  <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- Optional Information --}}
              <div class="border-t border-border pt-6">
                <h3 class="font-semibold mb-4">Informasi Opsional (Membantu dalam verifikasi)</h3>
                
                <div class="grid sm:grid-cols-2 gap-4">
                  <div>
                    <label for="website_url" class="block text-sm font-medium mb-2">Website URL</label>
                    <input 
                      type="url" 
                      id="website_url" 
                      name="website_url" 
                      value="{{ old('website_url') }}" 
                      class="w-full px-4 py-3 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary @error('website_url') border-destructive @enderror"
                      placeholder="https://example.com"
                    />
                    @error('website_url')
                      <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label for="instagram_url" class="block text-sm font-medium mb-2">Instagram URL</label>
                    <input 
                      type="url" 
                      id="instagram_url" 
                      name="instagram_url" 
                      value="{{ old('instagram_url') }}" 
                      class="w-full px-4 py-3 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary @error('instagram_url') border-destructive @enderror"
                      placeholder="https://instagram.com/your_org"
                    />
                    @error('instagram_url')
                      <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label for="x_url" class="block text-sm font-medium mb-2">X (Twitter) URL</label>
                    <input 
                      type="url" 
                      id="x_url" 
                      name="x_url" 
                      value="{{ old('x_url') }}" 
                      class="w-full px-4 py-3 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary @error('x_url') border-destructive @enderror"
                      placeholder="https://x.com/your_org"
                    />
                    @error('x_url')
                      <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label for="facebook_url" class="block text-sm font-medium mb-2">Facebook URL</label>
                    <input 
                      type="url" 
                      id="facebook_url" 
                      name="facebook_url" 
                      value="{{ old('facebook_url') }}" 
                      class="w-full px-4 py-3 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary @error('facebook_url') border-destructive @enderror"
                      placeholder="https://facebook.com/your_org"
                    />
                    @error('facebook_url')
                      <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
              </div>

              {{-- Submit Button --}}
              <div class="flex items-center gap-4 pt-4">
                <button 
                  type="submit" 
                  class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-8 py-3 rounded-lg font-medium bg-gradient-to-r from-primary to-secondary text-white shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Submit Request
                </button>
                <p class="text-xs text-muted-foreground hidden sm:block">
                  Permohonan Anda akan di review dalam waktu 3-5 hari kerja.
                </p>
              </div>
            </form>
          </div>
        @endif
      @endguest
    </div>
  </section>
@endsection

@push('scripts')
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])
  <script src="{{ asset('js/form-validation.js') }}"></script>
  <script>
    // Sync join auth button with navbar auth button
    document.addEventListener('DOMContentLoaded', function() {
      const joinAuthBtn = document.getElementById('auth-btn-join');
      if (joinAuthBtn) {
        joinAuthBtn.addEventListener('click', () => {
          const authBtn = document.getElementById('auth-btn');
          if (authBtn) authBtn.click();
        });
      }

      // Initialize form validation
      const validator = new FormValidator('join-us-form', {
        organization_name: {
          required: true,
          min: 3,
          max: 30,
          messages: {
            required: 'Nama organisasi harus diisi.',
            min: 'Nama organisasi harus minimal 3 karakter.',
            max: 'Nama organisasi harus maximum 30 karakter.'
          }
        },
        organization_type: {
          required: true,
          in: ['NGO', 'Community Group', 'Educational', 'Corporate CSR', 'Environmental', 'Other'],
          messages: {
            required: 'Tipe organisasi harus diisi.',
            in: 'Tolong pilih tipe organisasi yang valid.'
          }
        },
        organization_description: {
          required: true,
          min: 50,
          max: 1000,
          messages: {
            required: 'Deskripsi organisasi harus diisi.',
            min: 'Deskripsi organisasi harus minimal 50 karakter.',
            max: 'Deskripsi organisasi harus maximal 1000 karakter.'
          }
        },
        planned_activities: {
          required: true,
          min: 50,
          max: 1000,
          messages: {
            required: 'Rencana kegiatan harus diisi.',
            min: 'Rencana kegiatan harus minimal 50 karakter.',
            max: 'Rencana kegiatan harus maximal 1000 karakter.'
          }
        },
        reason: {
          required: true,
          min: 50,
          max: 500,
          messages: {
            required: 'Alasan untuk pembuatan harus diisi.',
            min: 'Alasan harus minimal 50 karakter.',
            max: 'Alasan harus maximal 500 karakter.'
          }
        },
        website_url: {
          url: true,
          messages: {
            url: 'Tolong masukan website URL yang valid.'
          }
        },
        instagram_url: {
          url: true,
          messages: {
            url: 'Tolong masukan Instagram URL yang valid.'
          }
        },
        x_url: {
          url: true,
          messages: {
            url: 'Tolong masukan X/Twitter URL yang valid.'
          }
        },
        facebook_url: {
          url: true,
          messages: {
            url: 'Tolong masukan Facebook URL yang valid.'
          }
        }
      });

      // Add character counters for text areas
      validator.updateCharacterCount('organization_description', 1000);
      validator.updateCharacterCount('planned_activities', 1000);
      validator.updateCharacterCount('reason', 500);
    });
  </script>
@endpush
