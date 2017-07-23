@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">

        @include('partials.search', $search_data)

        <div class="col-md-12">
            {{ $general_ledgers->links() }}
        </div>

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    GENERAL LEDGER
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th style="width: 1%;"></th>
                            <th>GL#</th>
                            <th>DATE</th>
                            <th>JOURNAL</th>
                            <th>PARTICULARS</th>
                            <th>REFERENCE</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($general_ledgers as $i => $general_ledger)
                            <tr>
                                <td><a href="{{ url("/$route/{$general_ledger->id}/edit") }}"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a></td>
                                <td>{{ str_pad($general_ledger->id,7,0,STR_PAD_LEFT) }}</td>
                                <td>{{ $general_ledger->date }}</td>
                                <td>{{ $general_ledger->journal->journal_desc }}</td>
                                <td>{{ $general_ledger->particulars }}</td>
                                <td>{{ $general_ledger->reference }}</td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            {{ $general_ledgers->links() }}
        </div>
    </div>


</div>
@endsection
