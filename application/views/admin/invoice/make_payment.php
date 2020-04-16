<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title"
            id="myModalLabel"><?= lang('make_payment') . ' ' . lang('for') . ' ' . lang('invoice') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">

        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-lg-3 control-label"><?= lang('select') . ' ' . lang('invoice') ?> <span
                        class="text-danger">*</span>
                </label>
                <div class="col-lg-7">
                    <select name="item_select" class="selectpicker m0" data-width="100%"
                            onchange="location = this.value;"
                            id="item_select"
                            data-none-selected-text="<?php echo lang('select') . ' ' . lang('invoice'); ?>"
                            data-live-search="true">
                        <option value=""></option>
                        <?php
                        if (!empty($all_invoices)) {
                            foreach ($all_invoices as $client_id => $v_invoices_items) {
                                $currency = $this->invoice_model->client_currency_symbol($client_id);
                                if (empty($currency)) {
                                    $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                }
                                ?>
                                <optgroup label="<?php echo client_name($client_id); ?>">
                                    <?php
                                    if (!empty($v_invoices_items)) {
                                        foreach ($v_invoices_items as $v_invoices) {
                                            $description = ' ' . lang('due_amount') . ' :  ' . display_money($this->invoice_model->calculate_to('invoice_due', $v_invoices->invoices_id), $currency->symbol);
                                            ?>
                                            <option
                                                value="<?php echo base_url('admin/invoice/manage_invoice/payment/' . $v_invoices->invoices_id); ?>"
                                                data-subtext="<?php echo strip_html_tags($description); ?>">
                                                (<?= display_money($this->invoice_model->calculate_to('total', $v_invoices->invoices_id), $currency->symbol); ?>
                                                ) <?php echo $v_invoices->reference_no; ?></option>
                                        <?php }
                                    }
                                    ?>
                                </optgroup>

                            <?php } ?>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('body').find('select.selectpicker').not('.ajax-search').selectpicker({
        showSubtext: true,
    });
    $(document).on('hide.bs.modal', '#myModal', function () {
        location.reload();
    });
</script>
