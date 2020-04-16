<?php
$cur = $this->invoice_model->check_by(array('code' => $invoice_info['currency']), 'tbl_currencies');
$allow_customer_edit_amount = config_item('allow_customer_edit_amount');
$client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info['client_id']), 'tbl_client');
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying <strong><?= display_money($invoice_info['amount'], $cur->symbol); ?> </strong>
            for Invoice
            # <?= $invoice_info['item_name'] ?> via Authorize.net</h4>
    </div>
    <div class="modal-body">
        <?php
        $attributes = array('id' => 'authorize_form', 'name' => 'authorize', 'data-parsley-validate' => "", 'novalidate' => "", 'class' => 'form-horizontal');
        echo form_open('payment/authorize/purchase', $attributes);
        ?>


        <div id="payment-errors"></div>
        <input type="hidden" name="invoice_id" value="<?= $invoice_info['item_number'] ?>">
        <input type="hidden" name="ref" value="<?= $invoice_info['item_name'] ?>">
        <input type="hidden" name="currency" value="<?= $invoice_info['currency'] ?>">
        <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
            <input name="amount" value="<?= ($invoice_info['amount']) ?>" type="hidden">
        <?php } ?>
        <input id="token" name="token" type="hidden" value="">
        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('amount') ?> ( <?= $cur->symbol ?>) </label>
            <div class="col-lg-4">
                <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') { ?>
                    <input type="text" required name="amount" data-parsley-type="number"
                           data-parsley-max="<?= $invoice_info['amount'] ?>" class="form-control"
                           value="<?= ($invoice_info['amount']) ?>">
                <?php } else { ?>
                    <input type="text" class="form-control" value="<?= ($invoice_info['amount']) ?>" readonly>
                <?php } ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('card_number') ?></label>
            <div class="col-lg-5">
                <input type="text" id="ccNo" name="ccNo" class="form-control card-number input-medium"
                       autocomplete="off" placeholder="" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('CVC') ?></label>
            <div class="col-lg-2">
                <input type="text" id="cvv" size="4" name="cvv" class="form-control card-cvc input-mini"
                       autocomplete="off"
                       placeholder="123" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label"><?= lang('expiration_card') ?></label>
            <div class="col-lg-2">
                <input type="text" maxlength="2" id="expMonth" name="expMonth" class="form-control input-mini"
                       autocomplete="off"
                       placeholder="MM" required>

            </div>
            <div class="col-lg-2">
                <input type="text" maxlength="4" id="expYear" name="expYear" autocomplete="off"
                       class="form-control input-mini"
                       placeholder="YYYY"
                       required>
            </div>
        </div>
        <div class="form-group mt-lg">
            <label class="col-lg-4 control-label" style="visibility: hidden"><?= lang('expiration_card') ?></label>
            <div class="col-lg-2">
                <strong class="bold bb strong"><?php echo lang('billing_address'); ?></strong>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo lang('email'); ?>
            </label>
            <div class="col-lg-6">
                <input type="email" name="email" class="form-control" required
                       value="<?php echo !empty($client_info->email) ? $client_info->email : ''; ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo lang('cardholder_name'); ?>
            </label>
            <div class="col-lg-6">
                <input type="text" name="billingName" class="form-control"
                       value="<?php echo !empty($client_info->name) ? $client_info->name : ''; ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo lang('billing_address'); ?>
            </label>
            <div class="col-lg-6">
                <input type="text" name="billingAddress1"
                       value="<?php echo !empty($client_info->address) ? $client_info->address : ''; ?>"
                       class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo lang('city'); ?>
            </label>
            <div class="col-lg-6">
                <input type="text" name="billingCity"
                       value="<?php echo !empty($client_info->city) ? $client_info->city : ''; ?>" class="form-control"
                       required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo lang('state'); ?>
            </label>
            <div class="col-lg-6">
                <input type="text" name="billingState"
                       class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo lang('country'); ?>
            </label>
            <div class="col-lg-6">
                <select name="billingCountry" class="form-control" required>
                    <option value=""></option>
                    <?php
                    $countries = get_result('tbl_countries');
                    foreach ($countries as $country) {
                        $selected = '';
                        if (!empty($client_info->country) && $client_info->country == $country->value) {
                            $selected = 'selected';
                        }
                        echo '<option ' . $selected . ' value="' . $country->value . '">' . $country->value . '</option>';
                    } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo lang('zip_code'); ?>
            </label>
            <div class="col-lg-6">
                <input type="text" value="<?php echo !empty($client_info->zipcode) ? $client_info->zipcode : ''; ?>"
                       name="billingPostcode"
                       class="form-control" required>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <a href="#" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></a>
        <input type="submit" value="<?= lang('submit') ?>" class="btn btn-success"/>
    </div>
    <?php echo form_close(); ?>
</div>

<script>
    $(function () {
        $('#authorize_form').validate();
    });
</script>

<!-- /.modal-content -->