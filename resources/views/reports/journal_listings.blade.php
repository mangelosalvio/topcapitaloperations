@extends('layouts.app')
@section('content')
<script type="text/javascript">
    function printIframe(id)
    {
        var iframe = document.frames ? document.frames[id] : document.getElementById(id);
        var ifWin = iframe.contentWindow || iframe;
        iframe.focus();
        ifWin.printPage();
        return false;
    }
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Journal Listings
                </div>
                <div class="panel-body">
                    {!! Form::open([
                        'url' => '/journal-listings',
                        'class' => 'form-horizontal'
                    ]) !!}
                    <div class="form-group">
                        {!! Form::label('from_date','From Date', [
                        'class' => 'col-sm-2 control-label'
                        ]) !!}

                        <div class="col-sm-3">
                            {!! Form::text('from_date', request('from_date'), [
                            'class' => 'form-control date'
                            ]) !!}
                        </div>

                        {!! Form::label('journal_id','Journal', [
                        'class' => 'col-sm-2 control-label'
                        ]) !!}
                        <div class="col-sm-3">
                            {!! Form::select('journal_id', $journals,request('journal_id'), [
                            'class' => 'form-control',
                            'placeholder' => 'Select Journal'
                            ]) !!}
                        </div>

                        <div class="col-sm-1">
                            {!! Form::submit('Generate',[
                                'class' => 'btn btn-default'
                            ]) !!}
                        </div>
                        @if ( request()->has(['from_date', 'to_date']) )
                        <div class="col-sm-1">
                            <input type="button" value="Print" onclick="printIframe('frame');" class="btn btn-default"/>
                        </div>
                        @endif
                    </div>
                    <div class="form-group">
                        {!! Form::label('to_date','To Date', [
                        'class' => 'col-sm-2 control-label'
                        ]) !!}

                        <div class="col-sm-3">
                            {!! Form::text('to_date', request('to_date'), [
                            'class' => 'form-control date'
                            ]) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}

                    <hr/>


                    @if ( isset( $url ) )
                    <div class="col-sm-12">
                        <iframe id="frame" src="{{ $url }}" frameborder="0" style="width:100%; height:400px; overflow-y: auto;"></iframe>
                    </div>
                    @endif


                </div>
            </div>
        </div>

    </div>


</div>
@endsection
