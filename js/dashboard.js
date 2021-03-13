var refreshTables = true;
var watchListTableHash = null;
var positionTableHash = null;
var unmatchTableHash = null;
var historyTableHash = null;

$(document).ready(function () {
    var currentDate = new Date();

    $('#delivery_date').MonthPicker({
        Button: false,
        MonthFormat: 'yy-mm',
    });

    $('#validity_date').datepicker({
        dateFormat: "yy-mm-dd",
        beforeShow: function () {
            setTimeout(function () {
                $('.ui-datepicker').css('z-index', 99999999999999);
            }, 0);
        },
        showButtonPanel: true,
        minDate: new Date(currentDate.getFullYear(), currentDate.getMonth(), 1),
        maxDate: new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0),
    });

    $('.nav-tabs a').on('shown.bs.tab', function(event){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust()
            .responsive.recalc();
    });

    $('#amount').inputmask({
        alias: "currency",
        rightAlign: false,
        autoUnmask: true,
        removeMaskOnSubmit: true
    });

    $('#collateral_amount').inputmask({
        alias: "currency",
        rightAlign: false,
        autoUnmask: true,
        removeMaskOnSubmit: true,
    });

    // The only way to make collateral amount readonly using input mask
    $("#collateral_amount").focus(function(){
        $(this).blur();
    });

    $('#collateral').inputmask({
        alias: "decimal",
        rightAlign: false,
        autoUnmask: true,
        removeMaskOnSubmit: true,
        autoGroup: true,
        mask: "9{1,3}[.99]"
    });

    // Realtime showing commission on input
    $('#amount').on('input', function () {
        showCommission($('#commission_to_show'), this.value);
        showCollateralAmount(this.value, $('#collateral').val(), $('#collateral_amount'));
    });

    $('#collateral').on('input', function () {
        if (this.value > 100) {
            this.value = 100;
        }
        showCollateralAmount($('#amount').val(), this.value, $('#collateral_amount'));
    });

    $('#collateral_amount').on('input', function () {
        var amount = $('#amount').val();
        var collateral = $('#collateral');

        if (Number(this.value) > Number(amount)) {
            this.value = amount;
        }

        showCollateralPercent(amount, this.value, collateral);
    });

    // Edit unmatch elements
    $('#edit_delivery_date').MonthPicker({
        Button: false,
        MonthFormat: 'yy-mm',
    });


    $('#edit_validity_date').datepicker({
        dateFormat: "yy-mm-dd",
        beforeShow: function () {
            setTimeout(function () {
                $('.ui-datepicker').css('z-index', 99999999999999);
            }, 0);
        },
        showButtonPanel: true,
        minDate: new Date(currentDate.getFullYear(), currentDate.getMonth(), 1),
        maxDate: new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0),
    });

    $('#edit_amount').inputmask({
        alias: "currency",
        rightAlign: false,
        autoUnmask: true,
        removeMaskOnSubmit: true
    });

    $('#edit_collateral_amount').inputmask({
        alias: "currency",
        rightAlign: false,
        autoUnmask: true,
        removeMaskOnSubmit: true
    });

    // The only way to make collateral amount readonly using input mask
    $("#edit_collateral_amount").focus(function(){
        $(this).blur();
    });

    $('#edit_collateral').inputmask({
        alias: "decimal",
        rightAlign: false,
        autoUnmask: true,
        removeMaskOnSubmit: true,
        autoGroup: true,
        mask: "9{1,3}[.99]"
    });

    // Realtime showing commission on input
    $('#edit_amount').on('input', function () {
        showCommission($('#edit_commission_to_show'), this.value);
        showCollateralAmount(this.value, $('#edit_collateral').val(), $('#edit_collateral_amount'));
    });

    $('#edit_collateral').on('input', function () {
        if (this.value > 100) {
            this.value = 100;
        }
        showCollateralAmount($('#edit_amount').val(), this.value, $('#edit_collateral_amount'));
    });

    $('#edit_collateral_amount').on('input', function () {
        var edit_amount = $('#edit_amount').val();
        var edit_collateral = $('#edit_collateral');

        if (Number(this.value) > Number(edit_amount)) {
            this.value = edit_amount;
        }
        showCollateralPercent(edit_amount, this.value, edit_collateral);
    });

    $('#submit_order_btn').on('click', function () {
        resetNewOrderFrom();
        makeInputsReadOnly(false);
        localStorage.setItem('n_o_btn', '');
        handleButtonsOnNewOrder('all');

        $('#submit_order').modal({
            backdrop: 'static',
            keyboard: false
        });
        // $('#submit_order').modal('show');
    });

    getProductsByGroup('#group_select option:selected'); // first load products
    $('#group_select').on('change', function () {
        getProductsByGroup(this);
    });

    function getProductsByGroup(groupIdSelector) {
        var groupId = $(groupIdSelector).val();

        $.ajax({
            url: "/products/group-" + groupId,
            method: 'get',
            success: function (result) {
                $('#product_chart_select').html(result).selectpicker('refresh');
                getProductDetailChartData('#product_chart_select option:selected'); // first load chart
            }
        });
    }

    $('#product_chart_select').on('change', function () {
        getProductDetailChartData(this);
    });

    function getProductDetailChartData(productIdSelector) {
        $.ajax({
            url: "/products/chart/details",
            method: 'post',
            data: {
                product_id: $(productIdSelector).val()
            },
            success: function (result) {
                if (!$.trim(result)) {
                    $('#product_chart_message').html('No Data...').show();
                    $('#product_chart_div').hide();
                } else {
                    $('#product_chart_message').hide();
                    $('#product_chart_div').show();
                    chart.data = result;
                }
            }
        });
    }

    /* Continuously get new data from server */
    setInterval(function () {
        if (refreshTables) {

            $.post("/dashboard/watchlist", { wuh: true }, function(response) {
                if (response.data != watchListTableHash) {
                    $(".ui-tooltip").remove();
                    watchListTableHash = response.data;
                    watchListTable.ajax.reload(resetTableVisual);
                }
            });

            $.post("/dashboard/positions", { wuh: true }, function(response) {
                if (response.data != positionTableHash) {
                    $(".ui-tooltip").remove();
                    positionTableHash = response.data;
                    positionTable.ajax.reload(resetTableVisual);
                }
            });

            $.post("/dashboard/unmatch", { wuh: true }, function(response) {
                if (response.data != unmatchTableHash) {
                    $(".ui-tooltip").remove();
                    unmatchTableHash = response.data;
                    unmatchTable.ajax.reload(resetTableVisual);
                }
            });

            $.post("/dashboard/history", { wuh: true }, function(response) {
                if (response.data != historyTableHash) {
                    $(".ui-tooltip").remove();
                    historyTableHash = response.data;
                    historyTable.ajax.reload(resetTableVisual);
                }
            });

        }
    }, 15000);

    // Watch list datatable
    var watchListTable = $('#watch_list_table').DataTable({
        // "scrollY": 220,
        // "scrollX": true,
        // "scrollCollapse": true,
        "responsive": false,
        "paging": false,
        "bAutoWidth": false,
        "ajax": {
            "url": "/dashboard/watchlist",
            "type": "POST"
        },
        "columns": [
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    if (data.product.second_root) {
                        var text = truncate(data.product.second_root.name, 20);
                        var icon = productSymbols.find(element => element.name === data.product.second_root.name);

                        return `${(icon && icon.symbol) ? icon.symbol : ''}<span data-toggle="tooltip" title="${data.product.second_root.name}">${text}</span>`;
                    }

                    return 'N/A';
                }
            },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var text = truncate(data.product.name, 20);

                    return `<span data-toggle="tooltip" title="${data.product.name}">${text}</span>`;
                }
            },
            {
                "data": "delivery_date",
                "render": function (data, type, row) {
                    const date = new Date(data);
                    const year = new Intl.DateTimeFormat('en', {year: 'numeric'}).format(date);
                    const month = new Intl.DateTimeFormat('en', {month: 'short'}).format(date);
                    return month + ' ' + year;
                }
            },
            {"data": "casted_collateral"},
            {"data": "amount", "class": "js_currency"},
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var html = ``;

                    if (data.type === 1) {
                        html = `Buy<span><i class="fa fa-circle buy-circle"></i> </span>`;
                    } else if (data.type === 2) {
                        html = `Sell<span><i class="fa fa-circle sell-circle"></i> </span>`;
                    } else {
                        html = 'N/A';
                    }

                    return html;
                }
            },
            {
                "data": null,
                "render": function(data) {
                    var renderDate = parseISOStringToDate(data.created_at);
                    return renderDate.getFullYear() + '-' + renderDate.getMonth() + '-' + renderDate.getDate();
                }
            },
        ],
        // "columnDefs": [ {
        //     "searchable": false,
        //     "orderable": false,
        //     "targets": 0
        // } ],
        // "order": [[ 1, 'asc' ]],
        "initComplete": function () {
            resetTableVisual();
            $.post("/dashboard/watchlist", { wuh: true }, function(response) {
                watchListTableHash = response.data;
            });
        },
        "order": []
    });

    // Position datatable
    var positionTable = $('#position_table').DataTable({
        // "scrollY": 200,
        // "scrollX": true,
        // "scrollCollapse": true,
        "responsive": false,
        "paging": false,
        "bAutoWidth": false,
        "ajax": {
            "url": "/dashboard/positions",
            "type": "POST"
        },
        "columns": [
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    if (data.product.second_root) {
                        var text = truncate(data.product.second_root.name, 20);
                        var icon = productSymbols.find(element => element.name === data.product.second_root.name);

                        return `${(icon && icon.symbol) ? icon.symbol : ''}<span data-toggle="tooltip" title="${data.product.second_root.name}">${text}</span>`;
                    }

                    return 'N/A';
                }
            },
            {"data": "product.name"},
            {
                "data": null, "render": function (data, type, row, meta) {
                    return 'Matched';
                }
            },
            {"data": "amount", "class": "js_currency"},
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var html = ``;

                    if (data.type === 1) {
                        html = `Buy<span><i class="fa fa-circle buy-circle"></i> </span>`;
                    } else if (data.type === 2) {
                        html = `Sell<span><i class="fa fa-circle sell-circle"></i> </span>`;
                    } else {
                        html = 'N/A';
                    }

                    return html;
                }
            },
            {
                "data": "delivery_date",
                "render": function (data, type, row) {
                    const date = new Date(data);
                    const year = new Intl.DateTimeFormat('en', {year: 'numeric'}).format(date);
                    const month = new Intl.DateTimeFormat('en', {month: 'short'}).format(date);
                    return month + ' ' + year;
                }
            },
            /*{"data": "validity_date"},*/
            {"data": "casted_collateral"},
            {"data": "commission", "class": "js_currency"},
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var html = '';
                    percent = data.profit_loss_p_w_s;
                    amount = data.profit_loss;
                    if (parseFloat(percent) > 0) {
                        html += '<small class="text-success mr-1">';
                        html += '<i class="fas fa-arrow-up"></i> ';
                        html += percent + '%';
                        html += '</small>';
                        html += amount + ' CAD';
                        return html;
                    }

                    if (parseFloat(percent) < 0) {
                        html += '<small class="text-danger mr-1">';
                        html += '<i class="fas fa-arrow-down"></i> ';
                        html += percent + '%';
                        html += '</small>';
                        html += amount + ' CAD';
                        return html;
                    }

                    if (parseFloat(percent) == 0) {
                        html += '<small class="text-warning mr-1">';
                        html += '<i class="fas fa-arrow-right"></i> ';
                        html += percent + '%';
                        html += '</small>';
                        html += amount + ' CAD';
                        return html;
                    }

                    return html;
                }
            },
            {
                "data": null,
                "render": function(data) {
                    var renderDate = parseISOStringToDate(data.created_at);
                    return renderDate.getFullYear() + '-' + renderDate.getMonth() + '-' + renderDate.getDate();
                }
            },
        ],
        // "columnDefs": [ {
        //     "searchable": false,
        //     "orderable": false,
        //     "targets": 0
        // } ],
        "order": [],
        "initComplete": function () {
            $.post("/dashboard/positions", { wuh: true }, function(response) {
                positionTableHash = response.data;
            });
            resetTableVisual();
        }
    });

    // History datatable
    var unmatchTable = $('#unmatch_table').DataTable({
        // "scrollY": 200,
        // "scrollX": true,
        // "scrollCollapse": true,
        "responsive": false,
        "paging": false,
        "bAutoWidth": false,
        "ajax": {
            "url": "/dashboard/unmatch",
            "type": "POST"
        },
        "columns": [
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    if (data.product.second_root) {
                        var text = truncate(data.product.second_root.name, 20);
                        var icon = productSymbols.find(element => element.name === data.product.second_root.name);

                        return `${(icon && icon.symbol) ? icon.symbol : ''}<span data-toggle="tooltip" title="${data.product.second_root.name}">${text}</span>`;
                    }

                    return 'N/A';
                }
            },
            {"data": "product.name"},
            {
                "data": null, "render": function (data, type, row, meta) {
                    return 'Unmatched';
                }
            },
            {"data": "amount", "class": "js_currency"},
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var html = ``;

                    if (data.type === 1) {
                        html = `Buy<span><i class="fa fa-circle buy-circle"></i> </span>`;
                    } else if (data.type === 2) {
                        html = `Sell<span><i class="fa fa-circle sell-circle"></i> </span>`;
                    } else {
                        html = 'N/A';
                    }

                    return html;
                }
            },
            {
                "data": "delivery_date",
                "render": function (data, type, row) {
                    const date = new Date(data);
                    const year = new Intl.DateTimeFormat('en', {year: 'numeric'}).format(date);
                    const month = new Intl.DateTimeFormat('en', {month: 'short'}).format(date);
                    return month + ' ' + year;
                }
            },
            {"data": "validity_date"},
            {"data": "casted_collateral"},
            {"data": "commission", "class": "js_currency"},
            /*{
                "data": null,
                "render": function (data, type, row, meta) {
                    var html = '';
                    percent = data.profit_loss_p_w_s;
                    amount = data.profit_loss;
                    if (parseFloat(percent) > 0) {
                        html += '<small class="text-success mr-1">';
                        html += '<i class="fas fa-arrow-up"></i> ';
                        html += percent + '%';
                        html += '</small>';
                        html += amount + ' CAD';
                        return html;
                    }

                    if (parseFloat(percent) < 0) {
                        html += '<small class="text-danger mr-1">';
                        html += '<i class="fas fa-arrow-down"></i> ';
                        html += percent + '%';
                        html += '</small>';
                        html += amount + ' CAD';
                        return html;
                    }

                    if (parseFloat(percent) == 0) {
                        html += '<small class="text-warning mr-1">';
                        html += '<i class="fas fa-arrow-right"></i> ';
                        html += percent + '%';
                        html += '</small>';
                        html += amount + ' CAD';
                        return html;
                    }

                    return html;
                }
            },*/
            {
                "data": null,
                "render": function(data) {
                    var renderDate = parseISOStringToDate(data.created_at);
                    return renderDate.getFullYear() + '-' + renderDate.getMonth() + '-' + renderDate.getDate();
                }
            },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var html = `<button type="button" class="btn btn-outline-danger btn-sm table-btn" onclick="removeUnmatch(${data.id})">`;
                    html += `<i class="fa fa-trash"></i>`;
                    html += `</button>`;

                    html += `<button type="button" class="btn btn-outline-success btn-sm table-btn edit_btn">`;
                    html += `<i class="fa fa-pen"></i>`;
                    html += `</button>`;
                    return html;
                }
            }
        ],
        "order": [],
        "initComplete": function () {
            $.post("/dashboard/unmatch", { wuh: true }, function(response) {
                unmatchTableHash = response.data;
            });
            resetTableVisual();
        }
    });

    // History datatable
    var historyTable = $('#history_table').DataTable({
        // "scrollY": 200,
        // "scrollX": true,
        // "scrollCollapse": true,
        "responsive": false,
        "paging": false,
        "bAutoWidth": false,
        "ajax": {
            "url": "/dashboard/history",
            "type": "POST"
        },
        "columns": [
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    if (data.product.second_root) {
                        var text = truncate(data.product.second_root.name, 20);
                        var icon = productSymbols.find(element => element.name === data.product.second_root.name);

                        return `${(icon && icon.symbol) ? icon.symbol : ''}<span data-toggle="tooltip" title="${data.product.second_root.name}">${text}</span>`;
                    }

                    return 'N/A';
                }
            },
            {"data": "product.name"},
            {
                "data": null, "render": function (data, type, row, meta) {
                    return 'Delivered';
                }
            },
            {"data": "amount", "class": "js_currency"},
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var html = ``;

                    if (data.type === 1) {
                        html = `Buy<span><i class="fa fa-circle buy-circle"></i> </span>`;
                    } else if (data.type === 2) {
                        html = `Sell<span><i class="fa fa-circle sell-circle"></i> </span>`;
                    } else {
                        html = 'N/A';
                    }

                    return html;
                }
            },
            {
                "data": "delivery_date",
                "render": function (data, type, row) {
                    const date = new Date(data);
                    const year = new Intl.DateTimeFormat('en', {year: 'numeric'}).format(date);
                    const month = new Intl.DateTimeFormat('en', {month: 'short'}).format(date);
                    return month + ' ' + year;
                }
            },
            /*{"data": "validity_date"},*/
            {"data": "casted_collateral"},
            {"data": "commission", "class": "js_currency"},
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var html = '';
                    percent = data.profit_loss_p_b_o_d_d_w_s;
                    amount = data.profit_loss_b_o_d_d;
                    if (parseFloat(percent) > 0) {
                        html += '<small class="text-success mr-1">';
                        html += '<i class="fas fa-arrow-up"></i> ';
                        html += percent + '%';
                        html += '</small>';
                        html += amount + ' CAD';
                        return html;
                    }

                    if (parseFloat(percent) < 0) {
                        html += '<small class="text-danger mr-1">';
                        html += '<i class="fas fa-arrow-down"></i> ';
                        html += percent + '%';
                        html += '</small>';
                        html += amount + ' CAD';
                        return html;
                    }

                    if (parseFloat(percent) == 0) {
                        html += '<small class="text-warning mr-1">';
                        html += '<i class="fas fa-arrow-right"></i> ';
                        html += percent + '%';
                        html += '</small>';
                        html += amount + ' CAD';
                        return html;
                    }

                    return html;
                }
            },
            {
                "data": null,
                "render": function(data) {
                    var renderDate = parseISOStringToDate(data.created_at);
                    return renderDate.getFullYear() + '-' + renderDate.getMonth() + '-' + renderDate.getDate();
                }
            },
        ],
        "order": [],
        "initComplete": function () {
            resetTableVisual();
            $.post("/dashboard/history", { wuh: true }, function(response) {
                historyTableHash = response.data;
            });
        }
    });

    $(document).on("unmatchOrderRemoved", function (event, data) {
        unmatchTable.ajax.reload(resetTableVisual());
    });

    // Order with default values
    $('#watch_list_table').on('click', 'tbody tr', function () {
        resetNewOrderFrom();
        var userId = $('#user_id').val();
        var data = watchListTable.row(this).data();

        if (userId == data.user_id) return;

        $('#submit_order input[name=amount]').first().val(data.amount);
        $('#submit_order input[name=delivery_date]').first().val(data.delivery_date.substring(0, data.delivery_date.length - 3));
        $('#submit_order input[name=validity_date]').first().val(data.validity_date);
        $('#submit_order input[name=collateral]').first().val(data.collateral);
        $('#submit_order select[name=product_id]').first().val(data.product_id).selectpicker('refresh');
        var btn = data.type == 1 ? 'sell' : 'buy';
        handleButtonsOnNewOrder(btn);
        makeInputsReadOnly(true);
        showCommission($('#commission_to_show'), data.amount);
        showCollateralAmount(data.amount, data.collateral, $('#collateral_amount'));
        localStorage.setItem('n_o_btn', btn);

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        } else {
            watchListTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }

        $('#submit_order').modal({
            backdrop: 'static',
            keyboard: false
        });
        // $('#submit_order').modal('show');
    });

    //remove tr's background when clicked this buttons
    $("#submit_order .close").click(function () {
        $("#watch_list_table tr").removeClass("selected");
        makeInputsReadOnly(false);
    });
    $("#submit_order").on("hidden.bs.modal", function () {
        $("#watch_list_table tr").removeClass("selected");
        refreshTables = true;
    });
    $("#submit_order").on("show.bs.modal", function () {
        refreshTables = false;
    });
    $("#submit_order .btn-success").click(function () {
        $("#watch_list_table tr").removeClass("selected");
    });
    $("#submit_order .btn-danger").click(function () {
        $("#watch_list_table tr").removeClass("selected");
    });

    $('#unmatch_table').on('click', 'tbody tr td .edit_btn', function () {
        var data = unmatchTable.row($(this).closest('tr')).data();

        $('.err-message').empty();

        // Consider the locked amount for the order, within the limits of the user account balance
        $('#edit_unmatch input[name=old_edit_amount]').first().val(data.amount);
        $('#edit_unmatch input[name=old_edit_collateral]').first().val(data.collateral);

        $('#edit_unmatch input[name=edit_order_id]').first().val(data.id);
        $('#edit_unmatch input[name=edit_amount]').first().val(data.amount);
        $('#edit_unmatch input[name=edit_delivery_date]').first().val(data.delivery_date.substring(0, data.delivery_date.length - 3));
        $('#edit_unmatch input[name=edit_validity_date]').first().val(data.validity_date);
        $('#edit_unmatch input[name=edit_collateral]').first().val(data.collateral);
        $('#edit_unmatch select[name=edit_product_id]').first().val(data.product_id).selectpicker('refresh');
        var btn = data.type == 1 ? 'buy' : 'sell';
        handleButtonsOnEditOrder(btn);
        showCommission($('#edit_commission_to_show'), data.amount);
        showCollateralAmount(data.amount, data.collateral, $('#edit_collateral_amount'));
        localStorage.setItem('edit_n_o_btn', btn);

        if ($(this).closest('tr').hasClass('selected')) {
            $(this).closest('tr').removeClass('selected');
        } else {
            unmatchTable.$('tr.selected').removeClass('selected');
            $(this).closest('tr').addClass('selected');
        }

        $('#edit_unmatch').modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    //remove tr's background when clicked this buttons
    $("#edit_unmatch .close").click(function () {
        $("#unmatch_table tr").removeClass("selected");
    });
    $("#edit_unmatch").on("hidden.bs.modal", function () {
        refreshTables = true;
        $("#unmatch_table tr").removeClass("selected");
    });
    $("#edit_unmatch").on("show.bs.modal", function () {
        refreshTables = false;
    });
    $("#edit_unmatch .btn-success").click(function () {
        $("#unmatch_table tr").removeClass("selected");
    });
    $("#edit_unmatch .btn-danger").click(function () {
        $("#unmatch_table tr").removeClass("selected");
    });

    $("#edit_no_sell_btn, #edit_no_buy_btn").on("click", function () {
        var url = '/orders/' + $('#edit_order_id').val() + '/edit';

        var data = {};
        data.type = $(this).val();
        data.product_id = $('#edit_product_id').val();
        data.amount = $('#edit_amount').val();
        data.delivery_date = $('#edit_delivery_date').val();
        data.validity_date = $('#edit_validity_date').val();
        data.collateral = $('#edit_collateral').val();
        data.commission_to_show = $('#edit_commission_to_show').val();

        // Consider the locked amount for the order, within the limits of the user account balance
        data.oldAmount = $('#old_edit_amount').val();
        data.oldCollateral = $('#old_edit_collateral').val();

        $.ajax({
            url: url,
            method: 'patch',
            data: data,
            success: function (result) {
                if (result.success == true) {
                    $.event.trigger('unmatchOrderUpdated');

                    Swal.fire({
                        title: result.messageTitle,
                        text: result.messageBody,
                        icon: 'success',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.value) {
                            location.reload();
                        }
                    });

                } else {
                    Swal.fire({
                        title: result.messageTitle,
                        text: result.messageBody,
                        icon: 'error',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.value) {
                            location.reload();
                        }
                    });
                }
            },
            error: function (xhr) {
                $.each(xhr.responseJSON.errors, function (key, value) {
                    $('#err_' + key).text(value[0]);
                });
            }
        });
    });
});

function removeUnmatch(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "/orders/destroy-unmatch/" + id,
                method: 'get',
                success: function (result) {
                    if (result.success == true) {
                        $.event.trigger('unmatchOrderRemoved');
                        Swal.fire(result.messageTitle, result.messageBody, 'success');
                    } else {
                        Swal.fire(result.messageTitle, result.messageBody, 'error');
                    }
                }
            });
        }
    })
}

function resetNewOrderFrom() {
    $("#create_order input[name=amount]").val('');
    $("#create_order input[name=delivery_date]").val('');
    $("#create_order input[name=collateral]").val('');
    $("#create_order input[name=validity_date]").val('');
    $("#create_order select[name=product_id]").val(1);
    $("#create_order #commission_to_show").html('0');

    var orderForm = document.getElementById('create_order');
    orderForm.querySelectorAll('.form-control').forEach(jsContactInput => {
        jsContactInput.classList.remove('is-valid');
        jsContactInput.classList.remove('is-invalid');
        jsContactInput.addEventListener(('keyup'), () => {
            $('#phpMailerResult').hide();
        });
    });
}

function handleButtonsOnNewOrder(button) {
    var $buyBtn = $('#no_buy_btn');
    var $sellBtn = $('#no_sell_btn');

    switch (button) {
        case 'buy':
            $buyBtn.show();
            $sellBtn.hide();
            break;

        case 'sell':
            $buyBtn.hide();
            $sellBtn.show();
            break;

        case 'all':
            $buyBtn.show();
            $sellBtn.show();
            break;

        default:
            $buyBtn.show();
            $sellBtn.show();
            break;
    }
}


function handleButtonsOnEditOrder(button) {
    var $buyBtn = $('#edit_no_buy_btn');
    var $sellBtn = $('#edit_no_sell_btn');

    switch (button) {
        case 'buy':
            $buyBtn.show();
            $sellBtn.hide();
            break;

        case 'sell':
            $buyBtn.hide();
            $sellBtn.show();
            break;

        default:
            $buyBtn.hide();
            $sellBtn.hide();
            break;
    }
}

function makeInputsReadOnly(readOnly = false) {
    var $amount = $('#amount');
    var $collateral = $('#collateral');
    var $collateralAmount = $('#collateral_amount');
    var $createOrderForm = $('#create_order');
    var $validityDate = $('#validity_date');
    var $deliveryDate = $('#delivery_date');
    var $productSelect = $('#product_select');
    var $productSelectName = 'product_id';

    switch (readOnly) {
        case true:
            $amount.attr("readonly", true);
            $collateral.attr("readonly", true);
            $collateralAmount.attr("readonly", true);

            $deliveryDate.attr("readonly", true);
            $deliveryDate.MonthPicker({Disabled: true});
            $deliveryDate.attr("disabled", false);

            $validityDate.attr("readonly", true);
            $validityDate.datepicker("option", "disabled", true);
            $validityDate.attr("disabled", false);

            // Disable select and put its value on a hidden input
            var $hiddenInput = $('<input/>', {type: 'hidden', name: $productSelectName, value: $productSelect.val()});

            // Append the hidden field to the form
            $createOrderForm.append($hiddenInput);
            // Change name and disable
            $productSelect.attr("disabled", true);
            $productSelect.attr("name", $productSelectName + "_1");
            $productSelect.selectpicker('refresh');

            break;

        case false:
            $amount.attr("readonly", false);
            $collateral.attr("readonly", false);
            $collateralAmount.attr("readonly", false);

            $deliveryDate.attr("readonly", false);
            $deliveryDate.MonthPicker({Disabled: false});

            $validityDate.attr("readonly", false);
            $validityDate.datepicker("option", "disabled", false);

            // Remove the hidden fields if any
            $createOrderForm.find('input[type="hidden"][name=' + $productSelectName + ']').remove();
            // Restore the name and enable
            $productSelect.prop({name: $productSelectName, disabled: false});
            $productSelect.selectpicker('refresh');

            break;

        default:
            break;
    }
}

function productChartZoomAt(filter) {

    var start = new Date();
    var end = new Date();

    switch (filter) {
        case '1M':
            start = new Date(start.setMonth(start.getMonth() - 1));
            dateAxis.zoomToDates(start, end);
            break;

        case '3M':
            start = new Date(start.setMonth(start.getMonth() - 3));
            dateAxis.zoomToDates(start, end);
            break;

        case '6M':
            start = new Date(start.setMonth(start.getMonth() - 6));
            dateAxis.zoomToDates(start, end);
            break;

        case '1Y':
            start = new Date(start.setFullYear(start.getFullYear() - 1));
            dateAxis.zoomToDates(start, end);
            break;

        case '2Y':
            start = new Date(start.setFullYear(start.getFullYear() - 2));
            dateAxis.zoomToDates(start, end);
            break;

        case '3Y':
            start = new Date(start.setFullYear(start.getFullYear() - 3));
            dateAxis.zoomToDates(start, end);
            break;

        case '5Y':
            start = new Date(start.setFullYear(start.getFullYear() - 5));
            dateAxis.zoomToDates(start, end);
            break;

        case '10Y':
            start = new Date(start.setFullYear(start.getFullYear() - 10));
            dateAxis.zoomToDates(start, end);
            break;

        default:
            dateAxis.zoom({start: 0, end: 1});
            break;
    }

    //
    // dateAxis.zoomToDates(
    //     new Date(2018, 0, 2),
    //     new Date(2018, 0, 5)
    // );
}

// Product Chart
var chart;
var dateAxis;
var valueAxis;
am4core.ready(function () {
    am4core.useTheme(am4themes_dark);

    // Themes begin
    am4core.useTheme(am4themes_material);
    am4core.useTheme(am4themes_animated);
    // Themes end

    chart = am4core.create("product_chart_div", am4charts.XYChart);
    chart.logo.disabled = true;
    chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";
    chart.dateFormatter.dateFormat = "yyyy-MM-dd";

    // Create axes
    dateAxis = chart.xAxes.push(new am4charts.DateAxis());
    dateAxis.dateFormats.setKey("month", "MMM yyyy");
    // dateAxis.periodChangeDateFormats.setKey("month", "yyyy:MMM");

    dateAxis.renderer.minGridDistance = 60;

    valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

    // Change yAxes position to right
    valueAxis.renderer.opposite = true;

    // Create series
    var series = chart.series.push(new am4charts.LineSeries());
    series.dataFields.valueY = "value";
    series.dataFields.dateX = "date";
    series.tooltipText = "{value}";
    series.stroke = am4core.color("#007bff");
    series.fill = am4core.color("#007bff");
    series.fillOpacity = 0.6;

    var fillModifier = new am4core.LinearGradientModifier();
    fillModifier.opacities = [0.8, 0];
    fillModifier.offsets = [0, 0.8];
    fillModifier.gradient.rotation = 90;
    series.segments.template.fillModifier = fillModifier;

    series.tooltip.pointerOrientation = "vertical";

    chart.cursor = new am4charts.XYCursor();
    // chart.cursor.snapToSeries = series;
    chart.cursor.xAxis = dateAxis;
    chart.cursor.lineX.stroke = am4core.color("#fafbff");
    chart.cursor.lineX.strokeWidth = 1.5;
    chart.cursor.lineY.stroke = am4core.color("#fafbff");
    chart.cursor.lineY.strokeWidth = 1.5;
    chart.dataDateFormat = "YYYY-MM-DD";

    chart.scrollbarX = new am4core.Scrollbar();
    chart.scrollbarX.thumb.minWidth = 5;
    chart.scrollbarX.startGrip.icon.disabled = true;
    chart.scrollbarX.endGrip.icon.disabled = true;

}); // end am4core.ready()


function resetTableVisual() {
    currencyMask();
    $('[data-toggle="tooltip"]').tooltip();
}
