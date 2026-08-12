<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Resources\AuditLogs;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Administration\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use Modules\Administration\Filament\Resources\AuditLogs\Pages\ViewAuditLog;
use Modules\Administration\Filament\Resources\AuditLogs\Tables\AuditLogsTable;
use Modules\Administration\Models\AuditLog;
use UnitEnum;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static UnitEnum|string|null $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Logs de Auditoria';

    protected static ?string $modelLabel = 'Log de Auditoria';

    protected static ?string $pluralModelLabel = 'Logs de Auditoria';

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
            'view' => ViewAuditLog::route('/{record}'),
        ];
    }
}
