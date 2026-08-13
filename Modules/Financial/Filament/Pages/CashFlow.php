<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Modules\Financial\Filament\Widgets\CashFlowChart;
use Modules\Financial\Filament\Widgets\CashFlowOverview;
use UnitEnum;

class CashFlow extends Page
{
    use HasFiltersForm;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Fluxo de Caixa';

    protected static ?string $title = 'Fluxo de Caixa';

    public static function canAccess(): bool
    {
        return Auth::user()?->can('cash-flow.view') ?? false;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('start_date')
                    ->label('Início')
                    ->default(now()->startOfMonth()),
                DatePicker::make('end_date')
                    ->label('Fim')
                    ->default(now()->endOfMonth()),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CashFlowOverview::class,
            CashFlowChart::class,
        ];
    }
}
