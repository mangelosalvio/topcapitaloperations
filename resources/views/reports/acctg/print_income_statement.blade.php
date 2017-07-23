@extends('layouts.printable_reports')
@section('content')
    <div class="text-center" style="font-size:14px; font-weight: bold;">
        Income Statement <br/>
        {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }} - {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
    </div>
    <table class="table">
        <thead>
            <tr>
                <th colspan="4">REVENUES</th>
            </tr>
        </thead>
        <tbody>
            @foreach($RevenueAccounts as $RevenueAccount)
                <tr>
                    <td>{{ $RevenueAccount->account_code }}</td>
                    <td>{{ $RevenueAccount->account_desc }}</td>
                    <td class="text-right">{{ number_format($RevenueAccount->amount,2) }}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th class="text-right">{{ number_format($total_revenue,2) }}</th>
            </tr>
        </tbody>
        <thead>
        <tr>
            <th colspan="4">EXPENSES</th>
        </tr>
        </thead>
        <tbody>
        @foreach($ExpenseAccounts as $ExpenseAccount)
            <tr>
                <td>{{ $ExpenseAccount->account_code }}</td>
                <td>{{ $ExpenseAccount->account_desc }}</td>
                <td class="text-right">{{ number_format($ExpenseAccount->amount,2) }}</td>
                <td></td>
            </tr>
        @endforeach
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th class="text-right">{{ number_format($total_expenses,2) }}</th>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="2" class="text-right">NET PROFIT</th>
            <th>&nbsp;</th>
            <th class="text-right">{{ number_format($total_revenue - $total_expenses,2) }}</th>
        </tr>
        </tfoot>
    </table>
@endsection