<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <a style="margin-top: -5px" href="<?= base_url() ?>admin/credit_note/index/edit_credit_note"
                   data-original-title="<?= lang('new_credit_note') ?>" data-toggle="tooltip" data-placement="top"
                   class="btn btn-icon btn-<?= config_item('button_color') ?> btn-sm pull-right"><i
                            class="fa fa-plus"></i></a>
                <?= lang('all_credit_note') ?>
            </div>
            <div class="panel-body">
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                         data-size="5px" data-color="#333333">
                        <ul class="nav"><?php
                            if (!empty($all_credit_note_info)) {
                                foreach ($all_credit_note_info as $key => $v_credit_note) {
                                    $invoice_status = lang($v_credit_note->status);
                                    $label = 'default';
                                    ?>
                                    <li class="<?php
                                    if ($v_credit_note->credit_note_id == $this->uri->segment(5)) {
                                        echo "active";
                                    }
                                    ?>">
                                        <a href="<?= base_url() ?>admin/credit_note/index/email_credit_note/<?= $v_credit_note->credit_note_id ?>">

                                            <?php if ($v_credit_note->client_id == '0') { ?>
                                                <span class="label label-success">General credit_note</span>
                                                <?php
                                            } else {
                                                $client_info = $this->credit_note_model->check_by(array('client_id' => $credit_note_info->client_id), 'tbl_client');
                                                ?>
                                                <?= ucfirst($client_info->name) ?>
                                            <?php } ?>
                                            <div class="pull-right">
                                                <?php $currency = $this->credit_note_model->client_currency_symbol($credit_note_info->client_id); ?>
                                                <?= display_money($this->credit_note_model->credit_note_calculation('credit_note_amount', $credit_note_info->credit_note_id), $currency->symbol); ?>
                                            </div>
                                            <br>
                                            <small class="block small text-muted"><?= $v_credit_note->reference_no ?>
                                                <span
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
            <div class="panel-heading"><?= lang('email_credit_note') ?></div>
            <div class="panel-body">
                <form class="form-horizontal" method="post"
                      action="<?= base_url() ?>admin/credit_note/send_credit_note_email/<?= $credit_note_info->credit_note_id ?>">
                    <input type="hidden" name="ref" value="<?= $credit_note_info->reference_no ?>">
                    <?php $client_info = $this->credit_note_model->check_by(array('client_id' => $credit_note_info->client_id), 'tbl_client'); ?>
                    <input type="hidden" name="client_name" value="<?= ucfirst($client_info->name) ?>">
                    <input type="hidden" name="currency" value="<?= $credit_note_info->currency; ?>">

                    <input type="hidden" name="amount"
                           value="<?= ($this->credit_note_model->credit_note_calculation('total', $credit_note_info->credit_note_id)) ?>">

                    <div class="form-group">
                        <label class=" col-lg-1 control-label"><?= lang('subject') ?></label>
                        <div class="col-lg-7">
                            <?php
                            $email_template = email_templates(array('email_group' => 'credit_note_email'));
                            $message = $email_template->template_body;
                            $subject = $email_template->subject;
                            ?>
                            <input type="text" class="form-control"
                                   value="<?= $subject ?> <?= $credit_note_info->reference_no ?>" name="subject">
                        </div>
                    </div>


                    <textarea name="message" class="form-control textarea_"><?= $message ?></textarea>

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

