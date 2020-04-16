<?php
if (!empty($estimate_to_merge) && count($estimate_to_merge) > 0) { ?>
    <div class="row">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?php echo lang('estimate_available_for_merging'); ?></strong>
                </div>
            </div>
            <div class="pl-lg">
                <?php foreach ($estimate_to_merge as $_inv) { ?>
                    <?php $currency = $this->estimates_model->client_currency_symbol($_inv->client_id);
                    if (empty($currency)) {
                        $currency = $this->estimates_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    }
                    ?>
                    <div class="checkbox mb0 mt-sm">
                        <i class="fa fa-hand-o-right text-danger pr-sm" aria-hidden="true"></i>
                        <label class="checkbox-inline c-checkbox">
                            <input type="checkbox" name="invoices_to_merge[]"
                                   value="<?php echo $_inv->estimates_id; ?>">
                            <span class="fa fa-check"></span><a
                                href="<?php echo base_url() . 'admin/estimates/index/estimates_details/' . $_inv->estimates_id; ?>"
                                data-toggle="tooltip" data-placement="top"
                                data-title="<?php echo lang($_inv->status); ?>"
                                target="_blank"><?php echo $_inv->reference_no; ?></a>
                            -<?= display_money($this->estimates_model->estimate_calculation('total', $_inv->estimates_id), $currency->symbol); ?>
                        </label>
                    </div>
                <?php } ?>
                <p>
                <div class="checkbox checkbox-info">
                    <label class="checkbox-inline c-checkbox">
                        <input type="checkbox" checked name="cancel_merged_estimate" id="cancel_merged_invoices">
                        <span class="fa fa-check"></span> <strong><?php echo lang('cancel_merged_estimate'); ?></strong>
                    </label>
                </div>
                </p>

            </div>
        </div>
    </div>
<?php } ?>
