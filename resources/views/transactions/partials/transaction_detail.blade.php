<div class="modal fade" id="transaction_detail_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <h5 class="modal-title transaction-type" id="h5_transaction_type"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width:50%">User:</th>
                                <td id="td_user_name"></td>
                            </tr>
                            <tr>
                                <th style="width:50%">Date:</th>
                                <td id="td_date"></td>
                            </tr>
                            <tr>
                                <th>Value:</th>
                                <td id="td_value"></td>
                            </tr>
                            <tr>
                                <th>Product Group:</th>
                                <td id="td_product_group"></td>
                            </tr>
                            <tr>
                                <th>Product:</th>
                                <td id="td_product_name"></td>
                            </tr>
                            {{--<tr>
                                <th>Reason:</th>
                                <td id="td_reason"></td>
                            </tr>--}}
                            <tr>
                                <th>Type:</th>
                                <td id="td_type"></td>
                            </tr>

                            <tr>
                                <th>Amount:</th>
                                <td id="td_order_amount"></td>
                            </tr>
                            <tr>
                                <th>Collateral:</th>
                                <td id="td_order_collateral"></td>
                            </tr>
                            <tr>
                                <th>Delivery Date:</th>
                                <td id="td_order_delivery"></td>
                            </tr>
                            <tr>
                                <th>Validity:</th>
                                <td id="td_order_validity"></td>
                            </tr>
                        </tbody>
                    </table>
                    <h6 class="delete-label" id="h6_deleted_order"></h6>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
