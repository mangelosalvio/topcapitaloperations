@extends('layouts.printable_reports')
@section('content')
    <div class="text-center" style="font-size:14px; font-weight: bold;">
        Trial Balance Report <br/>
        {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }} - {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>ACCOUNT CODE</th>
                <th>ACCOUNT</th>
                <th class="text-right">DEBIT</th>
                <th class="text-right">CREDIT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($Accounts as $Account)
                <tr>
                    <td>{{ $Account->account_code }}</td>
                    <td>{{ $Account->account_desc }}</td>
                    <td class="text-right">{{ number_format($Account->debit,2) }}</td>
                    <td class="text-right">{{ number_format($Account->credit,2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th class="text-right">{{ number_format($total_debit,2) }}</th>
            <th class="text-right">{{ number_format($total_credit,2) }}</th>
        </tr>
        </tfoot>
    </table>
@endsection