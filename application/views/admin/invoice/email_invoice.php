<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <a style="margin-top: -5px" href="<?= base_url() ?>admin/invoice/manage_invoice/create_invoice"
                   data-original-title="<?= lang('new_invoice') ?>" data-toggle="tooltip" data-placement="top"
                   class="btn btn-icon btn-<?= config_item('button_color') ?> btn-sm pull-right"><i
                            class="fa fa-plus"></i></a>
                <?= lang('all_invoices') ?>
            </div>
            <div class="panel-body">
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                         data-size="5px" data-color="#333333">
                        <ul class="nav"><?php
                            $all_invoices_info = $this->db->get('tbl_invoices')->result();
                            if (!empty($all_invoices_info)) {
                                foreach ($all_invoices_info as $v_invoices) {
                                    if ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                                        $invoice_status = lang('fully_paid');
                                        $label = "success";
                                    } elseif ($v_invoices->emailed == 'Yes') {
                                        $invoice_status = lang('sent');
                                        $label = "info";
                                    } else {
                                        $invoice_status = lang('draft');
                                        $label = "default";
                                    }
                                    ?>
                                    <li class="<?php
                                    if ($v_invoices->invoices_id == $this->uri->segment(5)) {
                                        echo "active";
                                    }
                                    ?>">
                                        <?php
                                        $client_info = $this->invoice_model->check_by(array('client_id' => $v_invoices->client_id), 'tbl_client');
                                        if (!empty($client_info)) {
                                            $client_name = $client_info->name;
                                            $currency = $this->invoice_model->client_currency_symbol($v_invoices->client_id);
                                        } else {
                                            $client_name = '-';
                                            $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                        }
                                        ?>
                                        <a href="<?= base_url() ?>admin/invoice/manage_invoice/email_invoice/<?= $v_invoices->invoices_id ?>">
                                            <?= $client_name ?>
                                            <div class="pull-right">
                                                <?= display_money($this->invoice_model->get_invoice_cost($v_invoices->invoices_id), $currency->symbol); ?>
                                            </div>
                                            <br>
                                            <small class="block small text-muted"><?= $v_invoices->reference_no ?> <span
                                                        class="label label-<?= $label ?>"><?= $invoice_status ?></span>
                                            </small>
                                        </a></li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <section class="col-sm-9">
        <div class="panel panel-custom">
            <div class="panel-heading"><?= lang('email_invoice') ?></div>
            <div class="panel-body">
                <form class="form-horizontal" method="post"
                      action="<?= base_url() ?>admin/invoice/send_invoice_email/<?= $invoice_info->invoices_id ?>">
                    <input type="hidden" name="ref" value="<?= $invoice_info->reference_no ?>">
                    <?php $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client'); ?>
                    <input type="hidden" name="client_name" value="<?= ucfirst($client_info->name) ?>">
                    <input type="hidden" name="currency" value="<?= $invoice_info->currency; ?>">

                    <input type="hidden" name="amount"
                           value="<?= ($this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id)) ?>">

                    <div class="form-group">
                        <label class=" col-lg-1 control-label"><?= lang('subject') ?></label>
                        <div class="col-lg-7">
                            <?php
                            $email_template = email_templates(array('email_group' => 'invoice_message'), $invoice_info->client_id);
                            $message = $email_template->template_body;
                            $subject = $email_template->subject;
                            ?>
                            <input type="text" class="form-control"
                                   value="<?= $subject ?> <?= $invoice_info->reference_no ?>" name="subject">
                        </div>
                    </div>


                    <textarea name="message" class="form-control" id="ck_editor"><?= $message ?></textarea>
                    <?php echo display_ckeditor($editor['ckeditor']); ?>

                    <div class="form-group">
                        <label class=" col-lg-1 control-label">
                            <button type="submit"
                                    class="submit btn btn-<?= config_item('button_color') ?>"><?= lang('send') ?></button>
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>    

