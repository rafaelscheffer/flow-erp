<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Resources\AuditLogs\Pages;

use App\Enums\AuditEventType;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Administration\Filament\Resources\AuditLogs\AuditLogResource;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Quando')
                            ->dateTime('d/m/Y H:i:s'),
                        TextEntry::make('user.name')
                            ->label('Usuário')
                            ->default('Sistema'),
                        TextEntry::make('event')
                            ->label('Evento')
                            ->badge()
                            ->formatStateUsing(fn (AuditEventType $state): string => $state->label())
                            ->color(fn (AuditEventType $state): string => $state->color()),
                        TextEntry::make('auditable_type')
                            ->label('Modelo')
                            ->formatStateUsing(fn (string $state): string => class_basename($state)),
                        TextEntry::make('auditable_id')
                            ->label('ID do registro'),
                        TextEntry::make('ip_address')
                            ->label('IP'),
                        TextEntry::make('user_agent')
                            ->label('Agente do Usuário')
                            ->columnSpanFull(),
                    ]),
                Section::make('Alterações')
                    ->columns(2)
                    ->schema([
                        KeyValueEntry::make('old_values')
                            ->label('Valores antigos'),
                        KeyValueEntry::make('new_values')
                            ->label('Valores novos'),
                    ]),
            ]);
    }
}
