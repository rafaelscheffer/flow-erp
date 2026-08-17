@component('mail::message')
# Contas vencidas

Este é o resumo diário de contas em atraso no FlowERP.

@if ($overdueReceivables->isNotEmpty())
## A receber ({{ $overdueReceivables->count() }}) — total R$ {{ number_format($totalReceivable, 2, ',', '.') }}

@component('mail::table')
| Cliente | Vencimento | Valor |
| :--- | :--- | ---: |
@foreach ($overdueReceivables as $receivable)
| {{ $receivable->customer->name }} | {{ $receivable->due_date->format('d/m/Y') }} | R$ {{ number_format($receivable->amount, 2, ',', '.') }} |
@endforeach
@endcomponent
@endif

@if ($overduePayables->isNotEmpty())
## A pagar ({{ $overduePayables->count() }}) — total R$ {{ number_format($totalPayable, 2, ',', '.') }}

@component('mail::table')
| Fornecedor | Vencimento | Valor |
| :--- | :--- | ---: |
@foreach ($overduePayables as $payable)
| {{ $payable->supplier->name }} | {{ $payable->due_date->format('d/m/Y') }} | R$ {{ number_format($payable->amount, 2, ',', '.') }} |
@endforeach
@endcomponent
@endif

@component('mail::button', ['url' => url('/admin/cash-flow')])
Ver fluxo de caixa
@endcomponent

Atenciosamente,<br>
{{ config('app.name') }}
@endcomponent
