@php
$faqs = [
    [
        'question' => 'Apa itu JagaBumi.id?',
        'answer' => 'JagaBumi.id adalah platform komunitas yang menghubungkan individu dan organisasi dalam misi menjaga kelestarian lingkungan. Kami menyediakan berbagai quest atau tantangan lingkungan yang dapat diikuti untuk mendapatkan sertifikat dan reward.',
    ],
    [
        'question' => 'Bagaimana cara mengikuti quest?',
        'answer' => 'Untuk mengikuti quest, Anda perlu mendaftar akun terlebih dahulu. Setelah login, pilih quest yang ingin diikuti, baca syarat dan ketentuan, lalu klik tombol "Ikuti Quest". Ikuti instruksi dan kumpulkan bukti partisipasi sesuai ketentuan.',
    ],
    [
        'question' => 'Apa saja reward yang bisa didapatkan?',
        'answer' => 'Reward bervariasi tergantung quest yang diikuti. Umumnya termasuk sertifikat digital yang dapat diverifikasi, voucher belanja, merchandise eco-friendly, dan kesempatan untuk masuk ke leaderboard bulanan.',
    ],
    [
        'question' => 'Bagaimana cara mendaftar sebagai organisasi?',
        'answer' => 'Organisasi dapat mendaftar dengan memilih "Daftar sebagai Organisasi" saat registrasi. Lengkapi profil organisasi termasuk dokumen legal, lalu tim kami akan memverifikasi dalam 1-3 hari kerja. Setelah terverifikasi, organisasi dapat membuat dan mengelola quest.',
    ],
    [
        'question' => 'Apakah JagaBumi.id gratis?',
        'answer' => 'Ya, JagaBumi.id gratis untuk pengguna individu. Anda dapat mengikuti quest, mendapatkan sertifikat, dan bergabung dalam komunitas tanpa biaya. Untuk organisasi, tersedia paket gratis dengan fitur terbatas dan paket premium untuk fitur lebih lengkap.',
    ],
];
@endphp

<section class="py-20 bg-secondary/30">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-12 animate-fade-in">
                <span class="text-sm font-medium text-primary uppercase tracking-wider">
                    FAQ
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-foreground mt-2">
                    Pertanyaan yang Sering Diajukan
                </h2>
            </div>

            <div class="space-y-4 animate-fade-up">
                @foreach($faqs as $i => $faq)
                    <div class="glass-card px-6 border-none" style="animation-delay: {{ $i * 0.1 }}s;">
                        <details class="group">
                            <summary class="flex items-center justify-between cursor-pointer list-none py-5 font-medium text-foreground hover:text-primary transition-colors">
                                <span>{{ $faq['question'] }}</span>
                                <x-heroicon-o-chevron-down class="w-5 h-5 transition-transform group-open:rotate-180" />
                            </summary>
                            <div class="text-muted-foreground pb-5 leading-relaxed">
                                {{ $faq['answer'] }}
                            </div>
                        </details>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
