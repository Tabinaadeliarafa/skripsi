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
        <!-- Footer -->
        <footer class="bg-brandSurface border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Sistem Informasi Geografis Bencana Kabupaten Bekasi. All rights reserved.
            </div>
        </footer>
    @endif

    <script>
        lucide.createIcons();
    </script>

    @stack('scripts')
</body>
</html>