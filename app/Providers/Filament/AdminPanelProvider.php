<?php

namespace App\Providers\Filament;

use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Administration\Filament\Pages\EditProfile;
use Modules\Administration\Filament\Resources\AuditLogs\AuditLogResource;
use Modules\Administration\Filament\Resources\Roles\RoleResource;
use Modules\Administration\Filament\Resources\Users\UserResource;
use Modules\Customers\Filament\Resources\Customers\CustomerResource;
use Modules\Inventory\Filament\Resources\StockBalances\StockBalanceResource;
use Modules\Inventory\Filament\Resources\StockLocations\StockLocationResource;
use Modules\Inventory\Filament\Resources\StockMovements\StockMovementResource;
use Modules\Inventory\Filament\Resources\StockReservations\StockReservationResource;
use Modules\Products\Filament\Resources\Brands\BrandResource;
use Modules\Products\Filament\Resources\Categories\ProductCategoryResource;
use Modules\Products\Filament\Resources\Collections\ProductCollectionResource;
use Modules\Products\Filament\Resources\Products\ProductResource;
use Modules\Purchases\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use Modules\Purchases\Filament\Resources\Suppliers\SupplierResource;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile(EditProfile::class)
            ->multiFactorAuthentication([
                AppAuthentication::make()->recoverable(),
                EmailAuthentication::make(),
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->resources([
                UserResource::class,
                RoleResource::class,
                AuditLogResource::class,
                CustomerResource::class,
                ProductCategoryResource::class,
                BrandResource::class,
                ProductCollectionResource::class,
                ProductResource::class,
                StockLocationResource::class,
                StockMovementResource::class,
                StockBalanceResource::class,
                StockReservationResource::class,
                SupplierResource::class,
                PurchaseOrderResource::class,
            ])
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ]);
    }
}
