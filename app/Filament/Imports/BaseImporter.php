<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

abstract class BaseImporter extends Importer
{
    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'A importação foi concluída. '.number_format($import->successful_rows).' registro(s) importado(s) com sucesso.';

        $failedRowsCount = $import->getFailedRowsCount();

        if ($failedRowsCount > 0) {
            $body .= ' '.number_format($failedRowsCount).' linha(s) falharam.';
        }

        return $body;
    }
}
