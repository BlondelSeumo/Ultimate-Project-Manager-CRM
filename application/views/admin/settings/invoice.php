<?php echo message_box('success') ?>
<div class="row">
    <!-- Start Form -->
    <div class="col-lg-12">
        <form action="<?php echo base_url() ?>admin/settings/save_invoice" enctype="multipart/form-data"
              class="form-horizontal" method="post">
            <div class="panel panel-custom">
                <header class="panel-heading  "><?= lang('invoice_settings') ?></header>
                <div class="panel-body">
                    <input type="hidden" name="settings" value="<?= $load_setting ?>">

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('invoice_prefix') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-7">
                            <input type="text" name="invoice_prefix" class="form-control" style="width:260px"
                                   value="<?= config_item('invoice_prefix') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('invoices_due_after') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-7">
                            <input type="text" name="invoices_due_after" class="form-control" style="width:260px"
                                   data-toggle="tooltip" data-placement="top" data-original-title="<?= lang('days') ?>"
                                   value="<?= config_item('invoices_due_after') ?>" required>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('invoice_start_no') ?> <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-7">
                            <input type="text" name="invoice_start_no" class="form-control" style="width:260px"
                                   value="<?= config_item('invoice_start_no') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('invoice') . ' ' . lang('number_format') ?></label>
                        <div class="col-lg-5">
                            <input type="text" name="invoice_number_format" class="form-control" style="width:260px"
                                   value="<?php
                                   if (empty(config_item('invoice_number_format'))) {
                                       echo '[' . config_item('invoice_prefix') . ']' . '[yyyy][mm][dd][number]';
                                   } else {
                                       echo config_item('invoice_number_format');
                                   } ?>">
                            <small>ex [<?= config_item('invoice_prefix') ?>] = <?= lang('invoice_prefix') ?>,[yyyy] =
                                'Current Year (<?= date('Y') ?>)'[yy] ='Current Year (<?= date('y') ?>)',[mm] =
                                'Current Month(<?= date('M') ?>)',[m] =
                                'Current Month(<?= date('m') ?>)',[dd] = 'Current Date (<?= date('d') ?>)',[number] =
                                'Invoice Number (<?= sprintf('%04d', config_item('invoice_start_no')) ?>)'
                            </small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('qty_calculation_from_items') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input value="Yes" type="checkbox" <?php
                                    if (config_item('qty_calculation_from_items') == 'Yes') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="qty_calculation_from_items">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('item_total_qty_alert') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input value="Yes" type="checkbox" <?php
                                    if (config_item('item_total_qty_alert') == 'Yes') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="item_total_qty_alert">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('amount_to_words') ?></label>
                        <div class="col-lg-7">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input value="Yes" type="checkbox" <?php
                                    if (config_item('amount_to_words') == 'Yes') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="amount_to_words">
                                    <span class="fa fa-check"></span>
                                </label>
                                <small><?= lang('output_total_amount') . ' ' . lang('in') . ' ' . lang('invoice') . ',' . lang('payments') . ',' . lang('estimate') . ',' . lang('proposal') . ' ' . lang('and') . ' ' . lang('purchase') ?></small>
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('amount_to_words_lowercase') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input value="Yes" type="checkbox" <?php
                                    if (config_item('amount_to_words_lowercase') == 'Yes') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="amount_to_words_lowercase">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('allow_customer_edit_amount') ?><i title=""
                                                                                                          class="fa fa-question-circle"
                                                                                                          data-toggle="tooltip"
                                                                                                          data-placement="top"
                                                                                                          data-original-title="<?= lang('allow_customer_edit_amount_help') ?>"></i></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input value="Yes" type="checkbox" <?php
                                    if (config_item('allow_customer_edit_amount') == 'Yes') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="allow_customer_edit_amount">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('increment_invoice_number') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="hidden" value="off" name="increment_invoice_number"/>
                                    <input type="checkbox" <?php
                                    if (config_item('increment_invoice_number') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="increment_invoice_number">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('show_item_tax') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="hidden" value="off" name="show_invoice_tax"/>
                                    <input type="checkbox" <?php
                                    if (config_item('show_invoice_tax') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="show_invoice_tax">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('invoice_view') ?></label>
                        <div class="col-lg-6">
                            <?php
                            $opt_inv = array(1 => lang('tax_invoice'), 0 => lang('standard'), 2 => lang('indian_gst'));
                            echo form_dropdown('invoice_view', $opt_inv, config_item('invoice_view'), 'class="form-control" required="required" id="invoice_view"');
                            ?>
                        </div>
                    </div>
                    <div class="form-group" id="states" style="display: none;">
                        <label class="col-lg-3 control-label"><?= lang('gst_state') ?></label>
                        <div class="col-lg-6">
                            <?php
                            $states = $this->gst->getIndianStates();
                            echo form_dropdown('gst_state', $states, config_item('gst_state'), 'class="form-control tip" required="required" id="state" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('send_email_when_recur') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                    <input type="hidden" value="off" name="send_email_when_recur"/>
                                    <input type="checkbox" <?php
                                    if (config_item('send_email_when_recur') == 'TRUE') {
                                        echo "checked=\"checked\"";
                                    }
                                    ?> name="send_email_when_recur">
                                    <span class="fa fa-check"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('invoice_logo') ?></label>
                        <div class="col-lg-7">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 210px;">
                                    <?php if (config_item('invoice_logo') != '') : ?>
                                        <img src="<?php echo base_url() . config_item('invoice_logo'); ?>">
                                    <?php else: ?>
                                        <img src="http://placehold.it/350x260" alt="Please Connect Your Internet">
                                    <?php endif; ?>
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 210px;"></div>
                                <div>
                                    <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">
                                            <input type="file" name="invoice_logo" value="upload"
                                                   data-buttonText="<?= lang('choose_file') ?>" id="myImg"/>
                                            <span class="fileinput-exists"><?= lang('change') ?></span>    
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists"
                                           data-dismiss="fileinput"><?= lang('remove') ?></a>

                                </div>

                                <div id="valid_msg" style="color: #e11221"></div>

                            </div>
                        </div>
                    </div>
                    <div class="form-group terms">
                        <label class="col-lg-3 control-label"><?= lang('default_terms') ?></label>
                        <div class="col-lg-9">
                        <textarea class="form-control textarea"
                                  name="default_terms"><?= config_item('default_terms') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group terms">
                        <label class="col-lg-3 control-label"><?= lang('invoice_footer') ?></label>
                        <div class="col-lg-9">
                        <textarea class="form-control textarea"
                                  name="invoice_footer"><?= config_item('invoice_footer') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3 control-label"></div>
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- End Form -->
</div>
<script>
    $(document).ready(function () {
        $('#invoice_view').change(function (e) {
            if ($(this).val() == 2) {
                $('#states').show();
            } else {
                $('#states').hide();
            }
        });
        if ($('#invoice_view').val() == 2) {
            $('#states').show();
        } else {
            $('#states').hide();
        }
    });
</script>