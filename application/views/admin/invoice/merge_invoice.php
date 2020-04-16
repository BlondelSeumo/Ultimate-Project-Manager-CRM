<?php
if (!empty($invoices_to_merge) && count($invoices_to_merge) > 0) { ?>
    <div class="row">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?php echo lang('invoices_available_for_merging'); ?></strong>
                </div>
            </div>
            <div class="pl-lg">
                <?php foreach ($invoices_to_merge as $_inv) { ?>
                    <?php $currency = $this->invoice_model->client_currency_symbol($_inv->client_id);
                    if (empty($currency)) {
                        $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    }
                    ?>
                    <div class="checkbox mb0 mt-sm">
                        <i class="fa fa-hand-o-right text-danger pr-sm" aria-hidden="true"></i>
                        <label class="checkbox-inline c-checkbox">
                            <input type="checkbox" name="invoices_to_merge[]" value="<?php echo $_inv->invoices_id; ?>">
                            <span class="fa fa-check"></span><a
                                href="<?php echo base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $_inv->invoices_id; ?>"
                                data-toggle="tooltip"
                                data-title="<?php echo $this->invoice_model->get_payment_status($_inv->invoices_id);; ?>"
                                target="_blank"><?php echo $_inv->reference_no; ?></a>
                            -
                            <strong><?php echo display_money($this->invoice_model->calculate_to('invoice_due', $_inv->invoices_id), $currency->symbol); ?></strong>
                        </label>
                    </div>
                    <?php if ($_inv->discount_total > 0) { ?>
                        <span style="margin-left: 21px;">
                        <?php echo lang('invoices_merge_discount', display_money($_inv->discount_total, $currency->symbol)) . '<br/>'; ?>
                        </span>
                    <?php } else { ?>
                        <span></span>
                    <?php } ?>
                <?php } ?>
                <p>
                <div class="checkbox checkbox-info">
                    <label class="checkbox-inline c-checkbox">
                        <input type="checkbox" checked name="cancel_merged_invoices" id="cancel_merged_invoices">
                        <span class="fa fa-check"></span> <strong><?php echo lang('invoices_merge_cancel'); ?></strong>
                    </label>
                </div>
                </p>

            </div>
        </div>
    </div>
<?php } ?>
