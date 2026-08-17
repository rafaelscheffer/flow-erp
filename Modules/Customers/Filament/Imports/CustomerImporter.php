<?php

declare(strict_types=1);

namespace Modules\Customers\Filament\Imports;

use App\Filament\Imports\BaseImporter;
use App\Rules\ValidCnpj;
use App\Rules\ValidCpf;
use Filament\Actions\Imports\ImportColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Modules\Customers\Enums\CustomerType;
use Modules\Customers\Models\Customer;

class CustomerImporter extends BaseImporter
{
    protected static ?string $model = Customer::class;

    /**
     * @return array<ImportColumn>
     */
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('type')
                ->label('Tipo')
                ->requiredMapping()
                ->castStateUsing(fn (?string $state): ?string => match (mb_strtolower((string) $state)) {
                    'individual', 'pf', 'pessoa física', 'pessoa fisica' => CustomerType::Individual->value,
                    'company', 'pj', 'pessoa jurídica', 'pessoa juridica' => CustomerType::Company->value,
                    default => $state,
                })
                ->rules(['required', Rule::in([CustomerType::Individual->value, CustomerType::Company->value])]),
            ImportColumn::make('name')
                ->label('Nome')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('trade_name')
                ->label('Nome fantasia')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('document')
                ->label('CPF/CNPJ')
                ->requiredMapping()
                ->rules(fn (array $data, ?Model $record): array => [
                    'required',
                    'string',
                    'max:14',
                    Rule::unique('customers', 'document')->ignore($record),
                    ($data['type'] ?? null) === CustomerType::Company->value ? new ValidCnpj : new ValidCpf,
                ]),
            ImportColumn::make('state_registration')
                ->label('Inscrição estadual')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('birth_date')
                ->label('Data de nascimento')
                ->rules(['nullable', 'date']),
            ImportColumn::make('email')
                ->label('E-mail')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('phone')
                ->label('Telefone')
                ->rules(['nullable', 'string', 'max:20']),
            ImportColumn::make('zip_code')
                ->label('CEP')
                ->rules(['nullable', 'string', 'max:8']),
            ImportColumn::make('address')
                ->label('Logradouro')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('address_number')
                ->label('Número')
                ->rules(['nullable', 'string', 'max:20']),
            ImportColumn::make('address_complement')
                ->label('Complemento')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('neighborhood')
                ->label('Bairro')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('city')
                ->label('Cidade')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('state')
                ->label('UF')
                ->rules(['nullable', 'string', 'max:2']),
            ImportColumn::make('notes')
                ->label('Notas')
                ->rules(['nullable', 'string']),
            ImportColumn::make('is_active')
                ->label('Ativo')
                ->boolean()
                ->rules(['nullable', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Model
    {
        return Customer::query()->firstOrNew([
            'document' => $this->data['document'] ?? null,
        ]);
    }
}
