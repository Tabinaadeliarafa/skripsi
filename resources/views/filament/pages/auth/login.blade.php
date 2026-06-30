<x-filament-panels::page.simple>
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

        .sig-admin-login {
            min-height: 100vh;
            background: #F2EFEB;
            color: #12395C;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .fi-simple-header {
            display: none !important;
        }

        .fi-simple-header-heading {
            display: none !important;
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

        .sig-admin-login__topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
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

        .sig-admin-login__page-label {
            color: #12395C;
            font-size: 14px;
            font-weight: 700;
        }

        .sig-admin-login__content {
            min-height: calc(100vh - 64px);
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .sig-admin-login__info {
            display: flex;
            align-items: center;
            padding: 56px clamp(32px, 6vw, 96px);
            background:
                linear-gradient(135deg, rgba(18, 57, 92, 0.94), rgba(18, 57, 92, 0.78)),
                radial-gradient(circle at top left, rgba(212, 91, 31, 0.36), transparent 34%),
                #12395C;
            color: #ffffff;
        }

        .sig-admin-login__info-inner {
            max-width: 520px;
        }

        .sig-admin-login__eyebrow {
            display: inline-flex;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sig-admin-login__info h1 {
            margin-top: 28px;
            font-size: clamp(34px, 4vw, 56px);
            line-height: 1.05;
            font-weight: 850;
            letter-spacing: -0.055em;
        }

        .sig-admin-login__info p {
            margin-top: 18px;
            max-width: 460px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 16px;
            line-height: 1.7;
        }

        .sig-admin-login__features {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 34px;
        }

        .sig-admin-login__feature {
            min-height: 90px;
            padding: 16px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .sig-admin-login__feature strong {
            display: block;
            font-size: 22px;
            line-height: 1;
        }

        .sig-admin-login__feature span {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            line-height: 1.35;
            color: rgba(255, 255, 255, 0.72);
        }

        .sig-admin-login__form-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 56px 32px;
            background: #FFFEFC;
        }

        .sig-admin-login__form-area {
            width: 100%;
            max-width: 430px;
        }

        .sig-admin-login__form-area h2 {
            font-size: 32px;
            line-height: 1.15;
            font-weight: 850;
            letter-spacing: -0.04em;
            color: #12395C;
        }

        .sig-admin-login__form-area > p {
            margin-top: 10px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .sig-admin-login__card {
            margin-top: 28px;
            padding: 28px;
            border-radius: 28px;
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

        .sig-admin-login__brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
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

        @media (max-width: 900px) {
            .sig-admin-login__content {
                grid-template-columns: 1fr;
            }

            .sig-admin-login__info {
                padding: 40px 24px;
            }

            .sig-admin-login__features {
                grid-template-columns: 1fr;
            }

            .sig-admin-login__form-wrap {
                padding: 40px 20px 56px;
            }
        }
    </style>

    <div class="sig-admin-login">
        <header class="sig-admin-login__topbar">
            <div class="sig-admin-login__brand">
                <div>
                    <span>SIG KAB. BEKASI</span>
                    <span style="display:block;font-size:10px;font-weight:600;letter-spacing:.08em;color:#64748b;text-transform:uppercase;">
                        Bencana Kabupaten Bekasi
                    </span>
                </div>
                <div class="sig-admin-login__page-label">
                    Page Admin
                </div>
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
                            <strong>01</strong>
                            <span>Kelola data kejadian bencana</span>
                        </div>
                        <div class="sig-admin-login__feature">
                            <strong>02</strong>
                            <span>Perbarui data wilayah dan referensi</span>
                        </div>
                        <div class="sig-admin-login__feature">
                            <strong>03</strong>
                            <span>Pantau statistik melalui dashboard</span>
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