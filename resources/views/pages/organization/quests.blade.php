@extends('layouts.organization')

@section('content')
@php
$title = 'Quest Management';
$subtitle = "Create and manage your organization's quests";
@endphp
  <div class="space-y-6" x-data="{
    activeTab: 'all',
    searchQuery: '',
    sortBy: 'date',
    showCreateQuestDialog: false,
    currentStep: 1,
    totalSteps: 5,
    isSubmitting: false,
    formErrors: {},
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
    quests: {{ Js::from($quests->items()) }},
    filteredQuests: [],
    
    init() {
      this.updateFilteredQuests();
      this.$watch('activeTab', () => this.updateFilteredQuests());
      this.$watch('searchQuery', () => this.updateFilteredQuests());
      this.$watch('sortBy', () => this.updateFilteredQuests());
      
      // Restore form data from localStorage on page load
      this.restoreFormData();
      
      // Watch formData changes and save to localStorage (excluding file uploads)
      this.$watch('formData', (value) => {
        this.saveFormData();
      });
      
      // Watch currentStep and save it too
      this.$watch('currentStep', (value) => {
        localStorage.setItem('questFormCurrentStep', value);
      });
    },
    
    saveFormData() {
      // Save non-file form data to localStorage
      const dataToSave = {
        title: this.formData.title,
        desc: this.formData.desc,
        location_name: this.formData.location_name,
        latitude: this.formData.latitude,
        longitude: this.formData.longitude,
        radius_meter: this.formData.radius_meter,
        liveness_code: this.formData.liveness_code,
        registration_start_at: this.formData.registration_start_at,
        registration_end_at: this.formData.registration_end_at,
        quest_start_at: this.formData.quest_start_at,
        quest_end_at: this.formData.quest_end_at,
        judging_start_at: this.formData.judging_start_at,
        judging_end_at: this.formData.judging_end_at,
        prize_distribution_date: this.formData.prize_distribution_date,
        participant_limit: this.formData.participant_limit,
        winner_limit: this.formData.winner_limit,
        cert_name: this.formData.cert_name,
        cert_description: this.formData.cert_description,
        coupon_name: this.formData.coupon_name,
        coupon_description: this.formData.coupon_description
      };
      localStorage.setItem('questFormData', JSON.stringify(dataToSave));
    },
    
    restoreFormData() {
      // Restore form data from localStorage
      const savedData = localStorage.getItem('questFormData');
      const savedStep = localStorage.getItem('questFormCurrentStep');
      
      if (savedData) {
        try {
          const parsed = JSON.parse(savedData);
          // Merge saved data with current formData
          Object.keys(parsed).forEach(key => {
            if (parsed[key] !== null && parsed[key] !== undefined && parsed[key] !== '') {
              this.formData[key] = parsed[key];
            }
          });
        } catch (e) {
          console.error('Error restoring form data:', e);
        }
      }
      
      if (savedStep) {
        this.currentStep = parseInt(savedStep);
      }
    },
    
    clearSavedFormData() {
      localStorage.removeItem('questFormData');
      localStorage.removeItem('questFormCurrentStep');
    },
    
    validateStep(step) {
      this.formErrors = {};
      let isValid = true;
      
      if (step === 1) {
        // Basic Information
        if (!this.formData.title || this.formData.title.trim().length === 0) {
          this.formErrors.title = ['Quest title is required'];
          isValid = false;
        } else if (this.formData.title.length > 255) {
          this.formErrors.title = ['Quest title must not exceed 255 characters'];
          isValid = false;
        }
        
        if (!this.formData.desc || this.formData.desc.trim().length === 0) {
          this.formErrors.desc = ['Description is required'];
          isValid = false;
        }
        
        if (!this.formData.banner) {
          this.formErrors.banner = ['Quest banner image is required'];
          isValid = false;
        }
      } else if (step === 2) {
        // Location & Geofencing
        if (!this.formData.location_name || this.formData.location_name.trim().length === 0) {
          this.formErrors.location_name = ['Location name is required'];
          isValid = false;
        }
        
        if (!this.formData.latitude || isNaN(this.formData.latitude)) {
          this.formErrors.latitude = ['Valid latitude is required'];
          isValid = false;
        } else if (this.formData.latitude < -90 || this.formData.latitude > 90) {
          this.formErrors.latitude = ['Latitude must be between -90 and 90'];
          isValid = false;
        }
        
        if (!this.formData.longitude || isNaN(this.formData.longitude)) {
          this.formErrors.longitude = ['Valid longitude is required'];
          isValid = false;
        } else if (this.formData.longitude < -180 || this.formData.longitude > 180) {
          this.formErrors.longitude = ['Longitude must be between -180 and 180'];
          isValid = false;
        }
        
        if (this.formData.radius_meter && (isNaN(this.formData.radius_meter) || this.formData.radius_meter < 10)) {
          this.formErrors.radius_meter = ['Radius must be at least 10 meters'];
          isValid = false;
        }
      } else if (step === 3) {
        // Timeline
        if (!this.formData.registration_start_at) {
          this.formErrors.registration_start_at = ['Registration start date is required'];
          isValid = false;
        }
        if (!this.formData.registration_end_at) {
          this.formErrors.registration_end_at = ['Registration end date is required'];
          isValid = false;
        }
        if (!this.formData.quest_start_at) {
          this.formErrors.quest_start_at = ['Quest start date is required'];
          isValid = false;
        }
        if (!this.formData.quest_end_at) {
          this.formErrors.quest_end_at = ['Quest end date is required'];
          isValid = false;
        }
        if (!this.formData.judging_start_at) {
          this.formErrors.judging_start_at = ['Judging start date is required'];
          isValid = false;
        }
        if (!this.formData.judging_end_at) {
          this.formErrors.judging_end_at = ['Judging end date is required'];
          isValid = false;
        }
        if (!this.formData.prize_distribution_date) {
          this.formErrors.prize_distribution_date = ['Prize distribution date is required'];
          isValid = false;
        }
      } else if (step === 4) {
        // Participants & Winners
        if (!this.formData.participant_limit || isNaN(this.formData.participant_limit) || this.formData.participant_limit < 1) {
          this.formErrors.participant_limit = ['Participant limit must be at least 1'];
          isValid = false;
        }
        
        if (!this.formData.winner_limit || isNaN(this.formData.winner_limit) || this.formData.winner_limit < 1) {
          this.formErrors.winner_limit = ['Winner limit must be at least 1'];
          isValid = false;
        } else if (this.formData.winner_limit > this.formData.participant_limit) {
          this.formErrors.winner_limit = ['Winner limit cannot exceed participant limit'];
          isValid = false;
        }
      } else if (step === 5) {
        // Prizes
        if (!this.formData.cert_name || this.formData.cert_name.trim().length === 0) {
          this.formErrors.cert_name = ['Certificate name is required'];
          isValid = false;
        }
        
        if (!this.formData.cert_image) {
          this.formErrors.cert_image = ['Certificate image is required'];
          isValid = false;
        }
        
        if (!this.formData.cert_description || this.formData.cert_description.trim().length === 0) {
          this.formErrors.cert_description = ['Certificate description is required'];
          isValid = false;
        }
      }
      
      return isValid;
    },
    
    nextStep() {
      if (this.validateStep(this.currentStep)) {
        if (this.currentStep < this.totalSteps) {
          this.currentStep++;
        }
      }
    },
    
    prevStep() {
      if (this.currentStep > 1) {
        this.currentStep--;
        this.formErrors = {};
      }
    },
    
    async submitQuestForm(event) {
      event.preventDefault();
      
      if (this.isSubmitting) return;
      
      // Final validation
      if (!this.validateStep(this.currentStep)) {
        return;
      }
      
      this.isSubmitting = true;
      this.formErrors = {};
      
      const form = event.target;
      const formData = new FormData(form);
      
      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        });
        
        const data = await response.json();
        
        if (response.ok) {
          // Clear saved form data on successful submission
          this.clearSavedFormData();
          window.location.href = data.redirect || '{{ route('organization.quests.index') }}';
        } else {
          if (data.errors) {
            this.formErrors = data.errors;
            // Go back to first step with errors
            this.currentStep = 1;
          } else {
            alert(data.message || 'Failed to create quest. Please try again.');
          }
          this.isSubmitting = false;
        }
      } catch (error) {
        console.error('Quest creation error:', error);
        alert('An error occurred while creating the quest. Please try again.');
        this.isSubmitting = false;
      }
    },
    
    resetQuestForm() {
      this.currentStep = 1;
      this.formErrors = {};
      this.isSubmitting = false;
      this.formData = {
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
      };
      // Clear saved data from localStorage
      this.clearSavedFormData();
      // Reset file inputs
      const form = document.getElementById('createQuestForm');
      if (form) form.reset();
    },
    
    updateFilteredQuests() {
      let filtered = this.quests;
      
      // Filter by tab
      if (this.activeTab !== 'all') {
        filtered = filtered.filter(quest => {
          if (this.activeTab === 'active') return quest.status === 'ACTIVE';
          if (this.activeTab === 'review') return quest.status === 'IN_REVIEW';
          if (this.activeTab === 'ended') return quest.status === 'ENDED';
          return true;
        });
      }
      
      // Filter by search
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(quest => 
          quest.title.toLowerCase().includes(query)
        );
      }
      
      // Sort
      filtered.sort((a, b) => {
        if (this.sortBy === 'date') {
          return new Date(b.quest_start_at) - new Date(a.quest_start_at);
        } else if (this.sortBy === 'participants') {
          return (b.quest_participants_count || 0) - (a.quest_participants_count || 0);
        }
        return 0;
      });
      
      this.filteredQuests = filtered;
    },
    
    getStatusBadgeClass(status) {
      const classes = {
        'DRAFT': 'bg-muted text-muted-foreground',
        'IN_REVIEW': 'bg-highlight/20 text-highlight-foreground',
        'ACTIVE': 'bg-primary/20 text-primary',
        'ENDED': 'bg-destructive/20 text-destructive',
        'APPROVED': 'bg-secondary/20 text-secondary'
      };
      return classes[status] || classes['DRAFT'];
    },
    
    getStatusText(status) {
      const texts = {
        'DRAFT': 'Draft',
        'IN_REVIEW': 'In Review',
        'ACTIVE': 'Active',
        'ENDED': 'Ended',
        'APPROVED': 'Approved'
      };
      return texts[status] || status;
    },
    
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
      });
    }
  }">
    
    {{-- Header with Create Button --}}
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
      <div class="flex flex-col gap-1">
        <p class="text-sm text-muted-foreground">Manage all your environmental quests in one place</p>
      </div>
      
      <button 
        @click="showCreateQuestDialog = true"
        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold whitespace-nowrap"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Create Quest
      </button>
    </div>

    {{-- Create Quest Dialog --}}
    <div x-show="showCreateQuestDialog" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50"
         @click="showCreateQuestDialog = false"
         @keydown.escape.window="showCreateQuestDialog = false"
         style="display: none;">
    </div>

    <div x-show="showCreateQuestDialog"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 flex items-center justify-center z-50 p-4"
         @click.away="!isSubmitting && (showCreateQuestDialog = false)"
         style="display: none;">
      
      <div class="glass-card rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col" @click.stop>
        {{-- Header with Progress --}}
        <div class="bg-background/95 backdrop-blur-sm border-b border-border p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-2xl font-bold flex items-center gap-2">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                Create New Quest
              </h2>
              <p class="text-muted-foreground text-sm mt-1">
                Step <span x-text="currentStep"></span> of <span x-text="totalSteps"></span>: 
                <span x-text="['Basic Information', 'Location & Geofencing', 'Timeline', 'Participants & Winners', 'Prizes & Rewards'][currentStep - 1]"></span>
              </p>
            </div>
            <button @click="!isSubmitting && (showCreateQuestDialog = false, resetQuestForm())" :disabled="isSubmitting" class="text-muted-foreground hover:text-foreground transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          {{-- Progress Bar --}}
          <div class="flex items-center gap-2">
            <template x-for="step in totalSteps" :key="step">
              <div class="flex-1">
                <div class="h-2 rounded-full transition-all duration-300"
                     :class="step <= currentStep ? 'bg-gradient-to-r from-primary to-secondary' : 'bg-muted'">
                </div>
              </div>
            </template>
          </div>
        </div>

        {{-- Form --}}
        <form @submit="submitQuestForm" id="createQuestForm" action="{{ route('organization.quests.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
          @csrf
          <input type="hidden" name="org_id" value="{{ $currentOrg['id'] ?? '' }}">
          
          <div class="p-6 space-y-6">
            {{-- Error Summary --}}
            <div x-show="Object.keys(formErrors).length > 0" class="bg-destructive/10 border border-destructive/20 rounded-xl p-4" style="display: none;">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-destructive mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                  <h4 class="font-semibold text-destructive mb-1">Please fix the following errors:</h4>
                  <ul class="text-sm text-destructive/90 list-disc list-inside space-y-0.5">
                    <template x-for="(error, field) in formErrors" :key="field">
                      <li x-text="Array.isArray(error) ? error[0] : error"></li>
                    </template>
                  </ul>
                </div>
              </div>
            </div>
          
            {{-- Step 1: Basic Information --}}
            <div x-show="currentStep === 1" x-transition class="space-y-4">
              <h4 class="font-medium text-sm text-muted-foreground">Basic Information</h4>
            
              <div class="space-y-2">
                <label for="quest_title" class="block font-medium text-sm">Quest Title *</label>
                <input type="text" name="title" id="quest_title" x-model="formData.title" required
                     :class="formErrors.title ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                     class="w-full px-4 py-3 bg-background border rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition-all"
                     placeholder="e.g., Beach Cleanup Jakarta">
              <p x-show="formErrors.title" class="text-xs text-destructive" x-text="formErrors.title?.[0]" style="display: none;"></p>
            </div>

            <div class="space-y-2">
              <label for="quest_desc" class="block font-medium text-sm">Description *</label>
              <textarea name="desc" id="quest_desc" rows="4" required
                        x-model="formData.desc"
                        :class="formErrors.desc ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                        class="w-full px-4 py-3 bg-background border rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition-all resize-none"
                        placeholder="Describe the quest objectives and requirements..."></textarea>
              <p x-show="formErrors.desc" class="text-xs text-destructive" x-text="formErrors.desc?.[0]" style="display: none;"></p>
            </div>
          </div>

          {{-- Location & Geofencing --}}
          <div class="space-y-4">
            <h4 class="font-medium text-sm text-muted-foreground flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Location & Geofencing
            </h4>
            
            <div class="space-y-2">
              <label for="location_name" class="block font-medium text-sm">Location Name *</label>
              <input type="text" name="location_name" id="location_name" required
                     x-model="formData.location_name"
                     class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                     placeholder="e.g., Ancol Beach, Jakarta">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="space-y-2">
                <label for="latitude" class="block font-medium text-sm">Latitude *</label>
                <input type="number" name="latitude" id="latitude" step="0.000001" required
                       x-model="formData.latitude"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                       placeholder="-6.1234">
              </div>
              
              <div class="space-y-2">
                <label for="longitude" class="block font-medium text-sm">Longitude *</label>
                <input type="number" name="longitude" id="longitude" step="0.000001" required
                       x-model="formData.longitude"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                       placeholder="106.8456">
              </div>
              
              <div class="space-y-2">
                <label for="radius_meter" class="block font-medium text-sm">Radius (meters) *</label>
                <input type="number" name="radius_meter" id="radius_meter" min="10" required
                       x-model="formData.radius_meter"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
            </div>

            <div class="space-y-2">
              <label for="liveness_code" class="block font-medium text-sm">Liveness Code (Secret on-site code)</label>
              <input type="text" name="liveness_code" id="liveness_code"
                     x-model="formData.liveness_code"
                     class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                     placeholder="e.g., ECO2024">
              <p class="text-xs text-muted-foreground">This code will be shown at the event location for participants to verify attendance.</p>
            </div>
          </div>

          {{-- Timeline --}}
          <div class="space-y-4">
            <h4 class="font-medium text-sm text-muted-foreground flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Timeline
            </h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="registration_start_at" class="block font-medium text-sm">Registration Start *</label>
                <input type="datetime-local" name="registration_start_at" id="registration_start_at" required
                       x-model="formData.registration_start_at"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
              
              <div class="space-y-2">
                <label for="registration_end_at" class="block font-medium text-sm">Registration End *</label>
                <input type="datetime-local" name="registration_end_at" id="registration_end_at" required
                       x-model="formData.registration_end_at"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="quest_start_at" class="block font-medium text-sm">Quest Start *</label>
                <input type="datetime-local" name="quest_start_at" id="quest_start_at" required
                       x-model="formData.quest_start_at"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
              
              <div class="space-y-2">
                <label for="quest_end_at" class="block font-medium text-sm">Quest End *</label>
                <input type="datetime-local" name="quest_end_at" id="quest_end_at" required
                       x-model="formData.quest_end_at"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="judging_start_at" class="block font-medium text-sm">Judging Start *</label>
                <input type="datetime-local" name="judging_start_at" id="judging_start_at" required
                       x-model="formData.judging_start_at"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
              
              <div class="space-y-2">
                <label for="judging_end_at" class="block font-medium text-sm">Judging End *</label>
                <input type="datetime-local" name="judging_end_at" id="judging_end_at" required
                       x-model="formData.judging_end_at"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
            </div>

            <div class="space-y-2">
              <label for="prize_distribution_date" class="block font-medium text-sm">Prize Distribution Date *</label>
              <input type="datetime-local" name="prize_distribution_date" id="prize_distribution_date" required
                     x-model="formData.prize_distribution_date"
                     class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
            </div>
          </div>

          {{-- Participants & Winners --}}
          <div class="space-y-4">
            <h4 class="font-medium text-sm text-muted-foreground flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              Participants & Winners
            </h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="participant_limit" class="block font-medium text-sm">Max Participants *</label>
                <input type="number" name="participant_limit" id="participant_limit" required
                       x-model="formData.participant_limit"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
              
              <div class="space-y-2">
                <label for="winner_limit" class="block font-medium text-sm">Max Winners *</label>
                <input type="number" name="winner_limit" id="winner_limit" required
                       x-model="formData.winner_limit"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
            </div>
          </div>

          {{-- Images --}}
          <div class="space-y-4">
            <h4 class="font-medium text-sm text-muted-foreground flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Quest Images & Prizes
            </h4>
            
            <div class="space-y-2">
              <label for="banner_quest" class="block font-medium text-sm">Quest Banner *</label>
              <input type="file" name="banner" id="banner_quest" accept="image/*" required
                     :class="formErrors.banner ? 'border-destructive focus:ring-destructive' : 'border-border focus:ring-primary'"
                     class="w-full px-4 py-3 bg-background border rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition-all">
              <p x-show="formErrors.banner" class="text-xs text-destructive" x-text="formErrors.banner?.[0]" style="display: none;"></p>
              <p x-show="!formErrors.banner" class="text-xs text-muted-foreground">Quest cover image (wide landscape recommended)</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="cert_name" class="block font-medium text-sm">Certificate Name *</label>
                <input type="text" name="cert_name" id="cert_name" required
                       x-model="formData.cert_name"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
              
              <div class="space-y-2">
                <label for="cert_image" class="block font-medium text-sm">Certificate Image *</label>
                <input type="file" name="cert_image" id="cert_image" accept="image/jpeg,image/jpg,image/png" required
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
              </div>
            </div>

            <div class="space-y-2">
              <label for="cert_description" class="block font-medium text-sm">Certificate Description *</label>
              <textarea name="cert_description" id="cert_description" rows="2" required
                        x-model="formData.cert_description"
                        class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none"
                        placeholder="Description of the certificate prize..."></textarea>
            </div>

            <div class="border-t border-border pt-4 space-y-4">
              <p class="text-sm font-medium text-muted-foreground">Coupon Prize (Optional)</p>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <label for="coupon_name" class="block font-medium text-sm">Coupon Name</label>
                  <input type="text" name="coupon_name" id="coupon_name"
                         x-model="formData.coupon_name"
                         class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                         placeholder="e.g., Discount Voucher">
                </div>
                
                <div class="space-y-2">
                  <label for="coupon_image" class="block font-medium text-sm">Coupon Image</label>
                  <input type="file" name="coupon_image" id="coupon_image" accept="image/jpeg,image/jpg,image/png"
                         class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
              </div>

              <div class="space-y-2">
                <label for="coupon_description" class="block font-medium text-sm">Coupon Description</label>
                <textarea name="coupon_description" id="coupon_description" rows="2"
                          x-model="formData.coupon_description"
                          class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none"
                          placeholder="Description of the coupon prize..."></textarea>
              </div>
            </div>
          </div>

          {{-- Footer Actions --}}
          <div class="flex justify-end gap-3 pt-4 border-t border-border">
            <button type="button" @click="showCreateQuestDialog = false; resetQuestForm()" :disabled="isSubmitting"
                    class="px-6 py-3 rounded-lg border border-border hover:bg-muted/50 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
              Cancel
            </button>
            <button type="submit" :disabled="isSubmitting"
                    class="px-6 py-3 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center gap-2">
              <svg x-show="isSubmitting" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24" style="display: none;">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span x-text="isSubmitting ? 'Creating...' : 'Create Quest'">Create Quest</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Tabs --}}
    <div class="glass-card rounded-xl overflow-hidden">
      <div class="border-b border-border">
        <div class="flex overflow-x-auto">
          <button 
            @click="activeTab = 'all'"
            :class="activeTab === 'all' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'"
            class="px-6 py-4 font-medium border-b-2 transition-colors whitespace-nowrap"
          >
            All Quests
          </button>
          <button 
            @click="activeTab = 'active'"
            :class="activeTab === 'active' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'"
            class="px-6 py-4 font-medium border-b-2 transition-colors whitespace-nowrap"
          >
            Active
          </button>
          <button 
            @click="activeTab = 'review'"
            :class="activeTab === 'review' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'"
            class="px-6 py-4 font-medium border-b-2 transition-colors whitespace-nowrap"
          >
            In Review
          </button>
          <button 
            @click="activeTab = 'ended'"
            :class="activeTab === 'ended' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'"
            class="px-6 py-4 font-medium border-b-2 transition-colors whitespace-nowrap"
          >
            Ended
          </button>
        </div>
      </div>

      {{-- Filters --}}
      <div class="p-4 bg-muted/30 border-b border-border">
        <div class="flex flex-col sm:flex-row gap-3">
          <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
              type="text" 
              x-model="searchQuery"
              placeholder="Search quests by title..." 
              class="pl-9 w-full px-4 py-2.5 rounded-lg bg-background border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all"
            >
          </div>
          
          <div class="relative w-full sm:w-48">
            <select 
              x-model="sortBy"
              class="w-full px-4 py-2.5 pr-10 rounded-lg bg-background border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all appearance-none"
            >
              <option value="date">Sort by Date</option>
              <option value="participants">Sort by Participants</option>
            </select>
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </div>
      </div>

      {{-- Quest Cards --}}
      <div class="p-6">
        <template x-if="filteredQuests.length === 0">
          <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto mb-4 text-muted-foreground opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            <p class="text-lg font-medium text-muted-foreground mb-2">No quests found</p>
            <p class="text-sm text-muted-foreground mb-6">
              <template x-if="searchQuery">Try adjusting your search</template>
              <template x-if="!searchQuery">Create your first quest to get started</template>
            </p>
            <template x-if="!searchQuery">
              <button 
                @click="showCreateQuestDialog = true"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Your First Quest
              </button>
            </template>
          </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <template x-for="quest in filteredQuests" :key="quest.id">
            <a :href="`{{ route('organization.quests.index') }}/${quest.id}`" class="group">
              <div class="glass-card rounded-xl overflow-hidden hover-lift transition-all duration-300 h-full flex flex-col">
                {{-- Banner Image --}}
                <div class="relative h-48 bg-muted overflow-hidden">
                  <template x-if="quest.banner_url">
                    <img 
                      :src="quest.banner_url" 
                      :alt="quest.title"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    >
                  </template>
                  <template x-if="!quest.banner_url">
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/20 to-secondary/20">
                      <svg class="w-16 h-16 text-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                      </svg>
                    </div>
                  </template>
                  
                  {{-- Status Badge --}}
                  <div class="absolute top-3 right-3">
                    <span 
                      :class="getStatusBadgeClass(quest.status)"
                      class="px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-sm"
                      x-text="getStatusText(quest.status)"
                    ></span>
                  </div>
                </div>

                {{-- Quest Info --}}
                <div class="p-4 flex-1 flex flex-col">
                  <h3 class="font-bold text-lg mb-2 line-clamp-2 group-hover:text-primary transition-colors" x-text="quest.title"></h3>
                  
                  {{-- Stats --}}
                  <div class="flex items-center gap-4 text-sm text-muted-foreground mb-3">
                    <div class="flex items-center gap-1.5">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                      </svg>
                      <span x-text="`${quest.quest_participants_count || 0}/${quest.participant_limit || 0}`"></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <span class="line-clamp-1" x-text="quest.location_name || 'TBA'"></span>
                    </div>
                  </div>

                  {{-- Dates --}}
                  <div class="mt-auto pt-3 border-t border-border space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                      <span class="text-muted-foreground">Registration:</span>
                      <span class="font-medium" x-text="`${formatDate(quest.registration_start_at)} - ${formatDate(quest.registration_end_at)}`"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                      <span class="text-muted-foreground">Quest Period:</span>
                      <span class="font-medium" x-text="`${formatDate(quest.quest_start_at)} - ${formatDate(quest.quest_end_at)}`"></span>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </template>
        </div>
      </div>
    </div>

    {{-- Pagination --}}
    @if($quests->hasPages())
      <div class="flex justify-center">
        <div class="glass-card rounded-xl p-4">
          {{ $quests->links() }}
        </div>
      </div>
    @endif
  </div>
@endsection
