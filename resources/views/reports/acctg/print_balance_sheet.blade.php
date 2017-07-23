@extends('layouts.printable_reports')
@section('content')
    <div class="text-center" style="font-size:14px; font-weight: bold;">
        BALANCE SHEET <br/>
        {{ \Carbon\Carbon::create($year,$month)->format("F Y") }}
    </div>
    <table class="table">
        <thead>
            <tr>
                <th colspan="4">ASSETS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($AssetAccounts as $AssetAccount)
                <tr>
                    <td>{{ $AssetAccount->account_code }}</td>
                    <td>{{ $AssetAccount->account_desc }}</td>
                    <td class="text-right">{{ number_format($AssetAccount->amount,2) }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="2" class="text-right">TOTAL ASSETS</th>
                <th class="text-right">{{ number_format($total_assets,2) }}</th>
            </tr>

            <tr>
                <th colspan="4">LIABILITIES</th>
            </tr>

            @foreach($LiabilityAccounts as $LiabilityAccount)
                <tr>
                    <td>{{ $LiabilityAccount->account_code }}</td>
                    <td>{{ $LiabilityAccount->account_desc }}</td>
                    <td class="text-right">{{ number_format($LiabilityAccount->amount,2) }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="2" class="text-right">TOTAL LIABILITIES</th>
                <th class="text-right">{{ number_format($total_liabilities,2) }}</th>
            </tr>

            <tr>
                <th colspan="4">EQUITY</th>
            </tr>

            @foreach($EquityAccounts as $EquityAccount)
                <tr>
                    <td>{{ $EquityAccount->account_code }}</td>
                    <td>{{ $EquityAccount->account_desc }}</td>
                    <td class="text-right">{{ number_format($EquityAccount->amount,2) }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="2" class="text-right">TOTAL EQUITY</th>
                <th class="text-right">{{ number_format($total_equity,2) }}</th>
            </tr>

            <tr>
                <th colspan="2" class="text-right">NET INCOME/LOSS</th>
                <th class="text-right">{{ number_format($net_income,2) }}</th>
            </tr>

            <tr>
                <th colspan="4">&nbsp;</th>
            </tr>
            <tr>
                <th colspan="2" class="text-right">TOTAL LIABILITIES AND EQUITY</th>
                <th class="text-right">{{ number_format($net_income + $total_liabilities + $total_equity,2) }}</th>
            </tr>
        </tbody>
    </table>
@endsection