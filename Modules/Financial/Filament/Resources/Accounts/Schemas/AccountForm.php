<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Financial\Enums\AccountType;
use Modules\Financial\Models\Account;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Conta Contábil')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                        Select::make('type')
                            ->label('Tipo')
                            ->options(collect(AccountType::cases())->mapWithKeys(
                                fn (AccountType $case): array => [$case->value => $case->label()],
                            ))
                            ->required(),
                        Select::make('parent_id')
                            ->label('Conta Pai')
                            ->searchable()
                            ->preload()
                            ->options(fn (?Account $record): array => Account::query()
                                ->when($record, fn ($query) => $query->whereNotIn(
                                    'id',
                                    [$record->id, ...$record->descendantIds()],
                                ))
                                ->pluck('name', 'id')
                                ->all()),
                        Toggle::make('is_active')
                            ->label('Ativa')
                            ->default(true),
                    ]),
            ]);
    }
}
