<?php echo message_box('success') ?>
<?php echo message_box('error') ?>
<?php
$allow_customer_edit_amount = config_item('allow_customer_edit_amount');
$invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');

$cur = $this->invoice_model->check_by(array('code' => $invoice_info->currency), 'tbl_currencies');
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying <strong>
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
                    <?= display_money($amount, $cur->symbol); ?>
                <?php } ?>
            </strong> for Invoice # <?= $invoice_info->reference_no ?> via <?= lang('tapPayment') ?></h4>
    </div>
    <div class="panel-body">
        <?php
        $attributes = array('id' => 'tappay', 'name' => 'tappayment', 'data-parsley-validate' => "", 'novalidate' => "", 'class' => 'form-horizontal');
        echo form_open($action_url, $attributes);
        ?>
        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
            <input name="amount" value="<?= ($amount) ?>" type="hidden">
        <?php } ?>


        <input type="hidden" name="key" value="<?php echo $key ?>"/>
        <input type="hidden" name="invoice_id" value="<?php echo $invoice_id ?>"/>

        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('amount') ?> ( <?= $cur->symbol ?>) </label>
            <div class="col-lg-5">
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') { ?>
                    <input type="text" required name="amount" id="mamount" data-parsley-type="number"
                           max="<?= $amount ?>" class="form-control"
                           value="<?= ($amount) ?>">
                    <input type="hidden" class="form-control" id="edit_amount" value=""
                           readonly>
                <?php } else { ?>
                    <input type="text" class="form-control" id="mamount" value="<?= display_money($amount) ?>"
                           readonly>
                <?php } ?>
            </div>
        </div>
        <div class="modal-footer">
            <a href="<?= base_url('checkoutPayment') ?>" class="btn btn-default"
               data-dismiss="modal"><?= lang('close') ?></a>
            <input type="submit" value="<?= lang('submit') ?>" class="btn btn-success"/>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

