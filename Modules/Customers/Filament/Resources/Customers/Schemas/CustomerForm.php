<?php

declare(strict_types=1);

namespace Modules\Customers\Filament\Resources\Customers\Schemas;

use App\Rules\ValidCnpj;
use App\Rules\ValidCpf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Customers\Enums\CustomerType;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificação')
                    ->columns(2)
                    ->schema([
                        Radio::make('type')
                            ->label('Tipo')
                            ->options(collect(CustomerType::cases())->mapWithKeys(
                                fn (CustomerType $case): array => [$case->value => $case->label()],
                            ))
                            ->default(CustomerType::Individual->value)
                            ->live()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->label(fn (Get $get): string => $get('type') === CustomerType::Company->value ? 'Razão Social' : 'Nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('trade_name')
                            ->label('Nome Fantasia')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => $get('type') === CustomerType::Company->value),
                        TextInput::make('document')
                            ->label(fn (Get $get): string => $get('type') === CustomerType::Company->value ? 'CNPJ' : 'CPF')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rules(fn (Get $get): array => [
                                $get('type') === CustomerType::Company->value ? new ValidCnpj : new ValidCpf,
                            ])
                            ->maxLength(14),
                        TextInput::make('state_registration')
                            ->label('Inscrição Estadual')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => $get('type') === CustomerType::Company->value),
                        DatePicker::make('birth_date')
                            ->label('Data de Nascimento')
                            ->visible(fn (Get $get): bool => $get('type') === CustomerType::Individual->value),
                        Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                    ]),
                Section::make('Contato')
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20),
                    ]),
                Section::make('Endereço')
                    ->columns(3)
                    ->schema([
                        TextInput::make('zip_code')
                            ->label('CEP')
                            ->maxLength(8),
                        TextInput::make('address')
                            ->label('Logradouro')
                            ->maxLength(255)
                            ->columnSpan(2),
                        TextInput::make('address_number')
                            ->label('Número')
                            ->maxLength(20),
                        TextInput::make('address_complement')
                            ->label('Complemento')
                            ->maxLength(255),
                        TextInput::make('neighborhood')
                            ->label('Bairro')
                            ->maxLength(255),
                        TextInput::make('city')
                            ->label('Cidade')
                            ->maxLength(255),
                        TextInput::make('state')
                            ->label('UF')
                            ->maxLength(2),
                    ]),
                Section::make('Observações')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
