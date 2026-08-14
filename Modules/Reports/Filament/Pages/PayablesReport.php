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
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Models\Payable;
use Modules\Reports\Filament\Exports\PayablesExporter;
use UnitEnum;

class PayablesReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static UnitEnum|string|null $navigationGroup = 'Relatórios';

    protected static ?string $navigationLabel = 'Contas a Pagar';

    protected static ?string $title = 'Relatório de Contas a Pagar';

    public static function canAccess(): bool
    {
        return Auth::user()?->can('reports.payables.view') ?? false;
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
            ->query(Payable::query()->with(['supplier', 'purchaseOrder']))
            ->columns([
                TextColumn::make('supplier.name')
                    ->label('Fornecedor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purchase_order_id')
                    ->label('Pedido de Compra')
                    ->placeholder('—'),
                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PayableStatus $state): string => match ($state) {
                        PayableStatus::Pending => 'warning',
                        PayableStatus::Paid => 'success',
                        PayableStatus::Cancelled => 'danger',
                    })
                    ->formatStateUsing(fn (PayableStatus $state): string => $state->label()),
                TextColumn::make('paid_at')
                    ->label('Pago em')
                    ->dateTime('d/m/Y')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(PayableStatus::cases())->mapWithKeys(
                        fn (PayableStatus $case): array => [$case->value => $case->label()],
                    )),
                SelectFilter::make('supplier_id')
                    ->label('Fornecedor')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('due_date')
                    ->schema([
                        DatePicker::make('from')->label('De'),
                        DatePicker::make('until')->label('Até'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, string $date): Builder => $q->whereDate('due_date', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, string $date): Builder => $q->whereDate('due_date', '<=', $date))),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar')
                    ->exporter(PayablesExporter::class),
            ])
            ->defaultSort('due_date');
    }
}
