<?php

declare(strict_types=1);

namespace Modules\Financial\Jobs;

use App\Mail\OverdueFinancialsDigest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Models\Payable;
use Modules\Financial\Models\Receivable;

class SendOverdueFinancialsDigestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $overdueReceivables = Receivable::query()
            ->where('status', ReceivableStatus::Pending)
            ->whereDate('due_date', '<', today())
            ->with('customer')
            ->get();

        $overduePayables = Payable::query()
            ->where('status', PayableStatus::Pending)
            ->whereDate('due_date', '<', today())
            ->with('supplier')
            ->get();

        if ($overdueReceivables->isEmpty() && $overduePayables->isEmpty()) {
            return;
        }

        $recipients = User::query()
            ->permission(['receivables.view', 'payables.view'])
            ->get();

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new OverdueFinancialsDigest($overdueReceivables, $overduePayables));
        }
    }
}
