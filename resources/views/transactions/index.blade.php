@extends('layouts.app')

@section('title', __('Transactions'))
@section('css')
    <link rel="stylesheet" href="{{ asset("plugins/datatables/datatables.min.css") }}">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-money-bill"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('User Balance') }}</span>
                        <span class="info-box-number js_currency">{{ $userBalance }} CAD</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-dollar-sign"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Collateral') }}</span>
                        <span class="info-box-number js_currency">{{ $userCollateral }} CAD</span>
                    </div>
                </div>
            </div>
            <!-- fix for small devices only -->
            <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-percent"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Pending Commissions') }}</span>
                        <span class="info-box-number js_currency">{{ $userCommission }} CAD</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
{{--        <div class="row">--}}
{{--            <div class="col-12">--}}
{{--                <div class="card  card-outline">--}}
{{--                    <div class="card-header">--}}
{{--                        <h3 class="card-title">{{ __('Filter') }}</h3>--}}
{{--                        <div class="card-tools">--}}
{{--                            <button type="button" class="btn btn-tool" data-card-widget="">--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <!-- /.card-header -->--}}
{{--                    <div class="table-responsive p-0">--}}
{{--                        <form role="form" method="get" action="{{ route('transactions.index') }}">--}}
{{--                            <div class="card-body user-card-body">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="form-group col-md-12 col-sm-12 col-xs-12 resp-mr">--}}
{{--                                        <div class="row">--}}
{{--                                            <div class="col-md-4">--}}
{{--                                                <div cla`ss="row">--}}
{{--                                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">--}}
{{--                                                        <label for="types" title="{{ __('Type') }}">{{ __('Type') }}</label>--}}
{{--                                                        <select name="types[]" class="form-control selectpicker" id="roles" multiple>--}}
{{--                                                            @foreach($transactionTypes as $key => $value)--}}
{{--                                                                <option value="{{ $key }}" @if(@in_array($key, request()->query('types'))) selected @endif>--}}
{{--                                                                    {{ $key == TransactionTypes::Inc ? 'Deposit' : 'Withdraw' }}--}}
{{--                                                                </option>--}}
{{--                                                            @endforeach--}}
{{--                                                        </select>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">--}}
{{--                                                        <label for="reasons" title="{{ __('Reason') }}">{{ __('Reason') }}</label>--}}
{{--                                                        <select name="reasons[]" class="form-control selectpicker" id="roles" multiple>--}}
{{--                                                            @foreach($transactionReasons as $key => $value)--}}
{{--                                                                <option value="{{ $key }}" @if(@in_array($key, request()->query('reasons'))) selected @endif>--}}
{{--                                                                    {{ __($value) }}--}}
{{--                                                                </option>--}}
{{--                                                            @endforeach--}}
{{--                                                        </select>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <span class="transaction-sepratore">  </span>--}}
{{--                                            <div class="col-md-4">--}}
{{--                                                <div class="row">--}}
{{--                                                    <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">--}}
{{--                                                        <label for="min_amount" title="{{ __('Value (Min)') }}">{{ __('Value (Min)') }}</label>--}}
{{--                                                        <input type="text" name="min_amount" class="form-control" id="min_amount"--}}
{{--                                                               value="{{ request()->query('min_amount') }}" placeholder="{{ __('Min amount') }}">--}}
{{--                                                    </div>--}}
{{--                                                    <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">--}}
{{--                                                        <label for="max_amount" title="{{ __('Value (Min)') }}">{{ __('Value (Max)') }}</label>--}}
{{--                                                        <input type="text" name="max_amount" class="form-control" id="max_amount"--}}
{{--                                                               value="{{ request()->query('max_amount') }}" placeholder="{{ __('Max amount') }}">--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="form-group col-md-12">--}}
{{--                                        <div class="">--}}
{{--                                            <button type="submit" class="btn btn-outline-primary">{{ __('Search') }}</button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            <!-- /.card-body -->--}}

{{--                        </form>--}}
{{--                    </div>--}}
{{--                    <!-- /.card-body -->--}}
{{--                </div>--}}
{{--                <!-- /.card -->--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="row">
            <div class="col-12">
                <div class="card card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('List') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="">
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0 transaction-scroll">
                        <table class="table table-hover text-nowrap table-striped" id="transactions_table">
                            <thead>
                            <tr>
                                <th>{{ __('#ID') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Product Group ') }}</th>
                                <th>{{ __('Product') }}</th>
                                {{--<th>{{ __('Transaction Type') }}</th>--}}
                                <th>{{ __('Transaction Value (CAD)') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Inc/Dec') }}</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
{{--                            <tbody>--}}
{{--                            @foreach($transactions as $transaction)--}}
{{--                                <tr>--}}
{{--                                    <td>{{ $transaction->id }}</td>--}}
{{--                                    <td>--}}
{{--                                        @can('users-update')--}}
{{--                                            <a href="{{ route('users.edit', ['user' => $transaction->user->id]) }}">{{ $transaction->user->name }}</a>--}}
{{--                                        @else()--}}
{{--                                            {{ $transaction->user->name }}--}}
{{--                                        @endcan--}}
{{--                                    </td>--}}
{{--                                    <td>--}}
{{--                                        @if($transaction->type == TransactionTypes::Inc)--}}
{{--                                            <i class="fas fa-arrow-up text-success"></i>--}}
{{--                                        @elseif($transaction->type == TransactionTypes::Dec)--}}
{{--                                            <i class="fas fa-arrow-down text-danger"></i>--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
{{--                                    <td>--}}
{{--                                        @if($transaction->type == TransactionTypes::Inc)--}}
{{--                                            <span class="text-success">{{ number_format($transaction->amount, 4) }}</span>--}}
{{--                                        @elseif($transaction->type == TransactionTypes::Dec)--}}
{{--                                            <span class="text-danger">{{ number_format($transaction->amount, 4) }}</span>--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
{{--                                    <td>{{ TransactionReasons::listReverse()[$transaction->reason] }}</td>--}}
{{--                                    <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i') }}</td>--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}
{{--                            </tbody>--}}
                        </table>
                    </div>
                    <!-- /.card-body -->
{{--                    <div class="card-footer d-flex align-items-center justify-content-center">--}}
{{--                        {{ $transactions->withQueryString()->links() }}--}}
{{--                    </div>--}}
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>

    @include('transactions.partials.transaction_detail')
@endsection
@section('js')
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/transaction.js') }}"></script>
@endsection
