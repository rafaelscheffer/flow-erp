<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Exports;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

abstract class BaseExporter extends Exporter
{
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação foi concluída. '.number_format($export->successful_rows).' registro(s) exportado(s).';

        $failedRowsCount = $export->getFailedRowsCount();

        if ($failedRowsCount > 0) {
            $body .= ' '.number_format($failedRowsCount).' registro(s) falharam.';
        }

        return $body;
    }
}
