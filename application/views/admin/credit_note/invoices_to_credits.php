<?php if (!empty($all_open_credit)) {
    $this->load->model('credit_note_model');
    $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id);
    ?>
    <?php echo form_open(base_url('admin/invoice/apply_invoices_credit/' . $invoice_info->invoices_id)); ?>
    <div class="panel panel-custom ">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <div class="panel-title">
                <?php echo lang('apply_credits_from', $invoice_info->reference_no); ?>
            </div>
        </div>
        <div class="apply-invoice-credit" id="credit_apply" data-invoice-due="<?php echo $invoice_due; ?>">
            <?php if (!empty($all_open_credit)) {
            ?>
            <div class="table-responsive credit-table">
                <table class="table table-striped DataTables" id="DataTables">
                    <thead>
                    <tr>
                        <th><?= lang('credit_note') ?></th>
                        <th><?= lang('credit_note_date') ?></th>
                        <th><?= lang('credit').' '.lang('amount') ?></th>
                        <th><?= lang('credit').' '.lang('available') ?></th>
                        <th><?= lang('amount') . ' ' . ('to') . ' ' . ('credit') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($all_open_credit as $open_credit) {
                        $credit_remaining = $this->credit_note_model->credit_note_calculation('credit_remaining', $open_credit->credit_note_id);
                        ?>
                        <tr>
                            <td>
                                <a target="_blank"
                                   href="<?= base_url('admin/credit_note/index/credit_note_details/' . $open_credit->credit_note_id) ?>"> <?= $open_credit->reference_no ?></a>
                            </td>
                            <td><?= display_date($open_credit->credit_note_date) ?></td>
                            <td><?= display_money($this->credit_note_model->credit_note_calculation('total', $open_credit->credit_note_id), client_currency($open_credit->client_id)) ?></td>
                            <td><?= display_money($credit_remaining) ?></td>
                            <td>
                                <input type="number" data-parsley-type="number"
                                       data-credit-remaining="<?php echo $credit_remaining; ?>"
                                       name="amount[<?php echo $open_credit->credit_note_id; ?>]"
                                       class="form-control amount-credit-field" value="0">
                                <div class="text-danger validate_error hidden"><?= lang('credit_amount_bigger_then_due_amount'); ?></div>
                            </td>
                        </tr>
                        <?php
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
                                <td class="text-bold"><?php echo lang('invoice') . ' ' . ('due'); ?>:</td>
                                <td class="invoice_due">
                                    <?php echo $invoice_due; ?>
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
                <?php if (count($all_open_credit) > 0) { ?>
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