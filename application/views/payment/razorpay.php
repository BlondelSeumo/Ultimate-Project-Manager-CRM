<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying
            <strong><?= display_money($invoice_info['amount'], $invoice_info['currency']); ?></strong> for Invoice
            #<?= $invoice_info['item_name'] ?> via Razorpay</h4>
    </div>
    <div class="modal-body">
        <?php
        $allow_customer_edit_amount = config_item('allow_customer_edit_amount');
        ?>
        <div id="payment-errors"></div>
        <input type="hidden" name="invoice_id" value="<?= $invoice_info['item_number'] ?>">
        <input type="hidden" name="currency" value="<?= $invoice_info['currency'] ?>">
        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
            <input name="amount" id="amount" value="<?= $invoice_info['amount'] ?>" type="hidden">
        <?php } ?>
        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('amount') ?> ( <?= $invoice_info['currency'] ?>) </label>
            <div class="col-lg-4">
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') { ?>
                    <input type="text" id="r_amount" required name="amount" data-parsley-type="number"
                           data-parsley-max="<?= $invoice_info['amount'] ?>" class="form-control"
                           value="<?= ($invoice_info['amount']) ?>">
                <?php } else { ?>
                    <input type="text" class="form-control" value="<?= display_money($invoice_info['amount']) ?>"
                           readonly>
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <a href="#" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></a>
            <a href="javascript:void(0)" class="btn btn-sm btn-success" id="buy_now">Submit Payment</a>
        </div>
    </div>
</div>
<div id="loader-wrapper" style="display: none">
    <div id="loader"></div>
</div>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script type="text/javascript">
    var SITEURL = "<?php echo base_url() ?>";
    var encode_id = '<?= url_encode($invoice_info['item_number'])?>';
    $('body').on('click', '#buy_now', function (e) {
        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') { ?>
        var totalAmount = $.trim($("#r_amount").val());
        <?php }else{?>
        var totalAmount = "<?= ($invoice_info['amount']) ?>";
        <?php }?>
        var options = {
            "key": "<?= config_item('razorpay_key')?>",
            "amount": (totalAmount * 100), // 2000 paise = INR 20
            "name": '<?= config_item('company_name')?>',
            "description": "Invoice '<?= $invoice_info['item_name'] ?>' via Razorpay",
            "image": "<?= base_url() . config_item('company_logo')?>",
            "handler": function (response) {
                document.getElementById("loader-wrapper").style.display = "block";
                $('#loader-wrapper').delay(25000).fadeOut(function () {
                    $('#loader-wrapper').remove();
                });
                $.ajax({
                    url: SITEURL + 'payment/razorpay/invoice_success',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        razorpay_payment_id: response.razorpay_payment_id,
                        totalAmount: totalAmount,
                        invoice_id: "<?= $invoice_info['item_number'] ?>",
                    },
                    success: function (msg) {
                        toastr[msg.status](msg.message);
                        setTimeout(function () {
                            location.reload();
                        }, 5000);
                    }
                });

            },
            "theme": {
                "color": "#17759a"
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
        e.preventDefault();
    });

</script>