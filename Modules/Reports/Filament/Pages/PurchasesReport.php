<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Pages;

use BackedEnum;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Modules\Purchases\Enums\PurchaseOrderStatus;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Reports\Filament\Exports\PurchasesExporter;
use UnitEnum;

class PurchasesReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static UnitEnum|string|null $navigationGroup = 'Relatórios';

    protected static ?string $navigationLabel = 'Compras';

    protected static ?string $title = 'Relatório de Compras';

    public static function canAccess(): bool
    {
        return Auth::user()?->can('reports.purchases.view') ?? false;
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedTable::make(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(PurchaseOrder::query()->with(['supplier', 'location', 'items']))
            ->columns([
                TextColumn::make('id')
                    ->label('Nº')
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label('Fornecedor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order_date')
                    ->label('Data do Pedido')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PurchaseOrderStatus $state): string => match ($state) {
                        PurchaseOrderStatus::Draft => 'gray',
                        PurchaseOrderStatus::Sent => 'warning',
                        PurchaseOrderStatus::Received => 'success',
                        PurchaseOrderStatus::Cancelled => 'danger',
                    })
                    ->formatStateUsing(fn (PurchaseOrderStatus $state): string => $state->label()),
                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn (PurchaseOrder $record): float => $record->total)
                    ->money('BRL'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(PurchaseOrderStatus::cases())->mapWithKeys(
                        fn (PurchaseOrderStatus $case): array => [$case->value => $case->label()],
                    )),
                SelectFilter::make('supplier_id')
                    ->label('Fornecedor')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('order_date')
                    ->schema([
                        DatePicker::make('from')->label('De'),
                        DatePicker::make('until')->label('Até'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, string $date): Builder => $q->whereDate('order_date', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, string $date): Builder => $q->whereDate('order_date', '<=', $date))),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar')
                    ->exporter(PurchasesExporter::class),
            ])
            ->defaultSort('order_date', 'desc');
    }
}
