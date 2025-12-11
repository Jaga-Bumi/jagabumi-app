@extends('layouts.main')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-background via-background to-primary/5 py-12">
        <div class="container mx-auto px-4">
            {{-- Header --}}
            <div class="text-center mb-12 animate-slide-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary border border-primary/20 text-sm font-medium mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Request Approved
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    Create Your <span class="gradient-text">Organization</span>
                </h1>
                <p class="text-muted-foreground text-lg max-w-2xl mx-auto">
                    Your organization request has been approved! Complete the details below to launch your organization on JagaBumi.
                </p>
                <div class="mt-6 inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-accent/10 border border-accent/20 text-foreground">
                    <svg class="w-5 h-5 text-accent flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">You can only create <strong>ONE</strong> organization as CREATOR</span>
                </div>
            </div>

            {{-- Form --}}
            <div class="max-w-4xl mx-auto">
                <form id="create-org-form" action="{{ route('organization.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    {{-- Basic Information --}}
                    <div class="glass-card p-8 rounded-2xl shadow-card animate-slide-up">
                        <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center shadow-glow">
                                <span class="text-primary-foreground font-bold">1</span>
                            </div>
                            Basic Information
                        </h2>

                        <div class="space-y-6">
                            {{-- Organization Name --}}
                            <div>
                                <label for="name" class="block font-medium mb-2">
                                    Organization Name <span class="text-destructive">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $approvedRequest->organization_name) }}"
                                       required
                                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                                @error('name')
                                    <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Handle --}}
                            <div>
                                <label for="handle" class="block font-medium mb-2">
                                    Handle <span class="text-destructive">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground">@</span>
                                    <input type="text" 
                                           name="handle" 
                                           id="handle" 
                                           value="{{ old('handle') }}"
                                           required
                                           pattern="[a-zA-Z0-9_]+"
                                           class="w-full pl-8 pr-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                           placeholder="organization_handle">
                                </div>
                                <p class="mt-1 text-sm text-muted-foreground">Only letters, numbers, and underscores allowed</p>
                                @error('handle')
                                    <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="org_email" class="block font-medium mb-2">
                                    Organization Email <span class="text-destructive">*</span>
                                </label>
                                <input type="email" 
                                       name="org_email" 
                                       id="org_email" 
                                       value="{{ old('org_email') }}"
                                       required
                                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                       placeholder="contact@organization.com">
                                @error('org_email')
                                    <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Motto --}}
                            <div>
                                <label for="motto" class="block font-medium mb-2">
                                    Motto
                                </label>
                            <input type="text" 
                                   name="motto" 
                                   id="motto" 
                                   value="{{ old('motto') }}"
                                   maxlength="100"
                                   class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                   placeholder="Your organization's inspiring motto">
                            <p class="mt-1 text-sm text-muted-foreground">Optional tagline for your organization</p>
                            @error('motto')
                                <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="desc" class="block font-medium mb-2">
                                Description <span class="text-destructive">*</span>
                            </label>
                            <textarea name="desc" 
                                      id="desc" 
                                      rows="6" 
                                      required
                                      class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none"
                                      placeholder="Tell us about your organization's mission, vision, and goals...">{{ old('desc', $approvedRequest->organization_description) }}</textarea>
                            @error('desc')
                                <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Visual Branding --}}
                <div class="glass-card p-8 rounded-2xl shadow-card animate-slide-up">
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center shadow-glow">
                            <span class="text-primary-foreground font-bold">2</span>
                        </div>
                        Visual Branding
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Logo Upload --}}
                        <div>
                            <label for="logo_img" class="block font-medium mb-2">
                                Organization Logo <span class="text-destructive">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       name="logo_img" 
                                       id="logo_img" 
                                       accept="image/*"
                                       required
                                       class="hidden"
                                       onchange="previewImage(this, 'logo-preview')">
                                <label for="logo_img" 
                                       class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-border rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition-all bg-muted/30">
                                    <div id="logo-preview" class="flex flex-col items-center justify-center w-full h-full">
                                        <svg class="w-12 h-12 text-muted-foreground mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-foreground text-sm font-medium">Click to upload logo</span>
                                        <span class="text-muted-foreground text-xs mt-1">Square image recommended</span>
                                    </div>
                                </label>
                            </div>
                            @error('logo_img')
                                <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Banner Upload --}}
                        <div>
                            <label for="banner_img" class="block font-medium mb-2">
                                Banner Image <span class="text-destructive">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       name="banner_img" 
                                       id="banner_img" 
                                       accept="image/*"
                                       required
                                       class="hidden"
                                       onchange="previewImage(this, 'banner-preview')">
                                <label for="banner_img" 
                                       class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-border rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition-all bg-muted/30">
                                    <div id="banner-preview" class="flex flex-col items-center justify-center w-full h-full">
                                        <svg class="w-12 h-12 text-muted-foreground mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-foreground text-sm font-medium">Click to upload banner</span>
                                        <span class="text-muted-foreground text-xs mt-1">Wide landscape image recommended</span>
                                    </div>
                                </label>
                            </div>
                            @error('banner_img')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Contact & Social Media --}}
                <div class="glass-card p-8 rounded-2xl shadow-card animate-slide-up">
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center shadow-glow">
                            <span class="text-primary-foreground font-bold">3</span>
                        </div>
                        Contact & Social Media
                    </h2>

                    <div class="space-y-6">
                        {{-- Website --}}
                        <div>
                            <label for="website_url" class="font-medium mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                Website URL
                            </label>
                            <input type="url" 
                                   name="website_url" 
                                   id="website_url" 
                                   value="{{ old('website_url', $approvedRequest->website_url) }}"
                                   class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                   placeholder="https://www.yourorganization.com">
                            @error('website_url')
                                <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Instagram --}}
                        <div>
                            <label for="instagram_url" class="font-medium mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                Instagram
                            </label>
                            <input type="url" 
                                   name="instagram_url" 
                                   id="instagram_url" 
                                   value="{{ old('instagram_url') }}"
                                   class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                   placeholder="https://www.instagram.com/yourorganization">
                            @error('instagram_url')
                                <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- X (Twitter) --}}
                        <div>
                            <label for="x_url" class="font-medium mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-white/80" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                                X (Twitter)
                            </label>
                            <input type="url" 
                                   name="x_url" 
                                   id="x_url" 
                                   value="{{ old('x_url') }}"
                                   class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                   placeholder="https://x.com/yourorganization">
                            @error('x_url')
                                <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Facebook --}}
                        <div>
                            <label for="facebook_url" class="font-medium mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </label>
                            <input type="url" 
                                   name="facebook_url" 
                                   id="facebook_url" 
                                   value="{{ old('facebook_url') }}"
                                   class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                   placeholder="https://www.facebook.com/yourorganization">
                            @error('facebook_url')
                                <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center justify-between pt-6">
                    <a href="{{ route('dashboard.index') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold glass-card hover-lift transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-8 py-3 rounded-lg font-semibold gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Organization
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Image Preview Script --}}
    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" 
                             class="w-full h-full object-cover rounded-lg"
                             alt="Preview">
                    `;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const validator = new FormValidator('create-org-form', {
                name: {
                    required: true,
                    max: 30,
                    messages: {
                        required: 'Organization name is required.',
                        max: 'Name cannot exceed 30 characters.'
                    }
                },
                handle: {
                    required: true,
                    max: 30,
                    pattern: '^[a-zA-Z0-9_-]+$',
                    messages: {
                        required: 'Handle is required.',
                        max: 'Handle cannot exceed 30 characters.',
                        pattern: 'Handle can only contain letters, numbers, underscores, and hyphens.'
                    }
                },
                org_email: {
                    required: true,
                    email: true,
                    max: 255,
                    messages: {
                        required: 'Organization email is required.',
                        email: 'Please enter a valid email address.',
                        max: 'Email cannot exceed 255 characters.'
                    }
                },
                desc: {
                    required: true,
                    messages: {
                        required: 'Description is required.'
                    }
                },
                motto: {
                    required: true,
                    max: 255,
                    messages: {
                        required: 'Motto is required.',
                        max: 'Motto cannot exceed 255 characters.'
                    }
                },
                banner_img: {
                    required: true,
                    file: true,
                    image: true,
                    maxSize: 5120,
                    messages: {
                        required: 'Banner image is required.',
                        image: 'Banner must be an image file.',
                        maxSize: 'Banner image cannot exceed 5MB.'
                    }
                },
                logo_img: {
                    required: true,
                    file: true,
                    image: true,
                    maxSize: 2048,
                    messages: {
                        required: 'Logo image is required.',
                        image: 'Logo must be an image file.',
                        maxSize: 'Logo image cannot exceed 2MB.'
                    }
                },
                website_url: {
                    url: true,
                    messages: {
                        url: 'Please enter a valid website URL.'
                    }
                },
                instagram_url: {
                    url: true,
                    messages: {
                        url: 'Please enter a valid Instagram URL.'
                    }
                },
                x_url: {
                    url: true,
                    messages: {
                        url: 'Please enter a valid X/Twitter URL.'
                    }
                },
                facebook_url: {
                    url: true,
                    messages: {
                        url: 'Please enter a valid Facebook URL.'
                    }
                }
            });

            // Add character counters
            validator.updateCharacterCount('name', 30);
            validator.updateCharacterCount('handle', 30);
            validator.updateCharacterCount('motto', 255);
        });
    </script>
@endsection
