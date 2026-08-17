<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Financial\Models\Payable;
use Modules\Financial\Models\Receivable;

class OverdueFinancialsDigest extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  Collection<int, Receivable>  $overdueReceivables
     * @param  Collection<int, Payable>  $overduePayables
     */
    public function __construct(
        public Collection $overdueReceivables,
        public Collection $overduePayables,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Contas vencidas — FlowERP')
            ->markdown('mail.overdue-financials-digest', [
                'overdueReceivables' => $this->overdueReceivables,
                'overduePayables' => $this->overduePayables,
                'totalReceivable' => $this->overdueReceivables->sum('amount'),
                'totalPayable' => $this->overduePayables->sum('amount'),
            ]);
    }
}
