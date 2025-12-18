@extends('layouts.organization')

@section('content')
@php
$title = 'Create Quest';
$subtitle = 'Create a new environmental quest for your organization';
@endphp
  {{-- Back Button --}}
  <div class="mb-4">
    <a href="{{ route('organization.quests.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Balik ke Quest Management
    </a>
  </div>
  <div class="max-w-4xl mx-auto space-y-6" x-data="{
    currentStep: 1,
    totalSteps: 4,
    bannerPreview: null,
    certImagePreview: null,
    couponImagePreview: null,
    hasCoupon: false,
    errors: {},
    formData: {
      title: '',
      desc: '',
      banner: null,
      location_name: '',
      latitude: '',
      longitude: '',
      radius_meter: 100,
      liveness_code: '',
      registration_start_at: '',
      registration_end_at: '',
      quest_start_at: '',
      quest_end_at: '',
      judging_start_at: '',
      judging_end_at: '',
      prize_distribution_date: '',
      participant_limit: 50,
      winner_limit: 5,
      cert_name: 'Participation Certificate',
      cert_image: null,
      cert_description: '',
      coupon_name: '',
      coupon_image: null,
      coupon_description: ''
    },
    
    validateField(field) {
      // Clear previous error for this field
      delete this.errors[field];
      
      // Step 1 validations
      if (field === 'title') {
        if (!this.formData.title || this.formData.title.trim().length === 0) {
          this.errors.title = 'Judul Quest harus diisi';
        } else if (this.formData.title.length > 255) {
          this.errors.title = 'Judul Quest tidak boleh lebih dari 255 karakter';
        }
      } else if (field === 'desc') {
        if (!this.formData.desc || this.formData.desc.trim().length === 0) {
          this.errors.desc = 'Deskripsi harus diisi';
        }
      } else if (field === 'banner') {
        if (!this.formData.banner) {
          this.errors.banner = 'Gambar banner Quest harus diisi';
        }
      }
      // Step 2 validations
      else if (field === 'location_name') {
        if (!this.formData.location_name || this.formData.location_name.trim().length === 0) {
          this.errors.location_name = 'Nama lokasi harus diisi';
        }
      } else if (field === 'latitude') {
        if (!this.formData.latitude || isNaN(this.formData.latitude)) {
          this.errors.latitude = 'Latitude yang valid dibutuhkan';
        } else if (this.formData.latitude < -90 || this.formData.latitude > 90) {
          this.errors.latitude = 'Latitude harus di antara -90 dan 90';
        }
      } else if (field === 'longitude') {
        if (!this.formData.longitude || isNaN(this.formData.longitude)) {
          this.errors.longitude = 'Longitude yang valid dibutuhkan';
        } else if (this.formData.longitude < -180 || this.formData.longitude > 180) {
          this.errors.longitude = 'Longitude harus di antara -180 dan 180';
        }
      } else if (field === 'radius_meter') {
        if (this.formData.radius_meter && (isNaN(this.formData.radius_meter) || this.formData.radius_meter < 10)) {
          this.errors.radius_meter = 'Radius harus minimal 10 meter';
        }
      }
      // Step 3 validations
      else if (field === 'registration_start_at') {
        if (!this.formData.registration_start_at) {
          this.errors.registration_start_at = 'Tanggal mulai registrasi harus diisi';
        }
      } else if (field === 'registration_end_at') {
        if (!this.formData.registration_end_at) {
          this.errors.registration_end_at = 'Tanggal akhir registrasi harus diisi';
        }
      } else if (field === 'quest_start_at') {
        if (!this.formData.quest_start_at) {
          this.errors.quest_start_at = 'Tanggal mulai quest harus diisi';
        }
      } else if (field === 'quest_end_at') {
        if (!this.formData.quest_end_at) {
          this.errors.quest_end_at = 'Tanggal akhir quest harus diisi';
        }
      } else if (field === 'judging_start_at') {
        if (!this.formData.judging_start_at) {
          this.errors.judging_start_at = 'Tanggal mulai penjurian harus diisi';
        }
      } else if (field === 'judging_end_at') {
        if (!this.formData.judging_end_at) {
          this.errors.judging_end_at = 'Tanggal akhir penjurian harus diisi';
        }
      } else if (field === 'prize_distribution_date') {
        if (!this.formData.prize_distribution_date) {
          this.errors.prize_distribution_date = 'Tanggal pembagian hadiah harus diisi';
        }
      } else if (field === 'participant_limit') {
        if (!this.formData.participant_limit || isNaN(this.formData.participant_limit) || this.formData.participant_limit < 1) {
          this.errors.participant_limit = 'Limit jumlah peserta harus minimal 1';
        }
      } else if (field === 'winner_limit') {
        if (!this.formData.winner_limit || isNaN(this.formData.winner_limit) || this.formData.winner_limit < 1) {
          this.errors.winner_limit = 'Limit jumlah pemenang harus minimal 1';
        } else if (parseInt(this.formData.winner_limit) > parseInt(this.formData.participant_limit)) {
          this.errors.winner_limit = 'Limit jumlah pemenang tidak boleh lebih dari jumlah peserta';
        }
      }
      // Step 4 validations
      else if (field === 'cert_name') {
        if (!this.formData.cert_name || this.formData.cert_name.trim().length === 0) {
          this.errors.cert_name = 'Nama sertifikasi harus diisi';
        }
      } else if (field === 'cert_image') {
        if (!this.formData.cert_image) {
          this.errors.cert_image = 'Gambar sertifikasi harus diisi';
        }
      } else if (field === 'cert_description') {
        if (!this.formData.cert_description || this.formData.cert_description.trim().length === 0) {
          this.errors.cert_description = 'Deskripsi sertifikasi harus diisi';
        }
      }
    },
    
    validateStep(step) {
      let isValid = true;
      
      if (step === 1) {
        this.validateField('title');
        this.validateField('desc');
        this.validateField('banner');
        isValid = !this.errors.title && !this.errors.desc && !this.errors.banner;
      } else if (step === 2) {
        this.validateField('location_name');
        this.validateField('latitude');
        this.validateField('longitude');
        this.validateField('radius_meter');
        isValid = !this.errors.location_name && !this.errors.latitude && !this.errors.longitude && !this.errors.radius_meter;
      } else if (step === 3) {
        this.validateField('registration_start_at');
        this.validateField('registration_end_at');
        this.validateField('quest_start_at');
        this.validateField('quest_end_at');
        this.validateField('judging_start_at');
        this.validateField('judging_end_at');
        this.validateField('prize_distribution_date');
        this.validateField('participant_limit');
        this.validateField('winner_limit');
        isValid = !this.errors.registration_start_at && !this.errors.registration_end_at && 
                  !this.errors.quest_start_at && !this.errors.quest_end_at && 
                  !this.errors.judging_start_at && !this.errors.judging_end_at && 
                  !this.errors.prize_distribution_date && !this.errors.participant_limit && 
                  !this.errors.winner_limit;
      } else if (step === 4) {
        this.validateField('cert_name');
        this.validateField('cert_image');
        this.validateField('cert_description');
        isValid = !this.errors.cert_name && !this.errors.cert_image && !this.errors.cert_description;
      }
      
      return isValid;
    },
    
    previewImage(event, type) {
      const file = event.target.files[0];
      if (file && file.type.startsWith('image/')) {
        if (type === 'banner') this.formData.banner = file;
        else if (type === 'cert') this.formData.cert_image = file;
        else if (type === 'coupon') this.formData.coupon_image = file;
        
        const reader = new FileReader();
        reader.onload = (e) => {
          if (type === 'banner') this.bannerPreview = e.target.result;
          else if (type === 'cert') this.certImagePreview = e.target.result;
          else if (type === 'coupon') this.couponImagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    
    nextStep() {
      if (this.validateStep(this.currentStep)) {
        if (this.currentStep < this.totalSteps) {
          this.currentStep++;
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      }
    },
    
    prevStep() {
      if (this.currentStep > 1) {
        this.currentStep--;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    }
  }">
    
    {{-- Progress Indicator --}}
    <div class="glass-card rounded-xl p-6">
      <div class="flex items-center justify-between mb-4">
        <template x-for="step in totalSteps" :key="step">
          <div class="flex items-center flex-1">
            <div class="flex flex-col items-center flex-1">
              <div 
                :class="currentStep >= step ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'"
                class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all duration-300 mb-2"
                x-text="step"
              ></div>
              <span 
                :class="currentStep >= step ? 'text-foreground font-medium' : 'text-muted-foreground'"
                class="text-xs text-center transition-all"
                x-text="step === 1 ? 'Basic Info' : step === 2 ? 'Location & Dates' : step === 3 ? 'Participants' : 'Prizes'"
              ></span>
            </div>
            <div 
              x-show="step < totalSteps"
              :class="currentStep > step ? 'bg-primary' : 'bg-muted'"
              class="h-1 flex-1 transition-all duration-300"
            ></div>
          </div>
        </template>
      </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('organization.quests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" novalidate>
      @csrf
      <input type="hidden" name="org_id" value="{{ $organization->id }}">

      {{-- Step 1: Basic Information --}}
      <div x-show="currentStep === 1" x-transition class="glass-card rounded-xl p-6 space-y-6">
        <div>
          <h2 class="text-xl font-bold mb-1">Informasi Dasar</h2>
          <p class="text-sm text-muted-foreground">Mari kita mulai dengan dasar-dasar quest</p>
        </div>

        {{-- Title --}}
        <div>
          <label for="title" class="block text-sm font-medium mb-2">Judul Quest <span class="text-destructive">*</span></label>
          <input 
            type="text" 
            id="title" 
            name="title" 
            x-model="formData.title"
            @input="validateField('title')"
            value="{{ old('title') }}"
            maxlength="255"
            :class="errors.title ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
            class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
            placeholder="e.g., Beach Cleanup Challenge 2025"
          >
          <p x-show="errors.title" class="mt-1.5 text-sm text-destructive" x-text="errors.title" style="display: none;"></p>
          @error('title')
            <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
          @enderror
        </div>

        {{-- Description --}}
        <div>
          <label for="desc" class="block text-sm font-medium mb-2">Deskripsi <span class="text-destructive">*</span></label>
          <textarea 
            id="desc" 
            name="desc" 
            x-model="formData.desc"
            @input="validateField('desc')"
            rows="6"
            :class="errors.desc ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
            class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all resize-none"
            placeholder="Jelaskan tujuan perjalanan Anda, maksudnya, dan apa yang akan dilakukan oleh peserta..."
          >{{ old('desc') }}</textarea>
          <p x-show="errors.desc" class="mt-1.5 text-sm text-destructive" x-text="errors.desc" style="display: none;"></p>
          @error('desc')
            <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
          @enderror
        </div>

        {{-- Banner Upload --}}
        <div>
          <label for="banner" class="block text-sm font-medium mb-2">Banner Quest <span class="text-destructive">*</span></label>
          <div class="relative">
            <input 
              type="file" 
              id="banner" 
              name="banner" 
              accept="image/*"
              @change="previewImage($event, 'banner'); validateField('banner')"
              class="hidden"
            >
            <label 
              for="banner"
              :class="errors.banner ? 'border-destructive' : 'border-border hover:border-primary'"
              class="block w-full aspect-video rounded-lg border-2 border-dashed transition-all cursor-pointer overflow-hidden bg-muted/30"
            >
              <div x-show="!bannerPreview" class="w-full h-full flex flex-col items-center justify-center gap-3">
                <svg class="w-12 h-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div class="text-center">
                  <p class="text-sm font-medium">Click untuk upload gambar banner</p>
                  <p class="text-xs text-muted-foreground mt-1">PNG, JPG up to 2MB</p>
                </div>
              </div>
              <img x-show="bannerPreview" :src="bannerPreview" class="w-full h-full object-cover">
            </label>
          </div>
          <p x-show="errors.banner" class="mt-1.5 text-sm text-destructive" x-text="errors.banner" style="display: none;"></p>
          @error('banner')
            <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
          @enderror
        </div>
      </div>

      {{-- Step 2: Location & Dates --}}
      <div x-show="currentStep === 2" x-transition class="glass-card rounded-xl p-6 space-y-6">
        <div>
          <h2 class="text-xl font-bold mb-1">Lokasi & Timeline</h2>
          <p class="text-sm text-muted-foreground">Tentukan tempat dan waktu di mana quest akan berlangsung.</p>
        </div>

        {{-- Location --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label for="location_name" class="block text-sm font-medium mb-2">Nama Lokasi <span class="text-destructive">*</span></label>
            <input 
              type="text" 
              id="location_name" 
              name="location_name" 
              x-model="formData.location_name"
              @input="validateField('location_name')"
              value="{{ old('location_name') }}"
              :class="errors.location_name ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
              class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              placeholder="e.g., Kuta Beach, Bali"
            >
            <p x-show="errors.location_name" class="mt-1.5 text-sm text-destructive" x-text="errors.location_name" style="display: none;"></p>
            @error('location_name')
              <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="latitude" class="block text-sm font-medium mb-2">Latitude <span class="text-destructive">*</span></label>
            <input 
              type="number" 
              id="latitude" 
              name="latitude" 
              x-model="formData.latitude"
              @input="validateField('latitude')"
              value="{{ old('latitude') }}"
              step="0.00000001"
              min="-90"
              max="90"
              :class="errors.latitude ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
              class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              placeholder="-8.718828"
            >
            <p x-show="errors.latitude" class="mt-1.5 text-sm text-destructive" x-text="errors.latitude" style="display: none;"></p>
            @error('latitude')
              <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="longitude" class="block text-sm font-medium mb-2">Longitude <span class="text-destructive">*</span></label>
            <input 
              type="number" 
              id="longitude" 
              name="longitude" 
              x-model="formData.longitude"
              @input="validateField('longitude')"
              value="{{ old('longitude') }}"
              step="0.00000001"
              min="-180"
              max="180"
              :class="errors.longitude ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
              class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              placeholder="115.176314"
            >
            <p x-show="errors.longitude" class="mt-1.5 text-sm text-destructive" x-text="errors.longitude" style="display: none;"></p>
            @error('longitude')
              <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
            @enderror
          </div>

          <div class="md:col-span-2">
            <label for="radius_meter" class="block text-sm font-medium mb-2">Radius (meters)</label>
            <input 
              type="number" 
              id="radius_meter" 
              name="radius_meter" 
              x-model="formData.radius_meter"
              @input="validateField('radius_meter')"
              value="{{ old('radius_meter', 100) }}"
              min="10"
              :class="errors.radius_meter ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
              class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              placeholder="100"
            >
            <p class="mt-1.5 text-xs text-muted-foreground">Peserta harus berada dalam radius ini untuk melakukan check-in</p>
            <p x-show="errors.radius_meter" class="mt-1.5 text-sm text-destructive" x-text="errors.radius_meter" style="display: none;"></p>
            @error('radius_meter')
              <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Dates --}}
        <div class="space-y-4">
          <h3 class="font-semibold text-sm">Timeline Quest</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="registration_start_at" class="block text-sm font-medium mb-2">Mulai Registrasi <span class="text-destructive">*</span></label>
              <input 
                type="datetime-local" 
                id="registration_start_at" 
                name="registration_start_at" 
                x-model="formData.registration_start_at"
                @input="validateField('registration_start_at')"
                value="{{ old('registration_start_at') }}"
                :class="errors.registration_start_at ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              >
              <p x-show="errors.registration_start_at" class="mt-1.5 text-sm text-destructive" x-text="errors.registration_start_at" style="display: none;"></p>
              @error('registration_start_at')
                <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="registration_end_at" class="block text-sm font-medium mb-2">Akhir Registrasi <span class="text-destructive">*</span></label>
              <input 
                type="datetime-local" 
                id="registration_end_at" 
                name="registration_end_at" 
                x-model="formData.registration_end_at"
                @input="validateField('registration_end_at')"
                value="{{ old('registration_end_at') }}"
                :class="errors.registration_end_at ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              >
              <p x-show="errors.registration_end_at" class="mt-1.5 text-sm text-destructive" x-text="errors.registration_end_at" style="display: none;"></p>
              @error('registration_end_at')
                <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="quest_start_at" class="block text-sm font-medium mb-2">Mulai Quest <span class="text-destructive">*</span></label>
              <input 
                type="datetime-local" 
                id="quest_start_at" 
                name="quest_start_at" 
                x-model="formData.quest_start_at"
                @input="validateField('quest_start_at')"
                value="{{ old('quest_start_at') }}"
                :class="errors.quest_start_at ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              >
              <p x-show="errors.quest_start_at" class="mt-1.5 text-sm text-destructive" x-text="errors.quest_start_at" style="display: none;"></p>
              @error('quest_start_at')
                <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="quest_end_at" class="block text-sm font-medium mb-2">Akhir Quest <span class="text-destructive">*</span></label>
              <input 
                type="datetime-local" 
                id="quest_end_at" 
                name="quest_end_at" 
                x-model="formData.quest_end_at"
                @input="validateField('quest_end_at')"
                value="{{ old('quest_end_at') }}"
                :class="errors.quest_end_at ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              >
              <p x-show="errors.quest_end_at" class="mt-1.5 text-sm text-destructive" x-text="errors.quest_end_at" style="display: none;"></p>
              @error('quest_end_at')
                <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="judging_start_at" class="block text-sm font-medium mb-2">Mulai Penjurian <span class="text-destructive">*</span></label>
              <input 
                type="datetime-local" 
                id="judging_start_at" 
                name="judging_start_at" 
                x-model="formData.judging_start_at"
                @input="validateField('judging_start_at')"
                value="{{ old('judging_start_at') }}"
                :class="errors.judging_start_at ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              >
              <p x-show="errors.judging_start_at" class="mt-1.5 text-sm text-destructive" x-text="errors.judging_start_at" style="display: none;"></p>
              @error('judging_start_at')
                <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="judging_end_at" class="block text-sm font-medium mb-2">Akhir Penjurian <span class="text-destructive">*</span></label>
              <input 
                type="datetime-local" 
                id="judging_end_at" 
                name="judging_end_at" 
                x-model="formData.judging_end_at"
                @input="validateField('judging_end_at')"
                value="{{ old('judging_end_at') }}"
                :class="errors.judging_end_at ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              >
              <p x-show="errors.judging_end_at" class="mt-1.5 text-sm text-destructive" x-text="errors.judging_end_at" style="display: none;"></p>
              @error('judging_end_at')
                <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
              @enderror
            </div>

            <div class="md:col-span-2">
              <label for="prize_distribution_date" class="block text-sm font-medium mb-2">Tanggal Pemberian Hadiah <span class="text-destructive">*</span></label>
              <input 
                type="datetime-local" 
                id="prize_distribution_date" 
                name="prize_distribution_date" 
                x-model="formData.prize_distribution_date"
                @input="validateField('prize_distribution_date')"
                value="{{ old('prize_distribution_date') }}"
                :class="errors.prize_distribution_date ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              >
              <p x-show="errors.prize_distribution_date" class="mt-1.5 text-sm text-destructive" x-text="errors.prize_distribution_date" style="display: none;"></p>
              @error('prize_distribution_date')
                <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Step 3: Participants --}}
      <div x-show="currentStep === 3" x-transition class="glass-card rounded-xl p-6 space-y-6">
        <div>
          <h2 class="text-xl font-bold mb-1">Setting Peserta</h2>
          <p class="text-sm text-muted-foreground">Set limit untuk peserta dan pemenang</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="participant_limit" class="block text-sm font-medium mb-2">Limit Peserta <span class="text-destructive">*</span></label>
            <input 
              type="number" 
              id="participant_limit" 
              name="participant_limit" 
              x-model="formData.participant_limit"
              @input="validateField('participant_limit')"
              value="{{ old('participant_limit') }}"
              min="1"
              :class="errors.participant_limit ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
              class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              placeholder="100"
            >
            <p class="mt-1.5 text-xs text-muted-foreground">Jumlah maximum peserta yang dapat mengikuti quest</p>
            <p x-show="errors.participant_limit" class="mt-1.5 text-sm text-destructive" x-text="errors.participant_limit" style="display: none;"></p>
            @error('participant_limit')
              <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="winner_limit" class="block text-sm font-medium mb-2">Limit Pemenang <span class="text-destructive">*</span></label>
            <input 
              type="number" 
              id="winner_limit" 
              name="winner_limit" 
              x-model="formData.winner_limit"
              @input="validateField('winner_limit')"
              value="{{ old('winner_limit') }}"
              min="1"
              :class="errors.winner_limit ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
              class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              placeholder="10"
            >
            <p class="mt-1.5 text-xs text-muted-foreground">Jumlah maximum pemenang yang akan menerima hadiah</p>
            <p x-show="errors.winner_limit" class="mt-1.5 text-sm text-destructive" x-text="errors.winner_limit" style="display: none;"></p>
            @error('winner_limit')
              <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Optional: Liveness Code --}}
        <div>
          <label for="liveness_code" class="block text-sm font-medium mb-2">Liveness Code (Opsional)</label>
          <input 
            type="text" 
            id="liveness_code" 
            name="liveness_code" 
            value="{{ old('liveness_code') }}"
            maxlength="255"
            class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('liveness_code') border-destructive @enderror"
            placeholder="Masukan kode rahasia untuk verifikasi"
          >
          <p class="mt-1.5 text-xs text-muted-foreground">Kode yang harus dimasukkan oleh peserta selama proses verifikasi</p>
          @error('liveness_code')
            <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
          @enderror
        </div>
      </div>

      {{-- Step 4: Prizes --}}
      <div x-show="currentStep === 4" x-transition class="glass-card rounded-xl p-6 space-y-6">
        <div>
          <h2 class="text-xl font-bold mb-1">Hadiah Quest</h2>
          <p class="text-sm text-muted-foreground">Tentukan hadiah yang akan diterima oleh pemenang</p>
        </div>

        {{-- Certificate Prize --}}
        <div class="p-4 bg-primary/5 border border-primary/20 rounded-xl space-y-4">
          <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            <h3 class="font-semibold">Sertifikat (Required)</h3>
          </div>

          <div>
            <label for="cert_name" class="block text-sm font-medium mb-2">Nama Sertifikat <span class="text-destructive">*</span></label>
            <input 
              type="text" 
              id="cert_name" 
              name="cert_name" 
              x-model="formData.cert_name"
              @input="validateField('cert_name')"
              value="{{ old('cert_name') }}"
              maxlength="255"
              :class="errors.cert_name ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
              class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
              placeholder="e.g., Environmental Hero Certificate"
            >
            <p x-show="errors.cert_name" class="mt-1.5 text-sm text-destructive" x-text="errors.cert_name" style="display: none;"></p>
            @error('cert_name')
              <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="cert_description" class="block text-sm font-medium mb-2">Deskripsi Sertifikat <span class="text-destructive">*</span></label>
            <textarea 
              id="cert_description" 
              name="cert_description" 
              x-model="formData.cert_description"
              @input="validateField('cert_description')"
              rows="3"
              maxlength="5000"
              :class="errors.cert_description ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
              class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all resize-none"
              placeholder="Describe what this certificate represents..."
            >{{ old('cert_description') }}</textarea>
            <p x-show="errors.cert_description" class="mt-1.5 text-sm text-destructive" x-text="errors.cert_description" style="display: none;"></p>
            @error('cert_description')
              <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="cert_image" class="block text-sm font-medium mb-2">Gambar Sertifikat <span class="text-destructive">*</span></label>
            <input 
              type="file" 
              id="cert_image" 
              name="cert_image" 
              accept="image/jpeg,image/jpg,image/png"
              @change="previewImage($event, 'cert'); validateField('cert_image')"
              class="hidden"
            >
            <label 
              for="cert_image"
              class="block w-full aspect-[4/3] rounded-lg border-2 border-dashed border-border hover:border-primary transition-all cursor-pointer overflow-hidden bg-muted/30"
            >
              <div x-show="!certImagePreview" class="w-full h-full flex flex-col items-center justify-center gap-3">
                <svg class="w-10 h-10 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div class="text-center">
                  <p class="text-sm font-medium">Click untuk upload template sertifikat</p>
                  <p class="text-xs text-muted-foreground mt-1">PNG, JPG up to 2MB</p>
                </div>
              </div>
              <img x-show="certImagePreview" :src="certImagePreview" class="w-full h-full object-cover">
            </label>
            <p x-show="errors.cert_image" class="mt-1.5 text-sm text-destructive" x-text="errors.cert_image" style="display: none;"></p>
            @error('cert_image')
              <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Coupon Prize (Optional) --}}
        <div class="p-4 bg-accent/5 border border-accent/20 rounded-xl space-y-4">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
              </svg>
              <h3 class="font-semibold">Kupon (Opsional)</h3>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
              <input 
                type="checkbox" 
                x-model="hasCoupon"
                class="w-4 h-4 rounded border-border text-accent focus:ring-accent"
              >
              <span class="text-sm">Tambah hadiah kupon</span>
            </label>
          </div>

          <div x-show="hasCoupon" x-transition class="space-y-4">
            <div>
              <label for="coupon_name" class="block text-sm font-medium mb-2">Nama Kupon</label>
              <input 
                type="text" 
                id="coupon_name" 
                name="coupon_name" 
                x-model="formData.coupon_name"
                @input="validateField('coupon_name')"
                value="{{ old('coupon_name') }}"
                maxlength="255"
                :required="hasCoupon"
                :class="errors.coupon_name ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all"
                placeholder="e.g., 20% Off Eco Products"
              >
              <p x-show="errors.coupon_name" class="mt-1.5 text-sm text-destructive" x-text="errors.coupon_name" style="display: none;"></p>
              @error('coupon_name')
                <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="coupon_description" class="block text-sm font-medium mb-2">Deskripsi Kupon</label>
              <textarea 
                id="coupon_description" 
                name="coupon_description" 
                x-model="formData.coupon_description"
                @input="validateField('coupon_description')"
                rows="3"
                maxlength="5000"
                :required="hasCoupon"
                :class="errors.coupon_description ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                class="w-full px-4 py-3 rounded-lg bg-background border focus:ring-2 focus:border-primary transition-all resize-none"
                placeholder="Describe the coupon benefits and terms..."
              >{{ old('coupon_description') }}</textarea>
              <p x-show="errors.coupon_description" class="mt-1.5 text-sm text-destructive" x-text="errors.coupon_description" style="display: none;"></p>
              @error('coupon_description')
                <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="coupon_image" class="block text-sm font-medium mb-2">Gambar Kupon</label>
              <input 
                type="file" 
                id="coupon_image" 
                name="coupon_image" 
                accept="image/jpeg,image/jpg,image/png"
                :required="hasCoupon"
                @change="previewImage($event, 'coupon'); validateField('coupon_image')"
                class="hidden"
              >
              <label 
                for="coupon_image"
                class="block w-full aspect-[4/3] rounded-lg border-2 border-dashed border-border hover:border-primary transition-all cursor-pointer overflow-hidden bg-muted/30"
              >
                <div x-show="!couponImagePreview" class="w-full h-full flex flex-col items-center justify-center gap-3">
                  <svg class="w-10 h-10 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <div class="text-center">
                    <p class="text-sm font-medium">Click untuk upload gambar kupon</p>
                    <p class="text-xs text-muted-foreground mt-1">PNG, JPG up to 2MB</p>
                  </div>
                </div>
                <img x-show="couponImagePreview" :src="couponImagePreview" class="w-full h-full object-cover">
              </label>
              <p x-show="errors.coupon_image" class="mt-1.5 text-sm text-destructive" x-text="errors.coupon_image" style="display: none;"></p>
              @error('coupon_image')
                <p class="mt-1.5 text-sm text-destructive">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Navigation Buttons --}}
      <div class="flex items-center justify-between gap-4">
        <button 
          type="button"
          @click="prevStep"
          x-show="currentStep > 1"
          class="px-6 py-3 rounded-lg border border-border hover:bg-muted transition-all font-medium"
        >
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Sebelumnya
          </span>
        </button>

        <div class="flex-1"></div>

        <button 
          type="button"
          @click="nextStep"
          x-show="currentStep < totalSteps"
          class="px-6 py-3 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold"
        >
          <span class="flex items-center gap-2">
            Selanjutnya
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </button>

        <button 
          type="submit"
          x-show="currentStep === totalSteps"
          class="px-6 py-3 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold"
        >
          <span class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Buat Quest
          </span>
        </button>
      </div>
    </form>
  </div>
@endsection
