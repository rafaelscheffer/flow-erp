<?php

declare(strict_types=1);

namespace Modules\Products\Filament\Imports;

use App\Filament\Imports\BaseImporter;
use Filament\Actions\Imports\ImportColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Modules\Products\Enums\ProductType;
use Modules\Products\Models\Product;

class ProductImporter extends BaseImporter
{
    protected static ?string $model = Product::class;

    /**
     * @return array<ImportColumn>
     */
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('category')
                ->label('Categoria')
                ->relationship(resolveUsing: ['name']),
            ImportColumn::make('brand')
                ->label('Marca')
                ->relationship(resolveUsing: ['name']),
            ImportColumn::make('collection')
                ->label('Coleção')
                ->relationship(resolveUsing: ['name']),
            ImportColumn::make('type')
                ->label('Tipo')
                ->requiredMapping()
                ->castStateUsing(fn (?string $state): ?string => match (mb_strtolower((string) $state)) {
                    'simple', 'simples' => ProductType::Simple->value,
                    'variable', 'variável', 'variavel' => ProductType::Variable->value,
                    default => $state,
                })
                ->rules(['required', Rule::in([ProductType::Simple->value, ProductType::Variable->value])]),
            ImportColumn::make('name')
                ->label('Nome')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('description')
                ->label('Descrição')
                ->rules(['nullable', 'string']),
            ImportColumn::make('internal_code')
                ->label('Código interno')
                ->rules(fn (?Model $record): array => [
                    'nullable', 'string', 'max:255', Rule::unique('products', 'internal_code')->ignore($record),
                ]),
            ImportColumn::make('sku')
                ->label('SKU')
                ->rules(fn (?Model $record): array => [
                    'nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($record),
                ]),
            ImportColumn::make('ean')
                ->label('EAN')
                ->rules(fn (?Model $record): array => [
                    'nullable', 'string', 'max:14', Rule::unique('products', 'ean')->ignore($record),
                ]),
            ImportColumn::make('ncm')
                ->label('NCM')
                ->rules(['nullable', 'string', 'max:8']),
            ImportColumn::make('weight')
                ->label('Peso')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('height')
                ->label('Altura')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('width')
                ->label('Largura')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('length')
                ->label('Comprimento')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('cost_price')
                ->label('Preço de custo')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('sale_price')
                ->label('Preço de venda')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('promotional_price')
                ->label('Preço promocional')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('min_stock')
                ->label('Estoque mínimo')
                ->integer()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('max_stock')
                ->label('Estoque máximo')
                ->integer()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('is_active')
                ->label('Ativo')
                ->boolean()
                ->rules(['nullable', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Model
    {
        $sku = $this->data['sku'] ?? null;

        if (blank($sku)) {
            return new Product;
        }

        return Product::query()->firstOrNew(['sku' => $sku]);
    }
}
