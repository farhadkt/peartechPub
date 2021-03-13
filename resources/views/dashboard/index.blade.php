@extends('layouts.app')

@section('title', __('Dashboard'))
@section('css')
    <link rel="stylesheet" href="{{ asset("plugins/datatables/datatables.min.css") }}">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="card card-outline card-mrg">
                    <div class="card-header">
                        <h5 class="card-title"> Watch list </h5>
                        <div class="card-tools wl">
                            <button type="button" class="btn btn-tool" data-card-widget="">
                            </button>
                            <input type="hidden" id="user_id" value="{{ Auth::user()->id }}">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">--}}
                            {{--<div class="watch-list-s1">--}}
                            {{--<div class="row">--}}
                            {{--<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">--}}
                            {{--<div class="form-group">--}}
                            {{--<select class="form-control  select2" style="width: 100%;">--}}
                            {{--<option selected="selected">Alabama</option>--}}
                            {{--<option>Alaska</option>--}}
                            {{--<option>California</option>--}}
                            {{--<option>Delaware</option>--}}
                            {{--<option>Tennessee</option>--}}
                            {{--<option>Texas</option>--}}
                            {{--<option>Washington</option>--}}
                            {{--</select>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">--}}
                            {{--<form class="form-inline">--}}
                            {{--<div class="input-group input-group-sm watch-list-search">--}}
                            {{--<div class="input-group-append">--}}
                            {{--<button class="btn btn-navbar" type="submit">--}}
                            {{--<i class="fas fa-search"></i>--}}
                            {{--</button>--}}
                            {{--</div>--}}
                            {{--<input class="form-control watch-list-srch form-control-navbar" type="search" placeholder="Search" aria-label="Search">--}}
                            {{--</div>--}}
                            {{--</form>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="watch-list-orders"
                                                 class="table-responsive p-0 table-scrol watch-list-scroll"
                                                 style="height: 310px;">
                                                <table
                                                    class="table table-head-fixed text-nowrap table-hover table-valign-middle"
                                                    id="watch_list_table">
                                                    <thead>
                                                    <tr>
                                                        <th>Product Group</th>
                                                        <th>Product</th>
                                                        <th>Delivery</th>
                                                        <th>Collateral</th>
                                                        <th>Amount</th>
                                                        <th>Buy/Sell</th>
                                                        <th>Submission Date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-pd-r">--}}
                            {{--<div class="card-align">--}}
                            {{--<div class="card-headers">--}}
                            {{--<h3 class="sub-card-title">Buy Orders</h3>--}}
                            {{--</div>--}}
                            {{--<!-- /.card-header -->--}}
                            {{--<div class=" card-bd crd-mr" id="buy_orders">--}}
                            {{--@include('dashboard.partials.buy_orders')--}}
                            {{--</div>--}}
                            {{--<!-- /.card-body -->--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-pd-l">--}}
                            {{--<div class="card-align">--}}
                            {{--<div class="card-headers">--}}
                            {{--<h3 class="sub-card-title">Sell Orders</h3>--}}
                            {{--</div>--}}
                            {{--<!-- /.card-header -->--}}
                            {{--<div class="card-bd crd-mr" id="sell_orders">--}}
                            {{--@include('dashboard.partials.sell_orders')--}}
                            {{--</div>--}}
                            {{--<!-- /.card-body -->--}}
                            {{--</div>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>


            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">

                <div class="card card-outline card-mrg btn-light-st">
                    <div class="card-header" id="select-group-product">
                        <h5 class="card-title">Charts</h5>
                        <div class="card-tools card-tools-product" id="products-list">
                            <select class="selectpicker" data-size="5" data-live-search="true"
                                    id="product_chart_select">
                                {{--@foreach($products as $product)
                                    --}}{{-- data-content="@php $a = $product->name;$b = substr($a, 0, 25);$y = $b . "...";if($a > $b)echo $y; else echo $a; @endphp" --}}{{--
                                    <option value="{{ $product->id }}"
                                            data-content="{{ $product->name }}"
                                            title="{{$product->name}}"
                                        {{ $loop->iteration == 1 ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach--}}
                            </select>
                        </div>
                        <div class="card-tools card-tools-group" id="groups-list">
                            <select class="selectpicker" data-size="5" data-live-search="true" id="group_select">
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}"
                                            data-content="{{ $group->name }}"
                                            title="{{ $group->name }}"
                                        {{ $loop->iteration == 1 ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="product_chart_div_wrapper">
                            {{--chart-sorting--}}
                            <div class="search-filters-row">
                                <ul class="btn-group box-radio box-radio-numeric ">
                                    <li class="">
                                        <input id="radio-bedr-4" name="bedroom" type="radio"
                                               onclick="productChartZoomAt('3M')">
                                        <label class="" for="radio-bedr-4">3M</label>
                                    </li>
                                    <li class="">
                                        <input id="radio-bedr-5" name="bedroom" type="radio"
                                               onclick="productChartZoomAt('6M')">
                                        <label class="" for="radio-bedr-5">6M</label>
                                    </li>
                                    <li class="">
                                        <input id="radio-bedr-6" name="bedroom" type="radio"
                                               onclick="productChartZoomAt('1Y')">
                                        <label class="" for="radio-bedr-6">1Y</label>
                                    </li>
                                    <li class="">
                                        <input id="radio-bedr-7" name="bedroom" type="radio"
                                               onclick="productChartZoomAt('2Y')">
                                        <label class="" for="radio-bedr-7">2Y</label>
                                    </li>
                                    <li class="">
                                        <input id="radio-bedr-8" name="bedroom" type="radio"
                                               onclick="productChartZoomAt('3Y')">
                                        <label class="" for="radio-bedr-8">3Y</label>
                                    </li>
                                    <li class="">
                                        <input id="radio-bedr-9" name="bedroom" type="radio"
                                               onclick="productChartZoomAt('5Y')">
                                        <label class="" for="radio-bedr-9">5Y</label>
                                    </li>
                                    <li class="">
                                        <input id="radio-bedr-10" name="bedroom" type="radio"
                                               onclick="productChartZoomAt('10Y')">
                                        <label class="" for="radio-bedr-10">10Y</label>
                                    </li>
                                    <li class="">
                                        <input id="radio-bedr-0" name="bedroom" type="radio"
                                               onclick="productChartZoomAt('0')" checked>
                                        <label class="" for="radio-bedr-0">All Time</label>
                                    </li>
                                </ul>
                            </div>
                            {{--chart-sorting--}}
                            <div id="product_chart_message"></div>
                            <div id="product_chart_div"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                <div class="card card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <div class="submit-order">
                            <div class="nav-item">
                                <button type="button" data-backdrop="static"
                                        class="btn btn-block btn-primary dashboard-order-w"
                                        id="submit_order_btn">
                                    <a class="nav-links">Submit Order</a>
                                </button>
                            </div>
                        </div>
                        <ul class="nav nav-tabs" id="" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="dashboard-tab-unmatched-tab" data-toggle="pill"
                                   href="#dashboard-tab-unmatched" role="tab" aria-controls="dashboard-tab-unmatched"
                                   aria-selected="false">Unmatched</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="dashboard-tab-positions-tab" data-toggle="pill"
                                   href="#dashboard-tab-positions" role="tab" aria-controls="dashboard-tab-positions"
                                   aria-selected="true">Position</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="dashboard-tab-history-tab" data-toggle="pill"
                                   href="#dashboard-tab-history" role="tab" aria-controls="dashboard-tab-history"
                                   aria-selected="false">History</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content" id="">
                            <div class="tab-pane fade show active" id="dashboard-tab-unmatched" role="tabpanel"
                                 aria-labelledby="dashboard-tab-unmatched-tab">
                                <div class="card">
                                    <div id="unmatch" class="p-0 table-scroll scrolling table-responsive"
                                         style="height: 316px;">
                                        <table
                                            id="unmatch_table"
                                            class="table table-head-fixed text-nowrap table-hover table-valign-middle table-sort">
                                            <thead>
                                            <tr>
                                                <th>Product Group</th>
                                                <th>Product</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Buy/Sell</th>
                                                <th>Delivery</th>
                                                <th>Validity</th>
                                                <th>Collateral</th>
                                                <th>Commission</th>
                                                <th>Submission Date</th>
                                                {{--<th>Profit/Loss</th>--}}
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                            <div class="tab-pane fade" id="dashboard-tab-positions" role="tabpanel"
                                 aria-labelledby="dashboard-tab-positions-tab">
                                <div class="card">
                                    <div id="position" class="p-0 table-scroll scrolling table-responsive"
                                         style="height: 316px;">
                                        <table
                                            id="position_table"
                                            class="table table-head-fixed text-nowrap table-hover table-valign-middle table-sort">
                                            <thead>
                                            <tr>
                                                <th>Product Group</th>
                                                <th>Product</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Buy/Sell</th>
                                                <th>Delivery</th>
                                                {{--<th>Validity</th>--}}
                                                <th>Collateral</th>
                                                <th>Commission</th>
                                                <th>Profit/Loss</th>
                                                <th>Submission Date</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                            <div class="tab-pane fade" id="dashboard-tab-history" role="tabpanel"
                                 aria-labelledby="dashboard-tab-history-tab">
                                <div class="card">
                                    <div id="history" class="p-0 scrolling table-responsive" style="height: 316px;">
                                        <table
                                            id="history_table"
                                            class="table table-head-fixed text-nowrap table-hover table-valign-middle table-sort">
                                            <thead>
                                            <tr>
                                                <th>Product Group</th>
                                                <th>Product</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Buy/Sell</th>
                                                <th>Delivery</th>
                                                {{--<th>Validity</th>--}}
                                                <th>Collateral</th>
                                                <th>Commission</th>
                                                <th>Profit/Loss</th>
                                                <th>Submission Date</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="submit_order">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body submit-order-modal">
                    <form role="form" method="post" action="{{ route('orders.create') }}" id="create_order">
                        @csrf
                        <div class="form-group">
                            <select id="product_select" name="product_id" data-size="5"
                                    class="form-control w-100 selectpicker {{ Render::isInvalid('product_id') }}"
                                    data-live-search="true">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            {!! Render::errMsg('product_id') !!}
                        </div>
                        <div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <!-- text input -->
                                    <label>
                                        Amount:
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group input-group">
                                        <input name="amount" type="text" min="0" step="any" id="amount"
                                               class="form-control {{ Render::isInvalid('amount') }}"
                                               value="{{ old('amount') }}"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-dollar-sign"
                                                                              style="font-size: 18px"></i></span>
                                        </div>
                                        {!! Render::errMsg('amount') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <!-- text input -->
                                    <div class="form-group modal-form-group">
                                        <label>
                                            Delivery Date:
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group input-group">
                                        <input name="delivery_date" id="delivery_date" type="text" autocomplete="off"
                                               class="form-control {{ Render::isInvalid('delivery_date') }}"
                                               value="{{ old('delivery_date') }}"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"
                                                                              style="font-size: 12px"></i></span>
                                        </div>
                                        {!! Render::errMsg('delivery_date') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <!-- text input -->
                                    <label>
                                        Collateral:
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group input-group">
                                        <input name="collateral" type="text" min="0" max="100" step="any" autocomplete="off"
                                               id="collateral"
                                               class="form-control {{ Render::isInvalid('collateral') }}"
                                               value="{{ old('collateral') }}"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-percent"
                                                                              style="font-size: 12px"></i></span>
                                        </div>
                                        {!! Render::errMsg('collateral') !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group input-group">
                                        <input name="collateral_amount" type="text" min="0" step="any"
                                               id="collateral_amount"
                                               class="form-control {{ Render::isInvalid('collateral') }}"
                                               value="{{ old('collateral_amount') }}"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-dollar-sign"
                                                                              style="font-size: 18px"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <!-- text input -->
                                    <label>
                                        Validity date:
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group input-group">
                                        <input name="validity_date" id="validity_date" type="text" autocomplete="off"
                                               class="form-control {{ Render::isInvalid('validity_date') }}"
                                               value="{{ old('validity_date') }}"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"
                                                                              style="font-size: 12px"></i></span>
                                        </div>
                                        {!! Render::errMsg('validity_date') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="commission">
                                    <span>Commission: <span id="commission_to_show">0</span></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-4 modal-btn">
                                    <!-- text input -->
                                    <button name="type" type="submit" class="btn btn-block btn-success btn-sm"
                                            id="no_buy_btn"
                                            value="{{ OrderTypes::Buy }}">
                                        Buy
                                    </button>
                                </div>
                                <div class="col-sm-4 modal-btn">
                                    <button name="type" type="submit" class="btn btn-block btn-danger btn-sm"
                                            id="no_sell_btn"
                                            value="{{ OrderTypes::Sell }}">
                                        Sell
                                    </button>
                                </div>
                                <div class="col-sm-2">
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- /.form-group -->
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="edit_unmatch">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body submit-order-modal">
                    <form role="form" id="edit_unmatch">
                        @csrf
                        <input type="hidden" value="{{ old('edit_order_id') }}" name="edit_order_id" id="edit_order_id">
                        <div class="form-group">
                            <select id="edit_product_id" name="edit_product_id" data-size="5"
                                    class="form-control w-100 selectpicker {{ Render::isInvalid('edit_product_id') }}"
                                    data-live-search="true">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ old('edit_product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span id="err_product_id" class="err-message" role="alert"></span>
                        </div>
                        <div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <!-- text input -->
                                    <label>
                                        Amount:
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group input-group">
                                        <input type="hidden" value="" name="old_edit_amount" id="old_edit_amount">
                                        <input name="edit_amount" type="text" min="0" step="any" id="edit_amount"
                                               class="form-control"
                                               value="{{ old('edit_amount') }}"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-dollar-sign"
                                                                              style="font-size: 18px"></i></span>
                                        </div>
                                        <span id="err_amount" class="err-message" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <!-- text input -->
                                    <div class="form-group modal-form-group">
                                        <label>
                                            Delivery Date:
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group input-group">
                                        <input name="edit_delivery_date" id="edit_delivery_date" type="text" autocomplete="off"
                                               class="form-control"
                                               value="{{ old('edit_delivery_date') }}"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"
                                                                              style="font-size: 12px"></i></span>
                                        </div>
                                        <span id="err_delivery_date" class="err-message" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <!-- text input -->
                                    <label>
                                        Collateral:
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group input-group">
                                        <input type="hidden" value="" name="old_edit_collateral"
                                               id="old_edit_collateral">
                                        <input name="edit_collateral" type="text" min="0" step="any" autocomplete="off"
                                               id="edit_collateral"
                                               class="form-control"
                                               value="{{ old('edit_collateral') }}"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-percent"
                                                                              style="font-size: 12px"></i></span>
                                        </div>
                                        <span id="err_collateral" class="err-message" role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group input-group">
                                        <input name="edit_collateral_amount" type="text" min="0" step="any"
                                               id="edit_collateral_amount"
                                               class="form-control"
                                               value="{{ old('edit_collateral_amount') }}"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-dollar-sign"
                                                                              style="font-size: 18px"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <!-- text input -->
                                    <label>
                                        Validity date:
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group input-group">
                                        <input name="edit_validity_date" id="edit_validity_date" type="text" autocomplete="off"
                                               class="form-control"
                                               value="{{ old('edit_validity_date') }}"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"
                                                                              style="font-size: 12px"></i></span>
                                        </div>
                                        <span id="err_validity_date" class="err-message" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="commission">
                                    <span>Commission: <span id="edit_commission_to_show">0</span></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-4 modal-btn">
                                    <!-- text input -->
                                    <button name="type" type="button" class="btn btn-block btn-success btn-sm"
                                            id="edit_no_buy_btn"
                                            value="{{ OrderTypes::Buy }}">
                                        Buy
                                    </button>
                                </div>
                                <div class="col-sm-4 modal-btn">
                                    <button name="type" type="button" class="btn btn-block btn-danger btn-sm"
                                            id="edit_no_sell_btn"
                                            value="{{ OrderTypes::Sell }}">
                                        Sell
                                    </button>
                                </div>
                                <div class="col-sm-2">
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- /.form-group -->
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--    <div class="modal fade" id="watch_list_orders">--}}
    {{--        <div class="modal-dialog">--}}
    {{--            <div class="modal-content">--}}
    {{--                <div class="modal-header">--}}
    {{--                    <h5 class="modal-title">Submit Order</h5>--}}
    {{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
    {{--                        <span aria-hidden="true">&times;</span>--}}
    {{--                    </button>--}}
    {{--                </div>--}}
    {{--                <div class="modal-body submit-order-modal">--}}
    {{--                    <form role="form" method="post" action="{{ route('orders.create') }}" id="create_order">--}}
    {{--                        @csrf--}}
    {{--                        <div class="form-group">--}}
    {{--                            <select name="product_id" class="form-control select2 {{ Render::isInvalid('product_id') }}" style="width: 100%;">--}}
    {{--                                @foreach($products as $product)--}}
    {{--                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>--}}
    {{--                                        {{ $product->name }}--}}
    {{--                                    </option>--}}
    {{--                                @endforeach--}}
    {{--                            </select>--}}
    {{--                            {!! Render::errMsg('product_id') !!}--}}
    {{--                        </div>--}}
    {{--                        <div>--}}
    {{--                            <div class="row">--}}
    {{--                                <div class="col-sm-4">--}}
    {{--                                    <!-- text input -->--}}
    {{--                                    <label>--}}
    {{--                                        Amount:--}}
    {{--                                    </label>--}}
    {{--                                </div>--}}
    {{--                                <div class="col-sm-8">--}}
    {{--                                    <div class="form-group input-group">--}}
    {{--                                        <input name="amount" type="text" min="0" step="any" id="amount"--}}
    {{--                                               class="form-control {{ Render::isInvalid('amount') }}"--}}
    {{--                                               value="{{ old('amount') }}"--}}
    {{--                                        >--}}
    {{--                                        <div class="input-group-append">--}}
    {{--                                            <span class="input-group-text"><i class="fas fa-dollar-sign" style="font-size: 18px"></i></span>--}}
    {{--                                        </div>--}}
    {{--                                        {!! Render::errMsg('amount') !!}--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="row">--}}
    {{--                                <div class="col-sm-4">--}}
    {{--                                    <!-- text input -->--}}
    {{--                                    <div class="form-group modal-form-group">--}}
    {{--                                        <label>--}}
    {{--                                            Delivery Date:--}}
    {{--                                        </label>--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                                <div class="col-sm-8">--}}
    {{--                                    <div class="form-group input-group">--}}
    {{--                                        <input name="delivery_date" id="delivery_date" type="text"--}}
    {{--                                               class="form-control {{ Render::isInvalid('delivery_date') }}"--}}
    {{--                                               value="{{ old('delivery_date') }}"--}}
    {{--                                        >--}}
    {{--                                        <div class="input-group-append">--}}
    {{--                                            <span class="input-group-text"><i class="far fa-calendar-alt" style="font-size: 12px"></i></span>--}}
    {{--                                        </div>--}}
    {{--                                        {!! Render::errMsg('delivery_date') !!}--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="row">--}}
    {{--                                <div class="col-sm-4">--}}
    {{--                                    <!-- text input -->--}}
    {{--                                    <label>--}}
    {{--                                        Collateral:--}}
    {{--                                    </label>--}}
    {{--                                </div>--}}
    {{--                                <div class="col-sm-8">--}}
    {{--                                    <div class="form-group input-group">--}}
    {{--                                        <input name="collateral" type="number" min="0" step="any"--}}
    {{--                                               class="form-control {{ Render::isInvalid('collateral') }}"--}}
    {{--                                               value="{{ old('collateral') }}"--}}
    {{--                                        >--}}
    {{--                                        <div class="input-group-append">--}}
    {{--                                            <span class="input-group-text"><i class="fas fa-percent" style="font-size: 12px"></i></span>--}}
    {{--                                        </div>--}}
    {{--                                        {!! Render::errMsg('collateral') !!}--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="row">--}}
    {{--                                <div class="col-sm-4">--}}
    {{--                                    <!-- text input -->--}}
    {{--                                    <label>--}}
    {{--                                        Validity date:--}}
    {{--                                    </label>--}}
    {{--                                </div>--}}
    {{--                                <div class="col-sm-8">--}}
    {{--                                    <div class="form-group input-group">--}}
    {{--                                        <input name="delivery_date" id="delivery_date" type="text"--}}
    {{--                                               class="form-control {{ Render::isInvalid('delivery_date') }}"--}}
    {{--                                               value="{{ old('delivery_date') }}"--}}
    {{--                                        >--}}
    {{--                                        <div class="input-group-append">--}}
    {{--                                            <span class="input-group-text"><i class="far fa-calendar-alt" style="font-size: 12px"></i></span>--}}
    {{--                                        </div>--}}
    {{--                                        --}}{{--{!! Render::errMsg('delivery_date') !!}--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="row">--}}
    {{--                                <div class="Commisson">--}}
    {{--                                    <span>Commission: <span id="commission">0</span></span>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                            <div class="row">--}}
    {{--                                <div class="col-md-12 modal-btn">--}}
    {{--                                    <!-- text input -->--}}
    {{--                                   <div id="watch-list-buy" class="watch-list-btn">--}}
    {{--                                       <button name="type" type="submit" class="btn btn-block btn-success btn-sm"--}}
    {{--                                               value="{{ OrderTypes::Buy }}">--}}
    {{--                                           Buy--}}
    {{--                                       </button>--}}
    {{--                                   </div>--}}
    {{--                                </div>--}}
    {{--                                <div class="col-md-12 modal-btn">--}}
    {{--                                   <div id="watch-list-sell" class="watch-list-btn">--}}
    {{--                                       <button name="type" type="submit" class="btn btn-block btn-danger btn-sm"--}}
    {{--                                               value="{{ OrderTypes::Sell }}">--}}
    {{--                                           Sell--}}
    {{--                                       </button>--}}
    {{--                                   </div>--}}
    {{--                                </div>--}}

    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                    </form>--}}
    {{--                    <!-- /.form-group -->--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--            <!-- /.modal-content -->--}}
    {{--        </div>--}}
    {{--        <!-- /.modal-dialog -->--}}
    {{--    </div>--}}
    @if ($errors->any())
        @php
            /*toast()->error('There was a problem creating new order. Please check your inputs')->persistent(false,false);*/
            toast()->error('There was a problem creating new order. Please check your inputs');
        @endphp
        <script>
            document.addEventListener("DOMContentLoaded", function (event) {
                handleButtonsOnNewOrder(localStorage.getItem('n_o_btn'));
                $('#submit_order').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                // $('#submit_order').modal('show');
                showCommission($('#commission_to_show'), $('#amount').val());
            });
        </script>
    @endif
@endsection
@section('js')
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>

    <script src="{{ asset('plugins/amcharts4/core.js') }}"></script>
    <script src="{{ asset('plugins/amcharts4/charts.js') }}"></script>
    <script src="{{ asset('plugins/amcharts4/themes/material.js') }}"></script>
    <script src="{{ asset('plugins/amcharts4/themes/animated.js') }}"></script>
    <script src="{{ asset('plugins/amcharts4/themes/dark.js') }}"></script>

    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
