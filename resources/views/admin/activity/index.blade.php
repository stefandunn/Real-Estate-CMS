@extends('admin.layout')

@section('content')
<div class="container">
    @include('admin.title-bar')
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                @if( $activity_logs->count() == 0 )
                    <p>No activity logs found @if( !empty($_GET['search']) ) using your search criteria @endif</p>
                @else
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="col-sm-1">Action</th>
                            <th class="col-sm-2">What</th>
                            <th class="col-sm-1">Who</th>
                            <th class="col-sm-5">Original</th>
                            <th class="col-sm-5">What's new</th>
                            <th class="col-sm-3">When</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($activity_logs as $activity_log)
                                <tr>
                                    <td style='background-color: {{ $action_colours[$activity_log->action] }}'>{{ strtoupper($activity_log->action) }}</td>
                                    <td style='background-color: {{ $action_colours[$activity_log->action] }}'>{{ ucwords(str_replace("_", " ", $activity_log->model)) }}</td>
                                    <td style='background-color: {{ $action_colours[$activity_log->action] }}'>{{ $activity_log->user->name }}</td>
                                    <td style='background-color: {{ $action_colours[$activity_log->action] }}'>
                                        @if( !empty($activity_log->originalToArray()) )
                                            <span class="btn btn-info" onclick="showModal('Original Data', $(this).next('table').clone().show(), {'max-width' : 900, 'overflow' : 'auto', 'max-height': 700});">See original</span>
                                            <table class="table bg-white" style="display: none; margin-bottom: 0;">
                                                <thead>
                                                    <tr>
                                                        <th class="font-blue">Attribute</th>
                                                        <th class="font-blue">Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach( $activity_log->originalToArray() as $attr => $value )
                                                    <tr>
                                                        <td>{{ $attr }}</td>
                                                        <td>{!! $value !!}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p>N/A</p>
                                        @endif
                                    </td>
                                    <td style='background-color: {{ $action_colours[$activity_log->action] }}'>
                                        @if( !empty($activity_log->changesToArray()) )
                                            <span class="btn btn-info" onclick="showModal('New Data', $(this).next('table').clone().show());">See new data</span>
                                            <table class="table bg-white" style="display: none; margin-bottom: 0;">
                                                <thead>
                                                    <tr>
                                                        <th class="font-blue">Attribute</th>
                                                        <th class="font-blue">Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach( $activity_log->changesToArray() as $attr => $value )
                                                    <tr>
                                                        <td>{{ $attr }}</td>
                                                        <td>{{ $value }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p>N/A</p>
                                        @endif
                                    </td>
                                    <td style='background-color: {{ $action_colours[$activity_log->action] }}' class="nowrap">
                                        {!! date('g:ia j\<\s\u\p\>S\<\/\s\u\p\> F Y', strtotime($activity_log->created_at)) !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $activity_logs->links() }}
                @endif
            </div>
        </div>
    </div>
    @if( $activity_logs->count() > 0 )
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <a href="{{ action('Admin\ActivityController@clear') }}" class="btn btn-danger">Delete all</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection