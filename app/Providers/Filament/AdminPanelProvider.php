<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\LaporanBencanaResource;
use App\Filament\Resources\DesaResource;
use App\Filament\Resources\KecamatanResource;
use App\Filament\Resources\JenisBencanaResource;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => \Filament\Support\Colors\Color::hex('#D45B1F'), // Orange as active
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make('MENU UTAMA')
                        ->items([
                            NavigationItem::make('Dashboard')
                                ->icon('heroicon-o-squares-2x2')
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                                ->url(fn (): string => Pages\Dashboard::getUrl()),
                            NavigationItem::make('Peta')
                                ->icon('heroicon-o-map')
                                ->url(url('/peta'))
                                ->openUrlInNewTab(),
                        ]),
                    NavigationGroup::make('MANAJEMEN DATA')
                        ->items([
                            NavigationItem::make('Data Bencana')
                                ->icon('heroicon-o-circle-stack')
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.laporan-bencanas.index'))
                                ->url(fn (): string => LaporanBencanaResource::getUrl('index')),
                            NavigationItem::make('Tambah/Edit Bencana')
                                ->icon('heroicon-o-document-text')
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.laporan-bencanas.create') || request()->routeIs('filament.admin.resources.laporan-bencanas.edit'))
                                ->url(fn (): string => LaporanBencanaResource::getUrl('create')),
                        ]),
                    NavigationGroup::make('ANALISIS & LAPORAN')
                        ->items([
                            NavigationItem::make('Statistik & Analisis')
                                ->icon('heroicon-o-chart-bar')
                                ->url(url('/visualisasi'))
                                ->openUrlInNewTab(),
                            NavigationItem::make('Export Data')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->url(url('/laporan'))
                                ->openUrlInNewTab(),
                        ]),
                    NavigationGroup::make('REFERENSI')
                        ->items([
                            ...DesaResource::getNavigationItems(),
                            ...KecamatanResource::getNavigationItems(),
                            ...JenisBencanaResource::getNavigationItems(),
                        ]),
                ]);
            });
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => Blade::render('<style>
                /* Sidebar Background */
                aside.fi-sidebar { 
                    background-color: #0d2238 !important; 
                }
                
                /* Text and Icons default color */
                aside.fi-sidebar .fi-sidebar-item-label, 
                aside.fi-sidebar .fi-icon { 
                    color: #e2e8f0 !important; 
                }
                
                /* Group Headers */
                aside.fi-sidebar .fi-sidebar-group-label { 
                    color: #94a3b8 !important; 
                    font-weight: 700; 
                    letter-spacing: 0.05em; 
                }
                
                /* Active Item Styling */
                aside.fi-sidebar .fi-sidebar-item-active > a { 
                    background-color: #d97706 !important; 
                    border-radius: 0 9999px 9999px 0 !important; 
                    margin-left: -1rem !important; 
                    padding-left: 1rem !important; 
                }
                
                /* Active Item Text/Icon Color */
                aside.fi-sidebar .fi-sidebar-item-active .fi-sidebar-item-label,
                aside.fi-sidebar .fi-sidebar-item-active .fi-icon { 
                    color: #ffffff !important; 
                    font-weight: 600; 
                }
                
                /* Topbar branding background */
                .fi-topbar {
                    border-bottom: 1px solid #1e293b !important;
                }
            </style>'),
        );
    }
}
