<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                CheckboxList::make('permissions')
                    ->label('Permissões')
                    ->relationship('permissions', 'name')
                    ->columns(3)
                    ->bulkToggleable(),
            ]);
    }
}
