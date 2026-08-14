<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Pages;

use BackedEnum;
use Filament\Actions\ExportAction;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Modules\Customers\Enums\CustomerType;
use Modules\Customers\Models\Customer;
use Modules\Reports\Filament\Exports\CustomersExporter;
use UnitEnum;

class CustomersReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static UnitEnum|string|null $navigationGroup = 'Relatórios';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $title = 'Relatório de Clientes';

    public static function canAccess(): bool
    {
        return Auth::user()?->can('reports.customers.view') ?? false;
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
            ->query(Customer::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (CustomerType $state): string => $state->label()),
                TextColumn::make('document')
                    ->label('Documento')
                    ->searchable(),
                TextColumn::make('city')
                    ->label('Cidade')
                    ->sortable(),
                TextColumn::make('state')
                    ->label('Estado')
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefone'),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(collect(CustomerType::cases())->mapWithKeys(
                        fn (CustomerType $case): array => [$case->value => $case->label()],
                    )),
                TernaryFilter::make('is_active')
                    ->label('Ativo'),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar')
                    ->exporter(CustomersExporter::class),
            ])
            ->defaultSort('name');
    }
}
