@extends('layouts.reports')
@section('content')
    <style>
        @media print {
            * {
                font-size: 1.5vw;
            }
        }

        * {
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #fff;
            color: #000;
        }

        .b-bottom {
            border-bottom: 1px solid #000;
        }

        .b-bottom-2 {
            border-bottom: 5px double #000;
        }

        .w-80 {
            width: 80%;
        }

        .indent-1 {
            padding-left: 40px;
        }

        .indent-2 {
            padding-left: 80px;
        }

        .indent-3 {
            padding-left: 120px;
        }

        .signatory-table{
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;;
        }

        .signatory-table td{
            border: 1px solid #000;
            text-align: center;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>
                    UNLIFINANCE CORPORATION <br/>
                    <small>Corner Rosario-Amapola St. Bacolod City</small>
                </h2>
                <h3>
                    CASH VOUCHER
                </h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-2">PAY TO</div>
            <div class="col-xs-6 b-bottom">{{ $loan->customer->name }}</div>
            <div class="col-xs-2 text-right">DATE</div>
            <div class="col-xs-2 b-bottom" style="white-space: nowrap;">{{ date("F j, Y") }}</div>
        </div>

        <div class="row">
            <div class="col-xs-2">PESOS</div>
            <div class="col-xs-10 b-bottom">
                {{ $words }}
            </div>
        </div>

        <div class="row" style="height:350px; margin-top: 5px;">
            <div class="col-xs-8" style="outline:1px solid #000; height:100%;">
                <div class="row">
                    <div class="col-xs-12 text-center" style="outline:1px solid #000;">
                        E  X  P  L  A  N  A  T  I  O  N
                    </div>

                    <div class="col-xs-12">
                        <div class="row">
                            For: availment of loan re: <br/>
                            {{ $loan->collateral->collateral_desc }}, <br/>
                            pn - {{ number_format($loan->pn_amount,2) }}, {{ $loan->terms }} months, rate: {{ $loan->interest_rate }} + {{ $loan->rebate_rate }} %
                        </div>

                        <div class="row">
                            <div class="col-xs-10">
                                cash-out
                            </div>
                            <div class="col-xs-2 text-right">
                                {{ number_format($loan->amount,2) }}
                            </div>

                            <div class="col-xs-1">
                                less:
                            </div>
                            <div class="col-xs-7">
                                Service Fee
                            </div>
                            <div class="col-xs-2 text-right">
                                {{ number_format($loan->service_fees,2) }}
                            </div>
                            <div class="col-xs-2"> &nbsp; </div>

                            <div class="col-xs-7 col-xs-offset-1">
                                Doc Fee
                            </div>
                            <div class="col-xs-2 text-right">
                                {{ number_format($loan->total_doc_fees,2) }}
                            </div>
                            <div class="col-xs-2">&nbsp;</div>

                            @foreach ( $loan->otherAdditions as $OtherAddition )
                                <div class="col-xs-7 col-xs-offset-1">
                                    {{ $OtherAddition->account_desc }}
                                </div>
                                <div class="col-xs-2 text-right">
                                    {{ number_format($OtherAddition->pivot->amount,2) }}
                                </div>
                                <div class="col-xs-2">&nbsp;</div>

                            @endforeach

                            @foreach ( $loan->otherDeductions as $OtherDeduction )
                                <div class="col-xs-7 col-xs-offset-1">
                                    {{ $OtherDeduction->account_desc }}
                                </div>

                                <div class="col-xs-2 text-right">
                                    - {{ number_format($OtherDeduction->pivot->amount,2) }}
                                </div>

                                <div class="col-xs-2">&nbsp;</div>


                            @endforeach




                            <div class="col-xs-7 col-xs-offset-1">
                                Insurance Prem.
                            </div>
                            <div class="col-xs-2 text-right b-bottom">
                                {{ number_format($loan->od_insurance_fees,2) }}
                            </div>
                            <div class="col-xs-2 text-right b-bottom">{{ number_format( $loan->service_fees + $loan->total_doc_fees + $loan->od_insurance_fees + $loan->otherAdditions()->sum('amount') - $loan->otherDeductions()->sum('amount') ,2) }}</div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-4" style="height:100%;">
                <div class="row">
                    <div class="col-xs-12 text-center" style="outline: 1px solid #000;">
                        AMOUNT
                    </div>
                    <div class="col-xs-12 text-center" style="padding-top:120px;">
                        {{ number_format( $cash_amount ,2) }}
                    </div>


                </div>
            </div>

        </div>


    </div>

    <div class="row" style="outline:1px solid #000;">
        <div class="col-xs-7" style="outline:1px solid #000; height:100%;">
            <div class="row">
                <div class="col-xs-12">
                    JOURNAL ENTRY
                </div>
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-8">
                            L/R
                        </div>
                        <div class="col-xs-2 text-right">
                            {{ number_format($loan->pn_amount,2) }}
                        </div>
                        <div class="col-xs-2"></div>

                        <div class="col-xs-10">
                            UDI
                        </div>
                        <div class="col-xs-2 text-right">
                            {{ number_format($loan->interest_amount,2) }}
                        </div>

                        <div class="col-xs-10">
                            RFF
                        </div>
                        <div class="col-xs-2 text-right">
                            {{ number_format($loan->rebate_amount,2) }}
                        </div>

                        <div class="col-xs-10">
                            Processing Fee
                        </div>
                        <div class="col-xs-2 text-right">
                            {{ number_format($loan->service_fees,2) }}
                        </div>

                        <div class="col-xs-10">
                            Doc. Fee
                        </div>
                        <div class="col-xs-2 text-right">
                            {{ number_format($loan->total_doc_fees,2) }}
                        </div>

                        <div class="col-xs-10">
                            Insurance Payable
                        </div>
                        <div class="col-xs-2 text-right">
                            {{ number_format($loan->od_insurance_fees,2) }}
                        </div>

                        @foreach($loan->otherAdditions as $OtherAddition)
                            <div class="col-xs-10">
                                {{ $OtherAddition->account_desc }}
                            </div>
                            <div class="col-xs-2 text-right">
                                {{ number_format($OtherAddition->pivot->amount,2) }}
                            </div>
                        @endforeach

                        @foreach($loan->otherDeductions as $OtherDeduction)
                            <div class="col-xs-8">
                                {{ $OtherDeduction->account_desc }}
                            </div>
                            <div class="col-xs-2 text-right">
                                {{ number_format($OtherDeduction->pivot->amount,2) }}
                            </div>
                            <div class="col-xs-2"></div>
                        @endforeach

                        <div class="col-xs-10">
                            RCBC SAVINGS
                        </div>
                        <div class="col-xs-2 text-right">
                            {{ number_format( $cash_amount ,2) }}
                        </div>



                    </div>
                    <div class="row" style="padding-top:50px;">
                        <table class="signatory-table">
                            <tr>
                                <td>
                                    Made By:
                                </td>
                                <td>
                                    Checked By:
                                </td>
                                <td>
                                    Audited By:
                                </td>
                                <td>
                                    Approved for Payment:
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <br/><br/>
                                    EOG
                                </td>
                                <td class="text-center">
                                    <br/><br/>
                                    JJP
                                </td>
                                <td>
                                    <br/><br/>
                                </td>
                                <td class="text-center">
                                    <br/><br/>
                                    BKPASCUAL
                                </td>
                            </tr>
                        </table>
                        <div class="row text-center">
                            {{ date("m/d/Y H:i:s") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-5" style="outline:1px solid #000; height:100%;">
            <div class="row">
                <div class="col-xs-12">
                    Received from <br/><br/>
                    The sum of <br/>
                </div>
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-12 b-bottom">
                            &nbsp;
                        </div>
                        <div class="col-xs-12 b-bottom">
                            RCBC SAVINGS 1200000791
                        </div>
                        <div class="col-xs-12 b-bottom">
                            &nbsp;
                        </div>
                        <div class="col-xs-12 b-bottom">
                            &nbsp;
                        </div>
                        <div class="col-xs-12 b-bottom">
                            &nbsp;
                        </div>
                        <div class="col-xs-12">
                            as full/partial payment of the above
                        </div>

                        <div class="col-xs-12 b-bottom">
                            <br/><br/><br/>
                        </div>
                        <div class="col-xs-12 text-center">
                            PAYEE'S SIGNATURE
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>


@endsection