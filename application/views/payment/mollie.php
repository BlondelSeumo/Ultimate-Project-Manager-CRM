<?php
$allow_customer_edit_amount = config_item('allow_customer_edit_amount');
$attributes = array('class' => 'form-horizontal', 'data-parsley-validate' => "", 'novalidate' => "",);
echo form_open(base_url() . 'payment/mollie/pay/' . $invoice_info['item_number'], $attributes); ?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying
            <strong><?= display_money($invoice_info['amount'], $invoice_info['currency']); ?></strong> for Invoice
            #<?= $invoice_info['item_name'] ?> via Mollie</h4>
    </div>
    <div class="modal-body">

        <div id="payment-errors"></div>
        <input type="hidden" name="invoice_id" value="<?= $invoice_info['item_number'] ?>">
        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
            <input name="amount" value="<?= display_money($invoice_info['amount']) ?>" type="hidden">
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
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></a>
        <input type="submit" value="Procced Payment" class="btn btn-success"/>
    </div>
</div>
<?php echo form_close(); ?>

