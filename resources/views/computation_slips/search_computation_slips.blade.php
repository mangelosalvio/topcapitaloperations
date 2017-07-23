@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">

        @include('partials.search', $search_data)

        <div class="col-md-12">
            {{ $loans->links() }}
        </div>

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    COMPUTATION SLIPS
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th style="width: 1%;"></th>
                            <th>DATE</th>
                            <th>CUSTOMER</th>
                            <th>AMOUNT</th>
                            <th>TRANS TYPE</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($loans as $i => $loan)
                            <tr>
                                <td><a href="{{ url("/$route/{$loan->id}/edit") }}"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a></td>
                                <td>{{ $loan->date }}</td>
                                <td>{{ $loan->customer->name }}</td>
                                <td>{{ number_format($loan->amount,2) }}</td>
                                <td>{{ $loan->transType->label }}</td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            {{ $loans->links() }}
        </div>
    </div>


</div>
@endsection
