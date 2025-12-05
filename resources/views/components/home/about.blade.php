@php
$features = [
    [
        'icon' => 'leaf',
        'title' => 'Misi Hijau',
        'description' => 'Berkomitmen untuk menjaga kelestarian lingkungan melalui aksi nyata dan kolaborasi.',
    ],
    [
        'icon' => 'users',
        'title' => 'Komunitas Aktif',
        'description' => 'Bergabung dengan ribuan aktivis lingkungan dari seluruh Indonesia.',
    ],
    [
        'icon' => 'award',
        'title' => 'Apresiasi Nyata',
        'description' => 'Dapatkan sertifikat dan reward atas kontribusimu untuk bumi.',
    ],
    [
        'icon' => 'globe',
        'title' => 'Dampak Global',
        'description' => 'Aksi lokal dengan dampak yang terasa hingga ke skala global.',
    ],
];
@endphp

<section class="py-20 bg-background">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-6 animate-fade-in">
                <span class="text-sm font-medium text-primary uppercase tracking-wider">
                    Tentang Kami
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-foreground">
                    Bersama Menjaga Bumi untuk 
                    <span class="text-gradient">Generasi Mendatang</span>
                </h2>
                <p class="text-muted-foreground leading-relaxed">
                    JagaBumi.id adalah platform yang menghubungkan individu dan organisasi 
                    dalam misi bersama menjaga kelestarian lingkungan. Melalui quest yang 
                    menyenangkan dan reward yang nyata, kami membuat aksi lingkungan menjadi 
                    lebih bermakna dan terarah.
                </p>
                <p class="text-muted-foreground leading-relaxed">
                    Didirikan pada tahun 2024, kami telah berhasil membantu lebih dari 50.000 
                    pahlawan lingkungan untuk berkontribusi dalam berbagai kegiatan pelestarian 
                    alam di seluruh Indonesia.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 animate-scale-in">
                @foreach($features as $i => $feature)
                    <div class="glass-card p-6 group hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: {{ $i * 0.1 }}s;">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-emerald-light flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            @if($feature['icon'] === 'leaf')
                                <x-heroicon-o-cube class="w-6 h-6 text-primary-foreground" />
                            @elseif($feature['icon'] === 'users')
                                <x-heroicon-o-user-group class="w-6 h-6 text-primary-foreground" />
                            @elseif($feature['icon'] === 'award')
                                <x-heroicon-o-shield-check class="w-6 h-6 text-primary-foreground" />
                            @else
                                <x-heroicon-o-globe-alt class="w-6 h-6 text-primary-foreground" />
                            @endif
                        </div>
                        <h3 class="font-semibold text-foreground mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-muted-foreground">{{ $feature['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
