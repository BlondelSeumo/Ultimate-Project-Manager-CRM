<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <a style="margin-top: -5px" href="<?= base_url() ?>admin/estimates/index/edit_estimates"
                   data-original-title="<?= lang('new_estimate') ?>" data-toggle="tooltip" data-placement="top"
                   class="btn btn-icon btn-<?= config_item('button_color') ?> btn-sm pull-right"><i
                            class="fa fa-plus"></i></a>
                <?= lang('all_estimates') ?>
            </div>
            <div class="panel-body">
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                         data-size="5px" data-color="#333333">
                        <ul class="nav"><?php

                            if (!empty($all_estimates_info)) {
                                foreach ($all_estimates_info as $key => $v_estimate) {
                                    if ($v_estimate->invoiced == 'Yes') {
                                        $invoice_status = strtoupper(lang('invoiced'));
                                        $label = 'success';
                                    } elseif ($v_estimate->emailed == 'Yes') {
                                        $invoice_status = strtoupper(lang('sent'));
                                        $label = 'info';
                                    } else {
                                        $invoice_status = lang($v_estimate->status);
                                        $label = 'default';
                                    }
                                    ?>
                                    <li class="<?php
                                    if ($v_estimate->estimates_id == $this->uri->segment(5)) {
                                        echo "active";
                                    }
                                    ?>">
                                        <a href="<?= base_url() ?>admin/estimates/index/email_estimates/<?= $v_estimate->estimates_id ?>">

                                            <?php if ($v_estimate->client_id == '0') { ?>
                                                <span class="label label-success">General Estimate</span>
                                                <?php
                                            } else {
                                                $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');
                                                ?>
                                                <?= ucfirst($client_info->name) ?>
                                            <?php } ?>
                                            <div class="pull-right">
                                                <?php $currency = $this->estimates_model->client_currency_symbol($estimates_info->client_id); ?>
                                                <?= display_money($this->estimates_model->estimate_calculation('estimate_amount', $estimates_info->estimates_id), $currency->symbol); ?>
                                            </div>
                                            <br>
                                            <small class="block small text-muted"><?= $v_estimate->reference_no ?> <span
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
            <div class="panel-heading"><?= lang('email_estimate') ?></div>
            <div class="panel-body">
                <form class="form-horizontal" method="post"
                      action="<?= base_url() ?>admin/estimates/send_estimates_email/<?= $estimates_info->estimates_id ?>">
                    <input type="hidden" name="ref" value="<?= $estimates_info->reference_no ?>">
                    <?php $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client'); ?>
                    <input type="hidden" name="client_name" value="<?= ucfirst($client_info->name) ?>">
                    <input type="hidden" name="currency" value="<?= $estimates_info->currency; ?>">

                    <input type="hidden" name="amount"
                           value="<?= ($this->estimates_model->estimate_calculation('total', $estimates_info->estimates_id)) ?>">

                    <div class="form-group">
                        <label class=" col-lg-1 control-label"><?= lang('subject') ?></label>
                        <div class="col-lg-7">
                            <?php
                            $email_template = email_templates(array('email_group' => 'estimate_email'), $estimates_info->client_id);
                            $message = $email_template->template_body;
                            $subject = $email_template->subject;
                            ?>
                            <input type="text" class="form-control"
                                   value="<?= $subject ?> <?= $estimates_info->reference_no ?>" name="subject">
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

