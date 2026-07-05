<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIG Bencana Bekasi</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @stack('styles')
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'poppins'],
                    },
                    colors: {
                        primary: '#12395C',
                        secondary: '#D45B1F',
                        brandBg: '#F2EFEB',
                        brandSurface: '#FFFEFC',
                    }
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes guideFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        @keyframes guidePulse {
            0% { transform: scale(1); opacity: 0.72; }
            70% { transform: scale(1.45); opacity: 0; }
            100% { transform: scale(1.45); opacity: 0; }
        }

        .guide-callout {
            animation: guideFloat 2.2s ease-in-out infinite;
        }

        .guide-pulse-ring {
            animation: guidePulse 1.8s ease-out infinite;
        }
    </style>

    @php
        $isEmbed = request()->boolean('embed');
    @endphp

    @if ($isEmbed)
        <style>
            html,
            body {
                background: transparent !important;
                overflow-x: hidden;
            }

            body {
                min-height: auto !important;
            }
        </style>

        <style>
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
    @endif
</head>

<body class="{{ $isEmbed ? 'bg-transparent text-gray-800 antialiased' : 'bg-brandBg text-gray-800 antialiased flex flex-col min-h-screen' }}">

    @if (! $isEmbed)
        <!-- Navigation -->
        <nav class="bg-brandBg sticky top-0 z-50 pt-4 pb-2">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center h-16 bg-brandSurface rounded-full px-4 sm:px-6 shadow-sm border border-gray-100 gap-3">
                    
                    <!-- Logo Left -->
                    <a href="/" class="flex items-center gap-3 shrink-0">
                        <div class="bg-primary text-white p-2 rounded-full">
                            <i data-lucide="map" class="w-5 h-5"></i>
                        </div>
                        <div class="hidden sm:flex flex-col">
                            <span class="font-bold text-sm tracking-tight text-primary leading-none">SIG KAB.BEKASI</span>
                            <span class="text-[10px] text-gray-500 uppercase tracking-wider font-medium">Bencana Kabupaten Bekasi</span>
                        </div>
                    </a>
                    
                    <!-- Nav Center Desktop -->
                    <div class="hidden md:flex items-center space-x-2 mx-auto">
                        <a href="/" class="px-5 py-2 rounded-full text-sm font-medium transition-colors {{ request()->is('/') ? 'bg-primary text-white' : 'text-gray-600 hover:text-gray-900' }}">Beranda</a>
                        <a href="/peta" class="px-5 py-2 rounded-full text-sm font-medium transition-colors {{ request()->is('peta') ? 'bg-primary text-white' : 'text-gray-600 hover:text-gray-900' }}">Peta</a>
                        <a href="/visualisasi" class="px-5 py-2 rounded-full text-sm font-medium transition-colors {{ request()->is('visualisasi') ? 'bg-primary text-white' : 'text-gray-600 hover:text-gray-900' }}">Visualisasi</a>
                        <a href="/laporan" class="px-5 py-2 rounded-full text-sm font-medium transition-colors {{ request()->is('laporan') ? 'bg-primary text-white' : 'text-gray-600 hover:text-gray-900' }}">Laporan</a>
                    </div>

                    <!-- Nav Center Mobile -->
                    <div class="flex md:hidden items-center gap-1 overflow-x-auto flex-1 no-scrollbar">
                        <a href="/" class="px-2.5 py-1.5 rounded-full text-[10px] font-medium whitespace-nowrap {{ request()->is('/') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">Beranda</a>
                        <a href="/peta" class="px-2.5 py-1.5 rounded-full text-[10px] font-medium whitespace-nowrap {{ request()->is('peta') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">Peta</a>
                        <a href="/visualisasi" class="px-2.5 py-1.5 rounded-full text-[10px] font-medium whitespace-nowrap {{ request()->is('visualisasi') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">Visualisasi</a>
                        <a href="/laporan" class="px-2.5 py-1.5 rounded-full text-[10px] font-medium whitespace-nowrap {{ request()->is('laporan') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">Laporan</a>
                    </div>
                    
                    <!-- Login Right -->
                    <div class="flex items-center shrink-0">
                        <a href="/admin" class="text-sm font-medium bg-secondary text-white p-2 sm:px-5 sm:py-2 rounded-full hover:opacity-90 transition-opacity flex items-center gap-2 shadow-sm">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Login Admin</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
            
            <!-- Mobile menu
            <div class="md:hidden flex overflow-x-auto border-t border-gray-100 px-4 py-2 gap-2 bg-brandSurface shadow-inner">
                <a href="/" class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap {{ request()->is('/') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">Beranda</a>
                <a href="/peta" class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap {{ request()->is('peta') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">Peta</a>
                <a href="/visualisasi" class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap {{ request()->is('visualisasi') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">Visualisasi</a>
                <a href="/laporan" class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap {{ request()->is('laporan') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">Laporan</a>
            </div>
        </nav> -->
    @endif

    <!-- Main Content -->
    <main class="{{ $isEmbed ? 'w-full p-0 m-0' : 'flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6' }}">
        @yield('content')
    </main>


    @if (! $isEmbed)
        <!-- Floating Guide Button + Animated Hint -->
        <div class="fixed bottom-6 right-6 z-[9997] flex flex-col items-end gap-3">
            <div
                id="guideHintBubble"
                class="guide-callout pointer-events-none hidden sm:flex items-center gap-2 rounded-2xl bg-brandSurface px-4 py-3 text-sm font-semibold text-primary shadow-xl border border-gray-100"
            >
                <span class="relative flex h-2.5 w-2.5">
                    <span class="guide-pulse-ring absolute inline-flex h-full w-full rounded-full bg-secondary"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-secondary"></span>
                </span>
                <span>Klik di sini untuk panduan penggunaan</span>
                <span class="absolute -bottom-2 right-8 h-4 w-4 rotate-45 bg-brandSurface border-b border-r border-gray-100"></span>
            </div>

            <button
                type="button"
                onclick="openGuideModal()"
                onmouseenter="showGuideHint()"
                onfocus="showGuideHint()"
                class="group relative flex items-center gap-2 rounded-full bg-primary px-4 py-3 text-sm font-bold text-white shadow-xl transition-all hover:-translate-y-1 hover:opacity-95"
            >
                <span class="absolute inset-0 rounded-full bg-primary/30 blur-md opacity-0 transition-opacity group-hover:opacity-100"></span>
                <span class="relative flex h-8 w-8 items-center justify-center rounded-full bg-white/15">
                    <i data-lucide="help-circle" class="h-5 w-5"></i>
                </span>
                <span class="relative hidden sm:inline">Panduan</span>
            </button>
        </div>

        <!-- Guide Modal Overlay -->
        <div
            id="guideModalOverlay"
            class="fixed inset-0 z-[9998] hidden bg-black/40 backdrop-blur-sm"
            onclick="closeGuideModal()"
        ></div>

        <!-- Guide Modal -->
        <div
            id="guideModal"
            class="fixed left-1/2 top-1/2 z-[9999] hidden w-[92%] max-w-xl -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-[28px] bg-brandSurface shadow-2xl"
        >
            <div class="flex items-start justify-between gap-4 border-b border-gray-100 bg-primary px-6 py-5 text-white">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-blue-100">Panduan Pengguna</p>
                    <h2 class="mt-1 text-xl font-extrabold">Tata Cara Penggunaan Website</h2>
                </div>

                <button
                    type="button"
                    onclick="closeGuideModal()"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/10 transition hover:bg-white/20"
                >
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <div class="max-h-[70vh] overflow-y-auto p-6">
                <div class="space-y-4">
                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                        <button
                            type="button"
                            onclick="toggleGuideDetail('guideDetailBeranda', 'guideIconBeranda')"
                            class="flex w-full items-start gap-4 p-4 text-left"
                        >
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">1</div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-bold text-primary">Buka Halaman Beranda</h3>
                                        <p class="mt-1 text-sm leading-6 text-gray-600">Gunakan halaman beranda untuk melihat ringkasan informasi bencana dan akses cepat menuju peta interaktif.</p>
                                    </div>
                                    <i id="guideIconBeranda" data-lucide="chevron-down" class="mt-1 h-5 w-5 shrink-0 text-gray-400 transition-transform"></i>
                                </div>
                            </div>
                        </button>

                        <div id="guideDetailBeranda" class="hidden border-t border-gray-100 bg-gray-50/70 px-4 pb-4 pt-3">
                            <div class="space-y-2 text-sm leading-6 text-gray-600 sm:ml-[52px]">
                                <p><strong class="text-primary">Cara menggunakan:</strong> buka halaman utama melalui menu Beranda untuk melihat gambaran umum data bencana.</p>
                                <ul class="list-disc space-y-1 pl-5">
                                    <li>Lihat ringkasan jumlah data bencana dan kecamatan yang ditampilkan pada kartu informasi.</li>
                                    <li>Perhatikan bagian risiko tertinggi untuk mengetahui wilayah dengan kejadian bencana paling besar.</li>
                                    <li>Tekan tombol <strong>Lihat Peta Interaktif</strong> jika ingin langsung menuju halaman peta.</li>
                                    <li>Gunakan bagian ringkasan peta di beranda untuk melihat gambaran awal persebaran bencana.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                        <button
                            type="button"
                            onclick="toggleGuideDetail('guideDetailPeta', 'guideIconPeta')"
                            class="flex w-full items-start gap-4 p-4 text-left"
                        >
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">2</div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-bold text-primary">Gunakan Menu Peta</h3>
                                        <p class="mt-1 text-sm leading-6 text-gray-600">Pilih menu Peta untuk melihat persebaran bencana. Pengguna dapat memilih kecamatan pada peta, memakai filter jenis bencana, filter tanggal, dan pencarian kecamatan.</p>
                                    </div>
                                    <i id="guideIconPeta" data-lucide="chevron-down" class="mt-1 h-5 w-5 shrink-0 text-gray-400 transition-transform"></i>
                                </div>
                            </div>
                        </button>

                        <div id="guideDetailPeta" class="hidden border-t border-gray-100 bg-gray-50/70 px-4 pb-4 pt-3">
                            <div class="space-y-2 text-sm leading-6 text-gray-600 sm:ml-[52px]">
                                <p><strong class="text-primary">Cara menggunakan:</strong> buka menu Peta untuk melihat sebaran kejadian bencana berdasarkan wilayah kecamatan.</p>
                                <ul class="list-disc space-y-1 pl-5">
                                    <li>Klik salah satu area kecamatan pada peta untuk melihat detail wilayah.</li>
                                    <li>Gunakan filter jenis bencana untuk menampilkan Banjir, Kekeringan, atau Cuaca Ekstrem.</li>
                                    <li>Gunakan filter tanggal untuk membatasi data berdasarkan rentang waktu kejadian.</li>
                                    <li>Gunakan pencarian kecamatan untuk menemukan wilayah tertentu dengan lebih cepat.</li>
                                    <li>Pada tampilan mobile, tekan tombol <strong>Panel Kontrol Peta</strong> untuk membuka filter.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                        <button
                            type="button"
                            onclick="toggleGuideDetail('guideDetailVisualisasi', 'guideIconVisualisasi')"
                            class="flex w-full items-start gap-4 p-4 text-left"
                        >
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">3</div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-bold text-primary">Lihat Visualisasi dan Prediksi</h3>
                                        <p class="mt-1 text-sm leading-6 text-gray-600">Buka menu Visualisasi untuk melihat grafik data bencana, tren kejadian, serta informasi prediksi risiko berdasarkan data historis.</p>
                                    </div>
                                    <i id="guideIconVisualisasi" data-lucide="chevron-down" class="mt-1 h-5 w-5 shrink-0 text-gray-400 transition-transform"></i>
                                </div>
                            </div>
                        </button>

                        <div id="guideDetailVisualisasi" class="hidden border-t border-gray-100 bg-gray-50/70 px-4 pb-4 pt-3">
                            <div class="space-y-2 text-sm leading-6 text-gray-600 sm:ml-[52px]">
                                <p><strong class="text-primary">Cara menggunakan:</strong> buka menu Visualisasi untuk memahami data bencana dalam bentuk grafik dan prediksi.</p>
                                <ul class="list-disc space-y-1 pl-5">
                                    <li>Lihat grafik jenis bencana untuk mengetahui perbandingan jumlah kejadian.</li>
                                    <li>Lihat grafik wilayah untuk mengetahui kecamatan dengan kejadian tertinggi.</li>
                                    <li>Lihat tren tahunan untuk memahami perubahan kejadian bencana dari tahun ke tahun.</li>
                                    <li>Bagian prediksi digunakan untuk melihat perkiraan risiko berdasarkan data historis.</li>
                                    <li>Gunakan filter yang tersedia jika ingin menyesuaikan data yang ditampilkan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                        <button
                            type="button"
                            onclick="toggleGuideDetail('guideDetailLaporan', 'guideIconLaporan')"
                            class="flex w-full items-start gap-4 p-4 text-left"
                        >
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">4</div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-bold text-primary">Cek Halaman Laporan</h3>
                                        <p class="mt-1 text-sm leading-6 text-gray-600">Gunakan menu Laporan untuk melihat daftar kejadian bencana dalam bentuk tabel dan gunakan filter untuk mencari data berdasarkan jenis bencana atau kecamatan.</p>
                                    </div>
                                    <i id="guideIconLaporan" data-lucide="chevron-down" class="mt-1 h-5 w-5 shrink-0 text-gray-400 transition-transform"></i>
                                </div>
                            </div>
                        </button>

                        <div id="guideDetailLaporan" class="hidden border-t border-gray-100 bg-gray-50/70 px-4 pb-4 pt-3">
                            <div class="space-y-2 text-sm leading-6 text-gray-600 sm:ml-[52px]">
                                <p><strong class="text-primary">Cara menggunakan:</strong> buka menu Laporan untuk melihat data kejadian bencana dalam bentuk tabel.</p>
                                <ul class="list-disc space-y-1 pl-5">
                                    <li>Baca tabel laporan untuk melihat tanggal, jenis bencana, wilayah, dan keterangan kejadian.</li>
                                    <li>Gunakan filter jenis bencana untuk menampilkan laporan berdasarkan kategori tertentu.</li>
                                    <li>Gunakan filter kecamatan untuk melihat laporan pada wilayah tertentu.</li>
                                    <li>Gunakan tombol export jika ingin menyimpan laporan dalam format yang tersedia.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl bg-orange-50 px-4 py-3 text-sm leading-6 text-orange-800">
                    <strong>Catatan:</strong> Tombol Login Admin hanya digunakan oleh administrator untuk mengelola data bencana pada sistem.
                </div>
            </div>
        </div>
    @endif


    @if (! $isEmbed)
        <!-- Footer -->
        <footer class="bg-brandSurface border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Sistem Informasi Geografis Bencana Kabupaten Bekasi. All rights reserved.
            </div>
        </footer>
    @endif

    <script>
        lucide.createIcons();

        function showGuideHint() {
            const hint = document.getElementById('guideHintBubble');
            if (!hint) return;
            hint.classList.remove('hidden');
            hint.classList.add('sm:flex');
        }

        function hideGuideHint() {
            const hint = document.getElementById('guideHintBubble');
            if (!hint) return;
            hint.classList.add('hidden');
            hint.classList.remove('sm:flex');
        }

        function toggleGuideDetail(detailId, iconId) {
            const detail = document.getElementById(detailId);
            const icon = document.getElementById(iconId);

            if (!detail) return;

            detail.classList.toggle('hidden');

            if (icon) {
                icon.classList.toggle('rotate-180');
            }
        }

        setTimeout(showGuideHint, 900);

        function openGuideModal() {
            const modal = document.getElementById('guideModal');
            const overlay = document.getElementById('guideModalOverlay');

            if (!modal || !overlay) return;

            modal.classList.remove('hidden');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            hideGuideHint();

            lucide.createIcons();
        }

        function closeGuideModal() {
            const modal = document.getElementById('guideModal');
            const overlay = document.getElementById('guideModalOverlay');

            if (!modal || !overlay) return;

            modal.classList.add('hidden');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeGuideModal();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>