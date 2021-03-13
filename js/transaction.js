$(document).ready(function () {

    var is_a = false;
    var $transactionTable = $('#transactions_table');

    var transactionsTable = $transactionTable.DataTable( {
        "responsive": false,
        "paging": true,
        "bAutoWidth": false,
        "ajax": {
            "url": "/transactions/index",
            "type": "POST",
            "dataSrc": function ( response ) {
                is_a = response.is_admin;

                return response.data;
            }
        },
        "columns": [
            { "data": "id" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var html = ``;

                    if (is_a == true) {
                        html = `<a href="/users/${data.user.id}/edit">${data.user.name}</a>`;
                    } else if (is_a == false) {
                        html = data.user.name;
                    } else {
                        html = `N/A`;
                    }

                    return html;
                }
            },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    if (data.order && data.order.product && data.order.product.second_root) {
                        var text = truncate(data.order.product.second_root.name, 20);
                        var icon = productSymbols.find(element => element.name === data.order.product.second_root.name);

                        return `${(icon && icon.symbol) ? icon.symbol : ''}<span data-toggle="tooltip" title="${data.order.product.second_root.name}">${text}</span>`;
                    }

                    return 'N/A';
                }
            },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    if (data.order) {
                        return data.order.product.name;
                    }
                    return 'Deleted order';
                }
            },
            /*{
                "data": null,
                "render": {
                    _: function (data, type, row, meta) {
                        var html = ``;
                        if (data.type === 1) {
                            html = `<i class="fas fa-arrow-up text-success"></i>`;
                        } else if (data.type === 2) {
                            html = `<i class="fas fa-arrow-down text-danger"></i>`;
                        } else {
                            html = `N/A`;
                        }

                        return html;
                    },
                    sort: 'type'
                    ,
                }
            },*/
            {
                "data": null,
                "render" : function (data, type, row, meta) {
                    var html = ``;
                    if (data.type === 1) {
                        html = `<span class="text-success js_currency">${data.amount}</span>`;
                    } else if (data.type === 2) {
                        html = `<span class="text-danger js_currency">${data.amount}</span>`;
                    } else {
                        html = `N/A`;
                    }

                    return html;
                    return 'hey';
                }
            },
            { "data": "reason_label" },
            { "data": "created_at_string" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var html = ``;

                    if (data.type === 1) {
                        html = `<span><i class="fa fa-circle buy-circle"></i> </span>Increase`;
                    } else if (data.type === 2) {
                        html = `<span><i class="fa fa-circle sell-circle"></i> </span>Decrease`;
                    } else {
                        html = 'N/A';
                    }

                    return html;
                }
            },
        ],
        "order": [],
        "initComplete": function () {
            currencyMask();
        }
    });


    $transactionTable.on('click', 'tbody tr', function() {
        var data = transactionsTable.row(this).data();

        $.ajax({
            url: "/transactions/detail",
            method: 'post',
            data: {id: data.id},
            success: function(result) {
                if (result.success == true) {
                    var rData = result.data;

                    const date = new Date(rData.order.delivery_date);
                    const year = new Intl.DateTimeFormat('en', {year: 'numeric'}).format(date);
                    const month = new Intl.DateTimeFormat('en', {month: 'short'}).format(date);
                    rData.order.delivery_date = month + ' ' + year;

                    if (rData.order.deleted_at != null) {
                        $('#h6_deleted_order').html('Deleted Order');
                    } else {
                        $('#h6_deleted_order').html('');
                    }

                    if (rData.order.type === 1) {
                        $('#h5_transaction_type').html('Buy');
                        $('#h5_transaction_type').css('color', '#28a745');
                    } else if (rData.order.type === 2){
                        $('#h5_transaction_type').html('Sell');
                        $('#h5_transaction_type').css('color', '#dc3545');
                    } else {
                        $('#h5_transaction_type').html('N/A');
                        $('#h5_transaction_type').css('color', '#bec5cb');
                    }

                    $('#td_user_name').html(rData.user.name);
                    $('#td_date').html(rData.created_at_string);
                    $('#td_value').html(rData.amount + '<span> CAD</span>');
                    $('#td_product_group').html(rData.order.product.second_root.name);
                    $('#td_product_name').html(rData.order.product.name);
                    /*$('#td_reason').html(rData.reason_label);
                    $('#td_type').html(rData.type_label);*/
                    $('#td_type').html(rData.reason_label);
                    $('#td_order_amount').html(rData.order.amount + ' CAD');
                    $('#td_order_collateral').html(rData.order.casted_collateral);
                    $('#td_order_delivery').html(rData.order.delivery_date);
                    $('#td_order_validity').html(rData.order.validity_date);
                    currencyMask();
                    if ( $(this).hasClass('selected') ) {
                        $(this).removeClass('selected');
                    }
                    else {
                        transactionsTable.$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                    }
                    $('#transaction_detail_modal').modal('show');
                } else {
                    Swal.fire(result.messageTitle, result.messageBody, 'warning');
                }
            }
        });
    });

    // $('#min_amount').inputmask({
    //     alias: "currency",
    //     rightAlign: false,
    //     autoUnmask: true,
    //     removeMaskOnSubmit: true
    // });
    //
    // $('#max_amount').inputmask({
    //     alias: "currency",
    //     rightAlign: false,
    //     autoUnmask: true,
    //     removeMaskOnSubmit: true
    // });

    //select the tr when click on them
    //remove tr's background when clicked this buttons
    $( "#transaction_detail_modal .close" ).click(function() {
        $("#transactions_table tr").removeClass("selected");
    });
    $("#transaction_detail_modal").on("hidden.bs.modal", function () {
        $("#transactions_table tr").removeClass("selected");
    });
    $( "#transaction_detail_modal .btn-success" ).click(function() {
        $("#transactions_table tr").removeClass("selected");
    });
    $( "#transaction_detail_modal .btn-danger" ).click(function() {
        $("#transactions_table tr").removeClass("selected");
    });
});
