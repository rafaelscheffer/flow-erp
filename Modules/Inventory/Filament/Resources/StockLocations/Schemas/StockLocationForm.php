<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockLocations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StockLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descrição')
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Ativo')
                    ->default(true),
            ]);
    }
}
