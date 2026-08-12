<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockMovements\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Modules\Inventory\Actions\RegisterStockTransferAction;
use Modules\Inventory\Filament\Resources\StockMovements\StockMovementResource;
use Modules\Inventory\Models\StockLocation;
use Modules\Products\Enums\ProductType;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductVariant;

class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->transferAction(),
            CreateAction::make(),
        ];
    }

    private function transferAction(): Action
    {
        return Action::make('transfer')
            ->label('Transferir Estoque')
            ->icon(Heroicon::OutlinedArrowsRightLeft)
            ->color('gray')
            ->authorize(fn (): bool => (bool) auth()->user()?->can('movements.create'))
            ->modalHeading('Transferir Estoque entre Locais')
            ->schema([
                Select::make('product_id')
                    ->label('Produto')
                    ->options(Product::query()->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->required()
                    ->afterStateUpdated(fn (callable $set) => $set('product_variant_id', null)),
                Select::make('product_variant_id')
                    ->label('Variante')
                    ->options(
                        fn (Get $get): array => $get('product_id')
                            ? ProductVariant::query()->where('product_id', $get('product_id'))->pluck('sku', 'id')->toArray()
                            : [],
                    )
                    ->searchable()
                    ->visible(
                        fn (Get $get): bool => Product::query()->find($get('product_id'))?->type === ProductType::Variable,
                    ),
                Select::make('from_location_id')
                    ->label('Local de Origem')
                    ->options(StockLocation::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('to_location_id')
                    ->label('Local de Destino')
                    ->options(StockLocation::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->different('from_location_id'),
                TextInput::make('quantity')
                    ->label('Quantidade')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->required(),
                Textarea::make('notes')
                    ->label('Observações')
                    ->rows(3),
            ])
            ->action(function (array $data): void {
                app(RegisterStockTransferAction::class)->execute(
                    productId: (int) $data['product_id'],
                    productVariantId: isset($data['product_variant_id']) ? (int) $data['product_variant_id'] : null,
                    fromLocationId: (int) $data['from_location_id'],
                    toLocationId: (int) $data['to_location_id'],
                    quantity: (int) $data['quantity'],
                    performedBy: (int) auth()->id(),
                    notes: $data['notes'] ?? null,
                );
            })
            ->successNotificationTitle('Transferência registrada com sucesso.');
    }
}
