<?php
$cur = $this->invoice_model->check_by(array('code' => $invoice_info['currency']), 'tbl_currencies');
$allow_customer_edit_amount = config_item('allow_customer_edit_amount');
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying <strong>
                <?= display_money($invoice_info['amount'], $cur->symbol); ?>
            </strong> for Invoice # <?= $invoice_info['item_name'] ?> via Braintree</h4>
    </div>
    <div class="modal-body">
        <?php
        $attributes = array('id' => 'braintree_form', 'name' => 'braintree', 'data-parsley-validate' => "", 'novalidate' => "", 'class' => 'form-horizontal');
        echo form_open('payment/braintree/purchase', $attributes);
        ?>
        <div id="payment-errors"></div>
        <input type="hidden" name="invoice_id" value="<?= $invoice_info['item_number'] ?>">
        <input type="hidden" name="ref" value="<?= $invoice_info['item_name'] ?>">
        <input type="hidden" name="item_name" value="<?= $invoice_info['item_name'] ?>">
        <input type="hidden" name="currency" value="<?= $invoice_info['currency'] ?>">
        <input type="hidden" name="item_number" value="<?= $invoice_info['item_number'] ?>">
        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
            <input name="amount" value="<?= number_format($invoice_info['amount'], config_item('decimal_separator'), '.', '') ?>" type="hidden">
        <?php } ?>
        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('amount') ?> ( <?= $invoice_info['currency'] ?>) </label>
            <div class="col-lg-4">
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') { ?>
                    <input type="text" id="amount" required name="amount" data-parsley-type="number"
                           data-parsley-max="<?= $invoice_info['amount'] ?>" class="form-control"
                           value="<?= ($invoice_info['amount']) ?>">
                <?php } else { ?>
                    <input type="text" class="form-control" value="<?= display_money($invoice_info['amount']) ?>"
                           readonly>
                <?php } ?>
            </div>
        </div>
        <div class="form-group p-lg">
            <label class="col-lg-4 control-label"></label>
            <div class="col-lg-8">
                <section>
                    <div class="bt-drop-in-wrapper">
                        <div id="bt-dropin"></div>
                    </div>
                </section>
            </div>
        </div>
        <div class="text-center" style="margin-top:15px;">
            <button class="btn btn-info" type="submit"><?php echo lang('submit'); ?></button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script src="https://js.braintreegateway.com/js/braintree-2.30.0.min.js"></script>
<script>
    braintree.setup('<?php echo !empty($client_token) ? $client_token : ''; ?>', 'dropin', {
        container: 'bt-dropin'
    });
</script>
