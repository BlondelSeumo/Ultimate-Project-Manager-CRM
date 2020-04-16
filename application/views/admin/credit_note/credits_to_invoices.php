<?php if ($credit_note->status == 'open') {
    $credit_remaining = $this->credit_note_model->credit_note_calculation('credit_remaining', $credit_note->credit_note_id);
    ?>
    <?php echo form_open(base_url('admin/credit_note/apply_credit_invoices/' . $credit_note->credit_note_id)); ?>
    <div class="panel panel-custom ">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <div class="panel-title">
                <?php echo lang('apply_credits_from', $credit_note->reference_no); ?>
            </div>
        </div>
        <div class="apply_credit_invoices" id="credit_apply" data-credit-remaining="<?php echo $credit_remaining; ?>">
            <?php if (!empty($all_invoices)) {
            $this->load->model('invoice_model');
            ?>
            <div class="table-responsive invoice-table">
                <table class="table table-striped DataTables" id="DataTables">
                    <thead>
                    <tr>
                        <th><?= lang('invoice') ?></th>
                        <th><?= lang('due_date') ?></th>
                        <th><?= lang('invoice_amount') ?></th>
                        <th><?= lang('due_amount') ?></th>
                        <th><?= lang('amount') . ' ' . ('to') . ' ' . ('credit') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($all_invoices as $v_invoices) {
                        $payment_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                        if ($payment_status != lang('fully_paid')) {
                            ?>
                            <tr>
                                <td>
                                    <a target="_blank"
                                       href="<?= base_url('admin/invoice/manage_invoice/invoice_details/' . $v_invoices->invoices_id) ?>"> <?= $v_invoices->reference_no ?></a>
                                </td>
                                <td><?= display_date($v_invoices->due_date) ?></td>
                                <td><?= display_money($this->invoice_model->calculate_to('invoice_cost', $v_invoices->invoices_id), client_currency($v_invoices->client_id)) ?></td>
                                <td><?= display_money($this->invoice_model->calculate_to('invoice_due', $v_invoices->invoices_id), client_currency($v_invoices->client_id)) ?></td>
                                <td>
                                    <input type="number" data-parsley-type="number"
                                           data-invoice-due="<?php echo $this->invoice_model->calculate_to('invoice_due', $v_invoices->invoices_id); ?>"
                                           name="amount[<?php echo $v_invoices->invoices_id; ?>]"
                                           class="form-control amount-credit-field" value="0">
                                    <div class="text-danger validate_error hidden"><?= lang('credit_amount_bigger_then_due_amount'); ?></div>
                                </td>
                            </tr>
                            <?php
                        }
                    }; ?>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-md-6 col-md-offset-6">
                    <div class="text-right">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td class="text-bold"><?php echo lang('added_into_payment'); ?>:</td>
                                <td>
                                    <div class="checkbox c-checkbox">
                                        <label>
                                            <input type="checkbox" checked class="custom-checkbox"
                                                   name="added_into_payment" id="use_postmark">
                                            <span class="fa fa-check"></span></label>
                                    </div>
                                </td>
                            </tr>
                            <tr class="postmark_config">
                                <td class="text-bold"><?php echo lang('send_email'); ?>:
                                </td>
                                <td>
                                    <div class="checkbox c-checkbox">
                                        <label>
                                            <input type="checkbox" checked class="custom-checkbox"
                                                   name="send_thank_you">
                                            <span class="fa fa-check"></span></label>
                                    </div>
                                </td>
                            </tr>
                            <tr class="postmark_config">
                                <td class="text-bold"><?php echo lang('send') . ' ' . lang('sms'); ?>:
                                </td>
                                <td>
                                    <div class="checkbox c-checkbox">
                                        <label>
                                            <input type="checkbox" checked class="custom-checkbox" name="send_sms">
                                            <span class="fa fa-check"></span></label>
                                    </div>
                                </td>
                            </tr>
                            <tr class="postmark_config">
                                <td class="text-bold"><?= lang('save_into_default_account') ?>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"
                                       title="<?= lang('will_be_added_into_deposit') ?>"></i>:
                                </td>
                                <td>
                                    <div class="checkbox c-checkbox">
                                        <label>
                                            <input type="checkbox" checked class="custom-checkbox"
                                                   name="save_into_account">
                                            <span class="fa fa-check"></span></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-bold"><?php echo lang('amount') . ' ' . ('to') . ' ' . ('credit'); ?>:
                                </td>
                                <td class="amount-credit">0</td>
                            </tr>
                            <tr>
                                <td class="text-bold"><?php echo lang('remaining') . ' ' . ('credit'); ?>:</td>
                                <td class="credit_remaining">
                                    <?php echo $credit_remaining; ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php } else { ?>
                    <p class="bold"><?php echo lang('nothing_to_display'); ?></p>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo lang('close'); ?></button>
                <?php if (count($all_invoices) > 0) { ?>
                    <button type="submit" class="btn btn-info amount_exceed"><?php echo lang('apply'); ?></button>
                <?php } ?>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
<?php } ?>
<script type="text/javascript">
    $('input[id="use_postmark"]').click(function () {
        if (this.checked) {
            $('.postmark_config').show();
        } else {
            $('.postmark_config').hide();
        }
    });
</script>