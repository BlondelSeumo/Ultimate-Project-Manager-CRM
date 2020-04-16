<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('new_payment') ?></h4>
    </div>
    <div class="modal-body">
        <p><?= lang('paypal_redirection_alert') ?></p>

        <?php
        $attributes = array('name' => 'paypal_form', 'data-parsley-validate' => "", 'novalidate' => "", 'class' => 'bs-example form-horizontal');
        echo form_open($paypal_url, $attributes);

        $cur = $this->invoice_model->check_by(array('code' => $invoice_info['currency']), 'tbl_currencies');
        $allow_customer_edit_amount = config_item('allow_customer_edit_amount');
        ?>

        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
            <input name="amount" value="<?= ($invoice_info['amount']) ?>" type="hidden">
        <?php } ?>
        <input name="currency" value="<?= ($invoice_info['currency']) ?>" type="hidden">
        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('reference_no') ?></label>
            <div class="col-lg-4">
                <input type="text" class="form-control" readonly value="<?= $invoice_info['item_name'] ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('amount') ?> ( <?= $cur->symbol ?>) </label>
            <div class="col-lg-4">
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') { ?>
                    <input type="text" required name="amount" data-parsley-type="number"
                           data-parsley-max="<?= $invoice_info['amount'] ?>" class="form-control"
                           value="<?= ($invoice_info['amount']) ?>">
                <?php } else { ?>
                    <input type="text" class="form-control" value="<?= display_money($invoice_info['amount']) ?>"
                           readonly>
                <?php } ?>
            </div>
        </div>
        <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></a>
            <button type="submit" class="btn btn-success"><?= lang('pay_invoice') ?></button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<!-- /.modal-content -->