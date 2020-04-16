<?php
$cur = $this->invoice_model->check_by(array('code' => $invoice_info['currency']), 'tbl_currencies');
$allow_customer_edit_amount = config_item('allow_customer_edit_amount');
$client_info = $this->db->where('client_id', $invoice_info['client_id'])->get('tbl_client')->row();
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title">Paying <strong><?= display_money($invoice_info['amount'], $cur->symbol); ?></strong> for
            # <?= $invoice_info['item_name'] ?> via CCAvenue</h4>
    </div>
    <div class="modal-body">
        <form method="post" name="customerData" class="form-horizontal"
              action="<?= base_url('payment/ccavenue/confirm') ?>">

            <input type="hidden" name="tid" id="tid" value="<?= time() ?>"/>

            <input type="hidden" name="merchant_id" value="<?= config_item('ccavenue_merchant_id') ?>"/>

            <input type="hidden" name="order_id" value="<?= $invoice_info['item_number'] ?>"/>
            <?php if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') { ?>
                <input type="hidden" name="amount" value="<?= $invoice_info['amount'] ?>"/>
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
            <input type="hidden" name="currency" value="<?php echo $invoice_info['currency'] ?>"/>
            <input type="hidden" name="redirect_url"
                   value="<?php echo site_url('payment/ccavenue/invoice_success'); ?>"/>
            <input type="hidden" name="cancel_url"
                   value="<?php echo site_url('payment/ccavenue/invoice_failure'); ?>"/>
            <input type="hidden" name="language" value="EN"/>
            <?php
            if ($_POST) {
                $name = $_POST['billing_name'];
                $email = $_POST['billing_email'];
                $tel = $_POST['billing_tel'];
                $address = $_POST['billing_address'];
                $city = $_POST['billing_city'];
                $state = $_POST['billing_state'];
                $pcountry = $_POST['billing_country'];
                $zipcode = $_POST['billing_zip'];
            } else {
                $name = (!empty($client_info->name) ? $client_info->name : '');
                $email = (!empty($client_info->email) ? $client_info->email : '');
                $tel = (!empty($client_info->phone) ? $client_info->phone : '');
                $address = (!empty($client_info->address) ? $client_info->address : '');
                $city = (!empty($client_info->city) ? $client_info->city : '');
                $state = '';
                $pcountry = (!empty($client_info->country) ? $client_info->country : '');
                $zipcode = (!empty($client_info->zipcode) ? $client_info->zipcode : '');
            }
            ?>
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('name') ?> </label>
                <div class="col-lg-5">
                    <input type="text" name="billing_name" class="form-control"
                           value="<?php echo !empty($name) ? $name : ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('email') ?> </label>
                <div class="col-lg-5">
                    <input type="text" name="billing_email" class="form-control"
                           value="<?php echo !empty($email) ? $email : ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('phone') ?> </label>
                <div class="col-lg-5">
                    <input type="text" name="billing_tel" class="form-control"
                           value="<?php echo !empty($tel) ? $tel : ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label"><?= lang('address') ?> </label>
                <div class="col-lg-5">
                <textarea class="form-control"
                          name="billing_address"><?php echo !empty($address) ? $address : ''; ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <?php echo lang('city'); ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" name="billing_city"
                           value="<?php echo !empty($city) ? $city : ''; ?>" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <?php echo lang('state'); ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" name="billing_state" value="<?= (!empty($state) ? $state : '') ?>"
                           class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <?php echo lang('country'); ?>
                </label>
                <div class="col-lg-6">
                    <select name="billing_country" class="form-control">
                        <option value=""></option>
                        <?php
                        $countries = get_result('tbl_countries');
                        foreach ($countries as $country) {
                            $selected = '';
                            if (!empty($pcountry) && $pcountry == $country->value) {
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
                    <input type="text" name="billing_zip"
                           value="<?php echo !empty($zipcode) ? $zipcode : ''; ?>"
                           class="form-control">
                </div>
            </div>
            <?php
            $client_id = $this->session->userdata('client_id');
            if (!empty($client_id)) {
                $redirect = 'client/dashboard';
            } else {
                $redirect = 'frontend/view_invoice/' . url_encode($invoice_info['item_number']);
            }
            ?>
            <div class="modal-footer">
                <a href="<?= base_url($redirect) ?>" class="btn btn-default"
                   data-dismiss="modal"><?= lang('close') ?></a>
                <input type="submit" id="submit" value="<?= lang('submit') ?>" class="btn btn-success"/>
            </div>
        </form>
    </div>
</div>
