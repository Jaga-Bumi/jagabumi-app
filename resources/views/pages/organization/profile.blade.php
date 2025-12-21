@extends('layouts.organization')

@section('content')
@php
$title = 'Profil Organisasi';
$subtitle = "Atur profil publik organisasi Anda";
$isCreator = $currentOrg['role'] === 'CREATOR';
@endphp
  <div class="max-w-4xl mx-auto space-y-6" x-data="profileEditor()">
    
    {{-- Hidden file inputs --}}
    <input type="file" id="banner-upload" accept="image/*" class="hidden" @change="handleBannerUpload($event)">
    <input type="file" id="logo-upload" accept="image/*" class="hidden" @change="handleLogoUpload($event)">
    
    {{-- Error messages for file uploads --}}
    <div x-show="errors.banner_img || errors.logo_img" class="glass-card rounded-xl p-4 bg-destructive/10 border border-destructive/20">
      <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-destructive flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="flex-1">
          <h3 class="font-semibold text-destructive mb-1">Image Upload Error</h3>
          <p x-show="errors.banner_img" class="text-sm text-destructive mb-1" x-text="errors.banner_img"></p>
          <p x-show="errors.logo_img" class="text-sm text-destructive" x-text="errors.logo_img"></p>
        </div>
      </div>
    </div>
    
    {{-- Banner & Logo --}}
    <div class="glass-card overflow-hidden rounded-xl">
      <div class="relative h-48 bg-gradient-to-br from-primary/20 to-secondary/20">
        <img :src="bannerPreview || '{{ asset('storage/OrganizationStorage/Banner/' . $organization->banner_img) }}?v={{ time() }}'" 
             alt="{{ $organization->name }}" 
             class="w-full h-full object-cover"
             x-show="bannerPreview || {{ $organization->banner_img ? 'true' : 'false' }}">
        <div x-show="!bannerPreview && !{{ $organization->banner_img ? 'true' : 'false' }}" class="absolute inset-0 bg-hero-pattern opacity-50"></div>
        @if($isCreator)
          <button x-show="isEditing" 
                  @click="document.getElementById('banner-upload').click()"
                  type="button"
                  class="absolute top-4 right-4 px-3 py-2 rounded-lg bg-card shadow-soft text-sm font-medium flex items-center gap-2 hover-lift">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Ganti Banner
          </button>
        @endif
      </div>
      <div class="relative pb-6 px-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4 -mt-16 sm:-mt-12">
          <div class="relative">
            <div class="w-28 h-28 rounded-2xl bg-card border-4 border-background shadow-card flex items-center justify-center overflow-hidden">
              <img :src="logoPreview || '{{ asset('storage/OrganizationStorage/Logo/' . $organization->logo_img) }}?v={{ time() }}'" 
                   alt="{{ $organization->name }}" 
                   class="w-full h-full object-cover"
                   x-show="logoPreview || {{ $organization->logo_img ? 'true' : 'false' }}">
              <svg x-show="!logoPreview && !{{ $organization->logo_img ? 'true' : 'false' }}" class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            @if($isCreator)
              <button x-show="isEditing" 
                      @click="document.getElementById('logo-upload').click()"
                      type="button"
                      class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-primary text-primary-foreground shadow-soft hover-lift flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
            @endif
          </div>
          <div class="flex-1 min-w-0 mt-4">
            <div class="flex items-center gap-2 mb-1">
              <input x-show="isEditing && {{ $isCreator ? 'true' : 'false' }}" 
                     x-model="profile.name" 
                     @input="validateField('name')"
                     :class="errors.name ? 'border-destructive focus:ring-destructive' : 'border-primary focus:ring-primary'"
                     class="text-xl font-bold h-auto py-1 px-2 max-w-sm rounded-lg border-2 transition-colors">
              <h2 x-show="!isEditing" class="text-xl font-bold" x-text="profile.name"></h2>
              <span class="px-2 py-1 rounded-lg text-xs font-medium bg-primary/10 text-primary flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Verified
              </span>
            </div>
            <p x-show="isEditing && errors.name" class="text-xs text-destructive mt-1" x-text="errors.name"></p>
            <div class="flex items-center gap-4 text-sm text-muted-foreground">
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4 text-highlight fill-highlight" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                {{ $stats['rating'] }} ratings
              </span>
            </div>
          </div>
          @if($isCreator)
            <div class="flex gap-2">
              <button x-show="!isEditing" @click="isEditing = true" class="px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Profil
              </button>
              <div x-show="isEditing" class="flex gap-2">
                <button @click="isEditing = false; resetForm()" class="px-4 py-2 rounded-lg hover:bg-muted transition-colors flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Batal
                </button>
                <button @click="saveProfile()" class="px-4 py-2 rounded-lg gradient-primary text-primary-foreground shadow-glow hover-lift flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Simpan
                </button>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="glass-card p-4 rounded-xl text-center">
        <svg class="w-6 h-6 text-highlight mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
        <p class="text-2xl font-bold">{{ $stats['rating'] }}</p>
        <p class="text-xs text-muted-foreground">Rata-rata Rating</p>
      </div>
      <div class="glass-card p-4 rounded-xl text-center">
        <svg class="w-6 h-6 text-primary mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
        <p class="text-2xl font-bold">{{ $stats['total_quests'] }}</p>
        <p class="text-xs text-muted-foreground">Total Quests</p>
      </div>
      <div class="glass-card p-4 rounded-xl text-center">
        <svg class="w-6 h-6 text-secondary mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <p class="text-2xl font-bold">{{ number_format($stats['total_participants']) }}</p>
        <p class="text-xs text-muted-foreground">Peserta</p>
      </div>
      <div class="glass-card p-4 rounded-xl text-center">
        <svg class="w-6 h-6 text-accent mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
        <p class="text-2xl font-bold">{{ $stats['active_quests'] }}</p>
        <p class="text-xs text-muted-foreground">Quests Aktif</p>
      </div>
    </div>

    {{-- Organization Status (Creator Only) --}}
    @if($isCreator)
    <div class="glass-card rounded-xl p-6" x-data="{ 
        currentStatus: '{{ $organization->status }}',
        isUpdating: false,
        async updateStatus(newStatus) {
            if (newStatus === this.currentStatus) return;
            
            this.isUpdating = true;
            try {
                const response = await fetch('{{ route('organization.updateStatus', ['id' => $organization->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    this.currentStatus = newStatus;
                } else {
                    alert(data.message || 'Failed to update status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                this.isUpdating = false;
            }
        }
    }">
      <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center"
               :class="{
                 'bg-green-500/10': currentStatus === 'ACTIVE',
                 'bg-yellow-500/10': currentStatus === 'HIATUS',
                 'bg-red-500/10': currentStatus === 'INACTIVE'
               }">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 :class="{
                   'text-green-500': currentStatus === 'ACTIVE',
                   'text-yellow-500': currentStatus === 'HIATUS',
                   'text-red-500': currentStatus === 'INACTIVE'
                 }">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="font-semibold">Status Organisasi</h3>
            <p class="text-sm text-muted-foreground">Kendalikan visibilitas organisasi Anda</p>
          </div>
        </div>
        
        <div class="flex items-center gap-3">
          {{-- Status Badge --}}
          <span class="px-3 py-1.5 rounded-lg text-sm font-medium"
                :class="{
                  'bg-green-500/10 text-green-500 border border-green-500/20': currentStatus === 'ACTIVE',
                  'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20': currentStatus === 'HIATUS',
                  'bg-red-500/10 text-red-500 border border-red-500/20': currentStatus === 'INACTIVE'
                }"
                x-text="currentStatus">
          </span>
          
          {{-- Status Dropdown --}}
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" 
                    :disabled="isUpdating"
                    class="px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors flex items-center gap-2 disabled:opacity-50">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span x-show="!isUpdating">Update Status</span>
              <span x-show="isUpdating">Updating...</span>
            </button>
            
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition
                 class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-card border border-border z-50">
              <div class="py-1">
                <button @click="updateStatus('ACTIVE'); open = false"
                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-muted transition-colors flex items-center gap-3"
                        :class="currentStatus === 'ACTIVE' ? 'bg-green-500/5' : ''">
                  <span class="w-2 h-2 rounded-full bg-green-500"></span>
                  <span class="flex-1">Active</span>
                  <svg x-show="currentStatus === 'ACTIVE'" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </button>
                <button @click="updateStatus('HIATUS'); open = false"
                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-muted transition-colors flex items-center gap-3"
                        :class="currentStatus === 'HIATUS' ? 'bg-yellow-500/5' : ''">
                  <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                  <span class="flex-1">Hiatus</span>
                  <svg x-show="currentStatus === 'HIATUS'" class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </button>
                <button @click="updateStatus('INACTIVE'); open = false"
                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-muted transition-colors flex items-center gap-3"
                        :class="currentStatus === 'INACTIVE' ? 'bg-red-500/5' : ''">
                  <span class="w-2 h-2 rounded-full bg-red-500"></span>
                  <span class="flex-1">Inactive</span>
                  <svg x-show="currentStatus === 'INACTIVE'" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      {{-- Status Descriptions --}}
      <div class="mt-4 pt-4 border-t border-border">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div class="flex items-start gap-2 p-2 rounded-lg" :class="currentStatus === 'ACTIVE' ? 'bg-green-500/5' : ''">
            <span class="w-2 h-2 rounded-full bg-green-500 mt-1 flex-shrink-0"></span>
            <div>
              <span class="font-medium text-green-600 dark:text-green-400">Active</span>
              <p class="text-muted-foreground">Terlihat oleh publik, dapat membuat quest.</p>
            </div>
          </div>
          <div class="flex items-start gap-2 p-2 rounded-lg" :class="currentStatus === 'HIATUS' ? 'bg-yellow-500/5' : ''">
            <span class="w-2 h-2 rounded-full bg-yellow-500 mt-1 flex-shrink-0"></span>
            <div>
              <span class="font-medium text-yellow-600 dark:text-yellow-400">Hiatus</span>
              <p class="text-muted-foreground">Dihentikan sementara, visibilitas terbatas.</p>
            </div>
          </div>
          <div class="flex items-start gap-2 p-2 rounded-lg" :class="currentStatus === 'INACTIVE' ? 'bg-red-500/5' : ''">
            <span class="w-2 h-2 rounded-full bg-red-500 mt-1 flex-shrink-0"></span>
            <div>
              <span class="font-medium text-red-600 dark:text-red-400">Inactive</span>
              <p class="text-muted-foreground">Tersembunyi dari publik, tidak ada quest baru.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- Description --}}
    <div class="glass-card rounded-xl p-6">
      <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h2 class="text-base font-semibold">Tentang Organisasi</h2>
      </div>
      <textarea x-show="isEditing && {{ $isCreator ? 'true' : 'false' }}" 
                x-model="profile.desc" 
                @input="validateField('desc')"
                :class="errors.desc ? 'border-destructive focus:ring-destructive' : 'border-primary focus:ring-primary'"
                rows="5" 
                class="w-full px-4 py-3 rounded-lg border-2 transition-colors"></textarea>
      <p x-show="isEditing && errors.desc" class="text-xs text-destructive mt-1" x-text="errors.desc"></p>
      <p x-show="!isEditing" class="text-muted-foreground leading-relaxed" x-text="profile.desc"></p>
    </div>

    {{-- Contact Info & Social Media --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      {{-- Contact Info --}}
      <div class="glass-card rounded-xl p-6">
        <div class="flex items-center gap-2 mb-4">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <h2 class="text-base font-semibold">Contact Information</h2>
        </div>
        <div class="space-y-4">
          <div>
            <label class="text-sm font-medium text-muted-foreground">Email</label>
            <input x-show="isEditing && {{ $isCreator ? 'true' : 'false' }}" 
                   x-model="profile.org_email" 
                   @input="validateField('org_email')"
                   :class="errors.org_email ? 'border-destructive focus:ring-destructive' : 'border-primary focus:ring-primary'"
                   type="email" 
                   class="mt-1 w-full px-3 py-2 rounded-lg border-2 transition-colors">
            <p x-show="isEditing && errors.org_email" class="text-xs text-destructive mt-1" x-text="errors.org_email"></p>
            <p x-show="!isEditing" class="text-foreground flex items-center gap-2 mt-1">
              <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span x-text="profile.org_email"></span>
            </p>
          </div>
          <div>
            <label class="text-sm font-medium text-muted-foreground">Motto</label>
            <input x-show="isEditing && {{ $isCreator ? 'true' : 'false' }}" 
                   x-model="profile.motto" 
                   @input="validateField('motto')"
                   :class="errors.motto ? 'border-destructive focus:ring-destructive' : 'border-primary focus:ring-primary'"
                   class="mt-1 w-full px-3 py-2 rounded-lg border-2 transition-colors">
            <p x-show="isEditing && errors.motto" class="text-xs text-destructive mt-1" x-text="errors.motto"></p>
            <p x-show="!isEditing" class="text-foreground mt-1 italic" x-text="profile.motto"></p>
          </div>
        </div>
      </div>

      {{-- Social Media --}}
      <div class="glass-card rounded-xl p-6">
        <div class="flex items-center gap-2 mb-4">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
          </svg>
          <h2 class="text-base font-semibold">Social Media</h2>
        </div>
        <div class="space-y-4">
          @foreach([
            ['key' => 'website_url', 'label' => 'Website'],
            ['key' => 'instagram_url', 'label' => 'Instagram'],
            ['key' => 'x_url', 'label' => 'Twitter / X'],
            ['key' => 'facebook_url', 'label' => 'Facebook']
          ] as $social)
            <div>
              <label class="text-sm font-medium text-muted-foreground">{{ $social['label'] }}</label>
              <input x-show="isEditing && {{ $isCreator ? 'true' : 'false' }}" 
                     x-model="profile.{{ $social['key'] }}" 
                     @input="validateField('{{ $social['key'] }}')"
                     :class="errors.{{ $social['key'] }} ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                     type="url" 
                     class="mt-1 w-full px-3 py-2 rounded-lg border transition-colors text-sm">
              <p x-show="isEditing && errors.{{ $social['key'] }}" class="text-xs text-destructive mt-1" x-text="errors.{{ $social['key'] }}"></p>
              <p x-show="!isEditing" class="text-muted-foreground text-sm mt-1" x-text="profile.{{ $social['key'] }} || '-'"></p>
            </div>
          @endforeach
        </div>
      </div>
    </div>

  </div>

  <script>
    function profileEditor() {
      return {
        isEditing: false,
        errors: {},
        profile: {
          name: '{{ $organization->name }}',
          desc: `{{ $organization->desc }}`,
          motto: '{{ $organization->motto }}',
          org_email: '{{ $organization->org_email }}',
          website_url: '{{ $organization->website_url ?? '' }}',
          instagram_url: '{{ $organization->instagram_url ?? '' }}',
          x_url: '{{ $organization->x_url ?? '' }}',
          facebook_url: '{{ $organization->facebook_url ?? '' }}'
        },
        bannerFile: null,
        logoFile: null,
        bannerPreview: null,
        logoPreview: null,

        validateField(field) {
          // Clear previous error
          delete this.errors[field];

          // Validation rules
          const value = this.profile[field];
          
          if (field === 'name' && value && value.length > 30) {
            this.errors[field] = 'Nama Organisasi tidak boleh lebih dari 30 karakter';
          }
          
          if (field === 'org_email' && value && !this.isValidEmail(value)) {
            this.errors[field] = 'Tolong masukan email yang valid';
          }
          
          if (field === 'motto' && value && value.length > 255) {
            this.errors[field] = 'Motto tidak boleh lebih dari 255 karakter';
          }
          
          if (['website_url', 'instagram_url', 'x_url', 'facebook_url'].includes(field) && value) {
            if (value.length > 255) {
              this.errors[field] = 'URL tidak boleh lebih dari 255 karakter';
            } else if (!this.isValidUrl(value)) {
              this.errors[field] = 'Tolong masukan URL yang valid';
            }
          }
        },

        isValidEmail(email) {
          return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },

        isValidUrl(url) {
          try {
            new URL(url);
            return true;
          } catch {
            return false;
          }
        },

        hasErrors() {
          return Object.keys(this.errors).length > 0;
        },

        handleBannerUpload(event) {
          const file = event.target.files[0];
          if (file) {
            // Clear previous error
            delete this.errors.banner_img;
            
            // Validate file size (5MB = 5120KB)
            const maxSize = 5120 * 1024; // 5MB in bytes
            if (file.size > maxSize) {
              this.errors.banner_img = 'Banner tidak boleh lebih dari 5MB.';
              event.target.value = ''; // Clear the file input
              return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
              this.errors.banner_img = 'Banner harus format: jpeg, png, jpg, gif, or webp.';
              event.target.value = '';
              return;
            }
            
            this.bannerFile = file;
            const reader = new FileReader();
            reader.onload = (e) => {
              this.bannerPreview = e.target.result;
            };
            reader.readAsDataURL(file);
          }
        },

        handleLogoUpload(event) {
          const file = event.target.files[0];
          if (file) {
            // Clear previous error
            delete this.errors.logo_img;
            
            // Validate file size (2MB = 2048KB)
            const maxSize = 2048 * 1024; // 2MB in bytes
            if (file.size > maxSize) {
              this.errors.logo_img = 'Logo tidak boleh lebih dari 2MB.';
              event.target.value = ''; // Clear the file input
              return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
              this.errors.logo_img = 'Logo harus format: jpeg, png, jpg, gif, or webp.';
              event.target.value = '';
              return;
            }
            
            this.logoFile = file;
            const reader = new FileReader();
            reader.onload = (e) => {
              this.logoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
          }
        },

        resetForm() {
          this.errors = {};
          this.profile = {
            name: '{{ $organization->name }}',
            desc: `{{ $organization->desc }}`,
            motto: '{{ $organization->motto }}',
            org_email: '{{ $organization->org_email }}',
            website_url: '{{ $organization->website_url ?? '' }}',
            instagram_url: '{{ $organization->instagram_url ?? '' }}',
            x_url: '{{ $organization->x_url ?? '' }}',
            facebook_url: '{{ $organization->facebook_url ?? '' }}'
          };
          this.bannerFile = null;
          this.logoFile = null;
          this.bannerPreview = null;
          this.logoPreview = null;
          document.getElementById('banner-upload').value = '';
          document.getElementById('logo-upload').value = '';
        },

        async saveProfile() {
          // Clear previous errors
          this.errors = {};
          
          // Validate all fields before submitting
          Object.keys(this.profile).forEach(field => this.validateField(field));
          
          if (this.hasErrors()) {
            // Build error message
            const errorFields = Object.keys(this.errors);
            const errorMessages = errorFields.map(field => {
              const fieldName = field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
              return `${fieldName}: ${this.errors[field]}`;
            }).join('\n');
            
            alert(`Please fix the following errors:\n\n${errorMessages}`);
            return;
          }

          const formData = new FormData();
          
          // Add text fields (only non-empty values)
          Object.keys(this.profile).forEach(key => {
            const value = this.profile[key];
            // Only append if value exists and is not just whitespace
            if (value && value.trim && value.trim() !== '') {
              formData.append(key, value.trim());
            } else if (value && !value.trim) {
              // For non-string values
              formData.append(key, value);
            }
          });

          // Add files if uploaded
          if (this.bannerFile) {
            formData.append('banner_img', this.bannerFile);
          }
          if (this.logoFile) {
            formData.append('logo_img', this.logoFile);
          }

          try {
            const response = await fetch('{{ route("organization.update", ["id" => $organization->id]) }}', {
              method: 'POST',
              body: formData,
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              }
            });

            const result = await response.json();
            
            if (response.ok && result.success) {
              this.isEditing = false;
              this.errors = {};
              
              // Add timestamp to force reload images without cache
              const timestamp = new Date().getTime();
              window.location.href = window.location.href.split('?')[0] + '?t=' + timestamp;
            } else {
              // Handle validation errors from server
              if (result.errors) {
                this.errors = {};
                Object.entries(result.errors).forEach(([field, messages]) => {
                  this.errors[field] = messages[0]; // Take first error message
                });
                console.error('Validation errors:', result.errors);
                alert('Mohon perbaiki kesalahan validasi yang ditandai pada formulir.');
              } else {
                console.error('Update failed:', result);
                alert(result.message || 'Gagal untuk update profil');
              }
            }
          } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat update. Silakan coba lagi.');
          }
        }
      }
    }
  </script>
@endsection
