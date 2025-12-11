@extends('layouts.organization')

@section('content')
@php
$title = $quest->title;
$subtitle = 'Quest Details';
$isInReview = $quest->status === 'IN REVIEW';
@endphp

<div class="max-w-7xl mx-auto space-y-6" x-data="{
  isEditing: false,
  isSaving: false,
  formErrors: {},
  bannerPreview: null,
  certImagePreview: null,
  couponImagePreview: null,
  formData: {
    title: '{{ $quest->title }}',
    desc: `{{ $quest->desc }}`,
    banner: null,
    location_name: '{{ $quest->location_name }}',
    latitude: '{{ $quest->latitude }}',
    longitude: '{{ $quest->longitude }}',
    radius_meter: {{ $quest->radius_meter }},
    liveness_code: '{{ $quest->liveness_code }}',
    registration_start_at: '{{ $quest->registration_start_at->format('Y-m-d\TH:i') }}',
    registration_end_at: '{{ $quest->registration_end_at->format('Y-m-d\TH:i') }}',
    quest_start_at: '{{ $quest->quest_start_at->format('Y-m-d\TH:i') }}',
    quest_end_at: '{{ $quest->quest_end_at->format('Y-m-d\TH:i') }}',
    judging_start_at: '{{ $quest->judging_start_at->format('Y-m-d\TH:i') }}',
    judging_end_at: '{{ $quest->judging_end_at->format('Y-m-d\TH:i') }}',
    prize_distribution_date: '{{ $quest->prize_distribution_date->format('Y-m-d\TH:i') }}',
    participant_limit: {{ $quest->participant_limit }},
    winner_limit: {{ $quest->winner_limit }},
    org_id: {{ $quest->org_id }},
    cert_name: '{{ $certificatePrize->name ?? 'Participation Certificate' }}',
    cert_image: null,
    cert_description: `{{ $certificatePrize->description ?? '' }}`,
    coupon_name: '{{ $couponPrize->name ?? '' }}',
    coupon_image: null,
    coupon_description: `{{ $couponPrize->description ?? '' }}`
  },
  
  handleBannerUpload(event) {
    const file = event.target.files[0];
    if (file) {
      this.formData.banner = file;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.bannerPreview = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  },
  
  handleCertImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
      this.formData.cert_image = file;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.certImagePreview = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  },
  
  handleCouponImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
      this.formData.coupon_image = file;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.couponImagePreview = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  },
  
  async saveQuest() {
    if (this.isSaving) return;
    
    this.isSaving = true;
    this.formErrors = {};
    
    const form = new FormData();
    form.append('_method', 'PUT');
    form.append('_token', '{{ csrf_token() }}');
    
    // Append all form data
    Object.keys(this.formData).forEach(key => {
      if (key === 'banner' || key === 'cert_image' || key === 'coupon_image') {
        if (this.formData[key]) {
          form.append(key, this.formData[key]);
        }
      } else if (this.formData[key] !== null && this.formData[key] !== '') {
        form.append(key, this.formData[key]);
      }
    });
    
    try {
      const response = await fetch('{{ route('organization.quests.update', $quest->id) }}', {
        method: 'POST',
        body: form,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (response.ok) {
        this.isEditing = false;
        window.location.reload();
      } else {
        if (data.errors) {
          this.formErrors = data.errors;
          console.error('Validation errors:', data.errors);
          
          // Show first error
          const firstError = Object.values(data.errors)[0][0];
          alert('Validation Error: ' + firstError);
        } else {
          alert(data.message || 'Failed to update quest');
        }
      }
    } catch (error) {
      console.error('Update error:', error);
      alert('An error occurred while updating the quest');
    } finally {
      this.isSaving = false;
    }
  },
  
  cancelEdit() {
    this.isEditing = false;
    this.formErrors = {};
    this.bannerPreview = null;
    this.certImagePreview = null;
    this.couponImagePreview = null;
    // Reset form data to original values
    this.formData = {
      title: '{{ $quest->title }}',
      desc: `{{ $quest->desc }}`,
      banner: null,
      location_name: '{{ $quest->location_name }}',
      latitude: '{{ $quest->latitude }}',
      longitude: '{{ $quest->longitude }}',
      radius_meter: {{ $quest->radius_meter }},
      liveness_code: '{{ $quest->liveness_code }}',
      registration_start_at: '{{ $quest->registration_start_at->format('Y-m-d\TH:i') }}',
      registration_end_at: '{{ $quest->registration_end_at->format('Y-m-d\TH:i') }}',
      quest_start_at: '{{ $quest->quest_start_at->format('Y-m-d\TH:i') }}',
      quest_end_at: '{{ $quest->quest_end_at->format('Y-m-d\TH:i') }}',
      judging_start_at: '{{ $quest->judging_start_at->format('Y-m-d\TH:i') }}',
      judging_end_at: '{{ $quest->judging_end_at->format('Y-m-d\TH:i') }}',
      prize_distribution_date: '{{ $quest->prize_distribution_date->format('Y-m-d\TH:i') }}',
      participant_limit: {{ $quest->participant_limit }},
      winner_limit: {{ $quest->winner_limit }},
      org_id: {{ $quest->org_id }},
      cert_name: '{{ $certificatePrize->name ?? 'Participation Certificate' }}',
      cert_image: null,
      cert_description: `{{ $certificatePrize->description ?? '' }}`,
      coupon_name: '{{ $couponPrize->name ?? '' }}',
      coupon_image: null,
      coupon_description: `{{ $couponPrize->description ?? '' }}`
    };
    document.getElementById('banner-upload').value = '';
    document.getElementById('cert-image-upload').value = '';
    document.getElementById('coupon-image-upload').value = '';
  }
}">
  
  {{-- Hidden file inputs --}}
  <input type="file" id="banner-upload" accept="image/*" class="hidden" @change="handleBannerUpload($event)">
  <input type="file" id="cert-image-upload" accept="image/*" class="hidden" @change="handleCertImageUpload($event)">
  <input type="file" id="coupon-image-upload" accept="image/*" class="hidden" @change="handleCouponImageUpload($event)">
  
  {{-- Back Button --}}
  <div>
    <a href="{{ route('organization.quests.index') }}" class="inline-flex items-center gap-2 text-muted-foreground hover:text-foreground transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
      Back to Quests
    </a>
  </div>

  {{-- Hero Banner --}}
  <div class="relative h-64 md:h-80 lg:h-96 rounded-2xl overflow-hidden">
    <img :src="bannerPreview || '{{ $quest->banner_url }}'"
         alt="{{ $quest->title }}" 
         class="w-full h-full object-cover"
         x-show="bannerPreview || {{ $quest->banner_url ? 'true' : 'false' }}">
    <div x-show="!bannerPreview && !{{ $quest->banner_url ? 'true' : 'false' }}" 
         class="w-full h-full bg-gradient-to-r from-primary/20 to-secondary/20"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent"></div>
    
    {{-- Upload Button (Edit Mode) --}}
    <template x-if="isEditing && {{ $isInReview ? 'true' : 'false' }}">
      <button @click="document.getElementById('banner-upload').click()"
              type="button"
              class="absolute top-4 right-4 px-4 py-2 rounded-lg bg-white/90 backdrop-blur-sm hover:bg-white text-sm font-medium shadow-lg transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span x-text="formData.banner ? 'Banner Selected' : 'Change Banner'"></span>
      </button>
    </template>
  </div>

  {{-- Action Buttons --}}
  @if($isInReview)
    <div class="flex justify-end gap-2">
      <template x-if="!isEditing">
        <button @click="isEditing = true" class="px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Edit Quest
        </button>
      </template>
      
      <template x-if="isEditing">
        <div class="flex gap-2">
          <button @click="cancelEdit" :disabled="isSaving" class="px-4 py-2 rounded-lg hover:bg-muted transition-colors flex items-center gap-2 disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancel
          </button>
          <button @click="saveQuest" :disabled="isSaving" class="px-4 py-2 rounded-lg gradient-primary text-primary-foreground shadow-glow hover-lift flex items-center gap-2 disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span x-text="isSaving ? 'Saving...' : 'Save Changes'"></span>
          </button>
        </div>
      </template>
    </div>
  @endif

  {{-- Main Content Grid --}}
  <div class="grid lg:grid-cols-3 gap-8">
    {{-- Main Content (2/3) --}}
    <div class="lg:col-span-2 space-y-6">
      {{-- Title & Description --}}
      <div class="glass-card rounded-xl p-6">
        <template x-if="!isEditing">
          <div>
            <div class="flex flex-wrap gap-2 mb-4">
              <span class="px-3 py-1 rounded-full text-sm font-medium {{ $quest->status === 'IN REVIEW' ? 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border border-yellow-500/20' : 'bg-green-500/10 text-green-600 dark:text-green-400 border border-green-500/20' }}">
                {{ $quest->status }}
              </span>
              <span class="px-3 py-1 bg-muted rounded-full text-sm font-medium">
                {{ $quest->quest_participants_count }}/{{ $quest->participant_limit }} Participants
              </span>
            </div>
            <h1 class="text-3xl font-bold mb-4">{{ $quest->title }}</h1>
            <p class="text-muted-foreground leading-relaxed whitespace-pre-line">{{ $quest->desc }}</p>
          </div>
        </template>
        
        <template x-if="isEditing">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-2">Quest Title</label>
              <input 
                type="text" 
                x-model="formData.title"
                class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
                :class="{ 'border-destructive': formErrors.title }"
              >
              <template x-if="formErrors.title">
                <p class="text-sm text-destructive mt-1" x-text="formErrors.title[0]"></p>
              </template>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">Description</label>
              <textarea 
                x-model="formData.desc"
                rows="6"
                class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
                :class="{ 'border-destructive': formErrors.desc }"
              ></textarea>
              <template x-if="formErrors.desc">
                <p class="text-sm text-destructive mt-1" x-text="formErrors.desc[0]"></p>
              </template>
            </div>
          </div>
        </template>
      </div>

      {{-- Location & Geofencing --}}
      <div class="glass-card rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          Location & Geofencing
        </h2>
        
        <template x-if="!isEditing">
          <div class="space-y-3">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-sm text-muted-foreground">Location</p>
                <p class="font-medium">{{ $quest->location_name }}</p>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-muted-foreground">Coordinates</p>
                  <p class="font-medium text-sm">{{ $quest->latitude }}, {{ $quest->longitude }}</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-muted-foreground">Radius</p>
                  <p class="font-medium">{{ $quest->radius_meter }}m</p>
                </div>
              </div>
            </div>
          </div>
        </template>
        
        <template x-if="isEditing">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-2">Location Name</label>
              <input 
                type="text" 
                x-model="formData.location_name"
                class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
              >
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2">Latitude</label>
                <input 
                  type="number" 
                  step="any"
                  x-model="formData.latitude"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
                >
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Longitude</label>
                <input 
                  type="number" 
                  step="any"
                  x-model="formData.longitude"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
                >
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">Radius (meters)</label>
              <input 
                type="number" 
                x-model="formData.radius_meter"
                class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
              >
            </div>
          </div>
        </template>
      </div>

      {{-- Timeline --}}
      <div class="glass-card rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Timeline
        </h2>
        
        <template x-if="!isEditing">
          <div class="space-y-3">
            @php
              $timelineItems = [
                ['icon' => 'calendar', 'label' => 'Registration Opens', 'date' => $quest->registration_start_at],
                ['icon' => 'calendar', 'label' => 'Registration Closes', 'date' => $quest->registration_end_at],
                ['icon' => 'play', 'label' => 'Quest Starts', 'date' => $quest->quest_start_at],
                ['icon' => 'stop', 'label' => 'Quest Ends', 'date' => $quest->quest_end_at],
                ['icon' => 'eye', 'label' => 'Judging Period', 'date' => $quest->judging_start_at, 'endDate' => $quest->judging_end_at],
                ['icon' => 'gift', 'label' => 'Prize Distribution', 'date' => $quest->prize_distribution_date],
              ];
            @endphp
            
            @foreach($timelineItems as $item)
              <div class="flex items-center gap-3 p-3 rounded-lg bg-muted/30">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                  @if($item['icon'] === 'calendar')
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  @elseif($item['icon'] === 'play')
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  @elseif($item['icon'] === 'stop')
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                    </svg>
                  @elseif($item['icon'] === 'eye')
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  @else
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                  @endif
                </div>
                <div class="flex-1">
                  <p class="text-sm text-muted-foreground">{{ $item['label'] }}</p>
                  <p class="font-medium">
                    {{ $item['date']->format('M d, Y H:i') }}
                    @if(isset($item['endDate']))
                      - {{ $item['endDate']->format('M d, Y H:i') }}
                    @endif
                  </p>
                </div>
              </div>
            @endforeach
          </div>
        </template>
        
        <template x-if="isEditing">
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2">Registration Start</label>
                <input 
                  type="datetime-local" 
                  x-model="formData.registration_start_at"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                >
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Registration End</label>
                <input 
                  type="datetime-local" 
                  x-model="formData.registration_end_at"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                >
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2">Quest Start</label>
                <input 
                  type="datetime-local" 
                  x-model="formData.quest_start_at"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                >
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Quest End</label>
                <input 
                  type="datetime-local" 
                  x-model="formData.quest_end_at"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                >
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-2">Judging Start</label>
                <input 
                  type="datetime-local" 
                  x-model="formData.judging_start_at"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                >
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Judging End</label>
                <input 
                  type="datetime-local" 
                  x-model="formData.judging_end_at"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                >
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">Prize Distribution Date</label>
              <input 
                type="datetime-local" 
                x-model="formData.prize_distribution_date"
                class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm"
              >
            </div>
          </div>
        </template>
      </div>

      {{-- Prizes --}}
      <div class="glass-card rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
          </svg>
          Prizes & Rewards
        </h2>
        
        <template x-if="!isEditing">
          <div class="space-y-4">
            @foreach($quest->prizes as $prize)
              <div class="flex gap-4 p-4 rounded-lg bg-muted/30">
                @if($prize->image_url)
                  <img src="{{ $prize->image_url }}" alt="{{ $prize->name }}" class="w-20 h-20 rounded-lg object-cover">
                @else
                  <div class="w-20 h-20 rounded-lg bg-primary/10 flex items-center justify-center">
                    <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                  </div>
                @endif
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-semibold">{{ $prize->name }}</h3>
                    <span class="px-2 py-0.5 text-xs rounded-full bg-primary/10 text-primary">{{ $prize->type }}</span>
                  </div>
                  <p class="text-sm text-muted-foreground">{{ $prize->description }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </template>
        
        <template x-if="isEditing">
          <div class="space-y-6">
            {{-- Certificate Prize --}}
            <div class="space-y-3">
              <h3 class="font-medium text-sm">Certificate Prize (Required)</h3>
              
              {{-- Certificate Image Preview --}}
              <div class="relative w-full h-32 rounded-lg overflow-hidden bg-muted/30">
                <img :src="certImagePreview || '{{ $certificatePrize->image_url ?? '' }}'"
                     alt="Certificate" 
                     class="w-full h-full object-cover"
                     x-show="certImagePreview || {{ $certificatePrize && $certificatePrize->image_url ? 'true' : 'false' }}">
                <div x-show="!certImagePreview && !{{ $certificatePrize && $certificatePrize->image_url ? 'true' : 'false' }}"
                     class="w-full h-full flex items-center justify-center">
                  <svg class="w-12 h-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <button @click="document.getElementById('cert-image-upload').click()"
                        type="button"
                        class="absolute bottom-2 right-2 px-3 py-1.5 rounded-lg bg-white/90 backdrop-blur-sm hover:bg-white text-xs font-medium shadow-lg transition-all flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span x-text="formData.cert_image ? 'Image Selected' : 'Change Image'"></span>
                </button>
              </div>
              
              <div>
                <label class="block text-sm font-medium mb-2">Name</label>
                <input 
                  type="text" 
                  x-model="formData.cert_name"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
                >
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Description</label>
                <textarea 
                  x-model="formData.cert_description"
                  rows="2"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
                ></textarea>
              </div>
            </div>
            
            {{-- Coupon Prize --}}
            <div class="space-y-3 pt-4 border-t border-border">
              <h3 class="font-medium text-sm">Coupon Prize (Optional)</h3>
              
              {{-- Coupon Image Preview --}}
              <div class="relative w-full h-32 rounded-lg overflow-hidden bg-muted/30">
                <img :src="couponImagePreview || '{{ $couponPrize->image_url ?? '' }}'"
                     alt="Coupon" 
                     class="w-full h-full object-cover"
                     x-show="couponImagePreview || {{ $couponPrize && $couponPrize->image_url ? 'true' : 'false' }}">
                <div x-show="!couponImagePreview && !{{ $couponPrize && $couponPrize->image_url ? 'true' : 'false' }}"
                     class="w-full h-full flex items-center justify-center">
                  <svg class="w-12 h-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <button @click="document.getElementById('coupon-image-upload').click()"
                        type="button"
                        class="absolute bottom-2 right-2 px-3 py-1.5 rounded-lg bg-white/90 backdrop-blur-sm hover:bg-white text-xs font-medium shadow-lg transition-all flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span x-text="formData.coupon_image ? 'Image Selected' : 'Change Image'"></span>
                </button>
              </div>
              
              <div>
                <label class="block text-sm font-medium mb-2">Name</label>
                <input 
                  type="text" 
                  x-model="formData.coupon_name"
                  placeholder="Leave empty if no coupon"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
                >
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Description</label>
                <textarea 
                  x-model="formData.coupon_description"
                  rows="2"
                  placeholder="Leave empty if no coupon"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
                ></textarea>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>

    {{-- Sidebar (1/3) --}}
    <div class="space-y-6">
      {{-- Quick Stats --}}
      <div class="glass-card rounded-xl p-6">
        <h3 class="font-semibold mb-4">Quick Stats</h3>
        <div class="space-y-3">
          <template x-if="!isEditing">
            <div>
              <div class="flex items-center justify-between text-sm mb-1">
                <span class="text-muted-foreground">Participant Limit</span>
                <span class="font-semibold">{{ $quest->participant_limit }}</span>
              </div>
              <div class="flex items-center justify-between text-sm mb-1">
                <span class="text-muted-foreground">Winner Limit</span>
                <span class="font-semibold">{{ $quest->winner_limit }}</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-muted-foreground">Current Participants</span>
                <span class="font-semibold text-primary">{{ $quest->quest_participants_count }}</span>
              </div>
            </div>
          </template>
          
          <template x-if="isEditing">
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium mb-2">Participant Limit</label>
                <input 
                  type="number" 
                  x-model="formData.participant_limit"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
                >
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Winner Limit</label>
                <input 
                  type="number" 
                  x-model="formData.winner_limit"
                  class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
                >
              </div>
            </div>
          </template>
        </div>
      </div>

      {{-- Organization Info --}}
      <div class="glass-card rounded-xl p-6">
        <h3 class="font-semibold mb-4">Organization</h3>
        <div class="flex items-center gap-3">
          @if($quest->organization->logo_img)
            <img src="{{ asset('OrganizationStorage/Logo/' . $quest->organization->logo_img) }}" alt="{{ $quest->organization->name }}" class="w-12 h-12 rounded-full object-cover">
          @else
            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
              {{ substr($quest->organization->name, 0, 1) }}
            </div>
          @endif
          <div class="flex-1 min-w-0">
            <p class="font-medium truncate">{{ $quest->organization->name }}</p>
            <p class="text-sm text-muted-foreground truncate">@if($quest->organization->handle){{ '@' . $quest->organization->handle }}@endif</p>
          </div>
        </div>
      </div>

      {{-- Additional Settings --}}
      <template x-if="isEditing">
        <div class="glass-card rounded-xl p-6">
          <h3 class="font-semibold mb-4">Additional Settings</h3>
          <div>
            <label class="block text-sm font-medium mb-2">Liveness Code</label>
            <input 
              type="text" 
              x-model="formData.liveness_code"
              placeholder="Optional verification code"
              class="w-full px-4 py-2 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary"
            >
            <p class="text-xs text-muted-foreground mt-1">Used for on-site verification</p>
          </div>
        </div>
      </template>

      {{-- Status Info --}}
      @if($quest->status === 'IN REVIEW')
        <div class="glass-card rounded-xl p-6 bg-yellow-500/5 border border-yellow-500/20">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
              <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400 mb-1">Pending Review</p>
              <p class="text-xs text-yellow-600/80 dark:text-yellow-400/80">This quest is waiting for admin approval. You can edit it until it's approved.</p>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
