<x-filament-panels::page.simple>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .fi-simple-layout {
            background: #F2EFEB !important;
        }

        .fi-simple-main-ctn {
            align-items: stretch !important;
            justify-content: stretch !important;
            min-height: 100vh !important;
        }

        .fi-simple-main {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            --tw-ring-color: transparent !important;
        }

        .fi-simple-header,
        .fi-simple-header-heading {
            display: none !important;
        }

        .sig-admin-login,
        .sig-admin-login * {
            font-family: 'Poppins', sans-serif !important;
        }

        .sig-admin-login {
            min-height: 100vh;
            background: #F2EFEB;
            color: #12395C;
        }

        .sig-admin-login__topbar {
            height: 72px;
            position: relative;
            display: flex;
            align-items: center;
            padding: 0 32px;
            background: #FFFEFC;
            border-bottom: 1px solid rgba(18, 57, 92, 0.08);
            width: 100%;
        }

        .sig-admin-login__brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }

        .sig-admin-login__brand-title {
            display: block;
            color: #12395C;
            font-size: 14px;
            line-height: 1;
            font-weight: 800;
        }

        .sig-admin-login__brand-subtitle {
            display: block;
            margin-top: 4px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .08em;
            color: #64748b;
            text-transform: uppercase;
        }

        .sig-admin-login__page-label {
            position: absolute;
            right: 32px;
            top: 50%;
            transform: translateY(-50%);
            color: #12395C;
            font-size: 14px;
            font-weight: 700;
            text-align: right;
        }

        .sig-admin-login__content {
            min-height: calc(100vh - 72px);
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .sig-admin-login__info {
            display: flex;
            align-items: center;
            padding: 64px clamp(32px, 6vw, 96px);
            background:
                radial-gradient(circle at top left, rgba(212, 91, 31, 0.34), transparent 34%),
                linear-gradient(135deg, #12395C 0%, #16466f 55%, #12395C 100%);
            color: #ffffff;
        }

        .sig-admin-login__info-inner {
            width: 100%;
            max-width: 560px;
        }

        .sig-admin-login__eyebrow {
            display: inline-flex;
            padding: 9px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sig-admin-login__info h1 {
            margin-top: 28px;
            font-size: clamp(36px, 4vw, 56px);
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -0.045em;
        }

        .sig-admin-login__info p {
            margin-top: 18px;
            max-width: 480px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 16px;
            line-height: 1.75;
        }

        .sig-admin-login__features {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-top: 36px;
            max-width: 560px;
        }

        .sig-admin-login__feature {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 18px 20px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.13);
            border: 1px solid rgba(255, 255, 255, 0.16);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(12px);
        }

        .sig-admin-login__feature-number {
            width: 48px;
            height: 48px;
            border-radius: 18px;
            background: #D45B1F;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
            flex-shrink: 0;
            box-shadow: 0 14px 28px rgba(212, 91, 31, 0.24);
        }

        .sig-admin-login__feature h3 {
            margin: 0 0 5px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.3;
        }

        .sig-admin-login__feature span {
            display: block;
            color: rgba(255, 255, 255, 0.76);
            font-size: 12px;
            line-height: 1.6;
        }

        .sig-admin-login__form-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 64px 32px;
            background:
                radial-gradient(circle at bottom right, rgba(18, 57, 92, 0.06), transparent 34%),
                #FFFEFC;
        }

        .sig-admin-login__form-area {
            width: 100%;
            max-width: 430px;
        }

        .sig-admin-login__form-area h2 {
            font-size: 34px;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #12395C;
        }

        .sig-admin-login__form-area > p {
            margin-top: 10px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
        }

        .sig-admin-login__card {
            margin-top: 28px;
            padding: 28px;
            border-radius: 30px;
            background: #F2EFEB;
            border: 1px solid rgba(18, 57, 92, 0.08);
            box-shadow: 0 24px 60px rgba(18, 57, 92, 0.10);
        }

        .sig-admin-login__card .fi-input-wrp {
            border-radius: 999px !important;
            background: #ffffff !important;
            box-shadow: none !important;
            border: 1px solid rgba(18, 57, 92, 0.10) !important;
            overflow: hidden;
        }

        .sig-admin-login__card .fi-btn {
            width: 100% !important;
            min-height: 48px !important;
            margin-top: 18px !important;
            border-radius: 999px !important;
            background: #D45B1F !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            letter-spacing: 0.02em;
            box-shadow: 0 16px 30px rgba(212, 91, 31, 0.24) !important;
        }

        .sig-admin-login__back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 46px;
            margin-top: 14px;
            border-radius: 999px;
            background: transparent;
            color: #12395C !important;
            border: 1px solid rgba(18, 57, 92, 0.18);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none !important;
            transition: 0.2s ease;
        }

        .sig-admin-login__back-btn:hover {
            background: rgba(18, 57, 92, 0.06);
            transform: translateY(-1px);
        }

        @media (max-width: 900px) {
            .sig-admin-login__content {
                grid-template-columns: 1fr;
            }

            .sig-admin-login__topbar {
                height: 72px;
                padding: 0 24px;
            }

            .sig-admin-login__page-label {
                right: 24px;
                font-size: 13px;
            }

            .sig-admin-login__info {
                align-items: flex-start;
                padding: 40px 24px 36px;
            }

            .sig-admin-login__info h1 {
                margin-top: 24px;
                font-size: 30px;
                line-height: 1.14;
                letter-spacing: -0.04em;
            }

            .sig-admin-login__info p {
                font-size: 13px;
                line-height: 1.75;
                max-width: 520px;
            }

            .sig-admin-login__features {
                margin-top: 28px;
                gap: 14px;
            }

            .sig-admin-login__feature {
                padding: 16px;
                border-radius: 20px;
            }

            .sig-admin-login__feature-number {
                width: 42px;
                height: 42px;
                border-radius: 15px;
                font-size: 16px;
            }

            .sig-admin-login__feature h3 {
                font-size: 13px;
            }

            .sig-admin-login__feature span {
                font-size: 11px;
            }

            .sig-admin-login__form-wrap {
                padding: 40px 20px 56px;
            }

            .sig-admin-login__form-area {
                max-width: 430px;
            }

            .sig-admin-login__form-area h2 {
                font-size: 30px;
                text-align: center;
            }

            .sig-admin-login__form-area > p {
                text-align: center;
                font-size: 13px;
            }

            .sig-admin-login__card {
                padding: 24px;
                border-radius: 26px;
            }
        }

        @media (max-width: 480px) {
            .sig-admin-login__topbar {
                padding: 0 18px;
            }

            .sig-admin-login__brand-subtitle {
                font-size: 9px;
            }

            .sig-admin-login__page-label {
                right: 18px;
                font-size: 12px;
            }

            .sig-admin-login__info {
                padding: 34px 20px;
            }

            .sig-admin-login__info h1 {
                font-size: 27px;
            }

            .sig-admin-login__form-wrap {
                padding: 34px 16px 48px;
            }
        }

        /* =========================
        FIX LOGIN DARK MODE
        Login tetap pakai tema terang agar tulisan terbaca
        ========================= */

        .dark .sig-admin-login {
            background: #F2EFEB !important;
            color: #12395C !important;
        }

        .dark .sig-admin-login__form-wrap {
            background:
                radial-gradient(circle at bottom right, rgba(18, 57, 92, 0.06), transparent 34%),
                #FFFEFC !important;
        }

        .dark .sig-admin-login__form-area h2 {
            color: #12395C !important;
        }

        .dark .sig-admin-login__form-area > p {
            color: #64748b !important;
        }

        .dark .sig-admin-login__card {
            background: #F2EFEB !important;
            border-color: rgba(18, 57, 92, 0.08) !important;
        }

        /* label login tetap gelap walau dark mode */
        .dark .sig-admin-login__card label,
        .dark .sig-admin-login__card .fi-fo-field-wrp-label,
        .dark .sig-admin-login__card .fi-fo-field-wrp-label span,
        .dark .sig-admin-login__card .fi-fo-field-wrp-label label {
            color: #12395C !important;
            background: transparent !important;
        }

        /* input login tetap putih */
        .dark .sig-admin-login__card .fi-input-wrp {
            background: #ffffff !important;
            border: 1px solid rgba(18, 57, 92, 0.10) !important;
        }

        .dark .sig-admin-login__card input {
            color: #12395C !important;
            background: #ffffff !important;
        }

        /* remember me tetap kelihatan */
        .dark .sig-admin-login__card .fi-checkbox-input {
            background-color: #ffffff !important;
            border-color: rgba(18, 57, 92, 0.25) !important;
        }

        .dark .sig-admin-login__card .fi-checkbox-list-option-label,
        .dark .sig-admin-login__card .fi-checkbox-label,
        .dark .sig-admin-login__card span {
            color: #12395C !important;
        }

        /* tombol kembali tetap terbaca */
        .dark .sig-admin-login__back-btn {
            color: #12395C !important;
            border-color: rgba(18, 57, 92, 0.18) !important;
        }
    </style>

    <div class="sig-admin-login">
        <header class="sig-admin-login__topbar">
            <div class="sig-admin-login__brand">
                <div>
                    <span class="sig-admin-login__brand-title">SIG KAB. BEKASI</span>
                    <span class="sig-admin-login__brand-subtitle">
                        Bencana Kabupaten Bekasi
                    </span>
                </div>
            </div>

            <div class="sig-admin-login__page-label">
                Page Admin
            </div>
        </header>

        <main class="sig-admin-login__content">
            <section class="sig-admin-login__info">
                <div class="sig-admin-login__info-inner">
                    <div class="sig-admin-login__eyebrow">Panel Administrator</div>

                    <h1>Kelola Data Bencana Kabupaten Bekasi</h1>

                    <p>
                        Halaman admin digunakan untuk mengelola data kecamatan, desa, jenis bencana,
                        serta laporan kejadian bencana yang ditampilkan pada sistem informasi geografis.
                    </p>

                    <div class="sig-admin-login__features">
                        <div class="sig-admin-login__feature">
                            <div class="sig-admin-login__feature-number">01</div>
                            <div>
                                <h3>Kelola Data Bencana</h3>
                                <span>Mengelola data kejadian bencana yang tersimpan di dalam sistem.</span>
                            </div>
                        </div>

                        <div class="sig-admin-login__feature">
                            <div class="sig-admin-login__feature-number">02</div>
                            <div>
                                <h3>Perbarui Data Referensi</h3>
                                <span>Memperbarui data kecamatan, desa, dan jenis bencana.</span>
                            </div>
                        </div>

                        <div class="sig-admin-login__feature">
                            <div class="sig-admin-login__feature-number">03</div>
                            <div>
                                <h3>Pantau Statistik Sistem</h3>
                                <span>Melihat ringkasan data melalui dashboard admin.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sig-admin-login__form-wrap">
                <div class="sig-admin-login__form-area">
                    <h2>Masuk Admin</h2>
                    <p>Gunakan akun administrator untuk mengakses panel pengelolaan data.</p>

                    <div class="sig-admin-login__card">
                        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

                        <x-filament-panels::form id="form" wire:submit="authenticate">
                            {{ $this->form }}

                            <x-filament-panels::form.actions
                                :actions="$this->getCachedFormActions()"
                                :full-width="$this->hasFullWidthFormActions()"
                            />
                        </x-filament-panels::form>

                        <a href="{{ url('/') }}" class="sig-admin-login__back-btn">
                            ← Kembali ke Beranda
                        </a>

                        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
                    </div>
                </div>
            </section>
        </main>
    </div>
</x-filament-panels::page.simple>