@extends('layouts.app')

@section('title', __('Import product details'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('Import fields') }}
                        </h3>
                    </div>
                    <form class="form-horizontal" method="POST" action="{{ route('products.import') }}">

                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            @csrf
                            <input type="hidden" name="temp_imported_id" value="{{ $tempImportedId }}"/>
                            <table class="table table-hover text-nowrap table-striped">
                                <thead>
                                <tr>
                                    @foreach ($dataHeaderFields as $dataHeader)
                                        <th>
                                            {{ $dataHeader }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($dataHeaderFields as $dataHeader)
                                        <th>
                                            @if (in_array($dataHeader, Arr::collapse(array_column($requiredDbFields, 'possibleMatches'))))
                                                <select name="fields[{{ $dataHeader }}]">
                                                    @foreach ($requiredDbFields as $dbField => $options)
                                                        <option value="{{ $dbField }}"
                                                                @if (in_array($dataHeader, $options['possibleMatches'])) selected @endif
                                                        >
                                                            {{ $dbField }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data as $row)
                                    <tr>
                                        @foreach ($row as $key => $value)
                                            <td>{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    {{--                                        @foreach ($dataHeaderFields as $dataHeader)--}}
                                    {{--                                            <td>--}}
                                    {{--                                                <select name="fields[{{ $key }}]">--}}
                                    {{--                                                    @foreach ($requiredDbFields as $dbField => $options)--}}
                                    {{--                                                        <option value="{{ $dbField }}"--}}
                                    {{--                                                                @if (in_array($dataHeader, $options['possibleMatches'])) selected @endif--}}
                                    {{--                                                        >--}}
                                    {{--                                                            {{ $dbField }}--}}
                                    {{--                                                        </option>--}}
                                    {{--                                                    @endforeach--}}
                                    {{--                                                </select>--}}
                                    {{--                                            </td>--}}
                                    {{--                                        @endforeach--}}
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                Import Data
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
