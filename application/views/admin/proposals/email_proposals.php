<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <a style="margin-top: -5px" href="<?= base_url() ?>admin/proposals/index/edit_proposals"
                   data-original-title="<?= lang('new_proposal') ?>" data-toggle="tooltip" data-placement="top"
                   class="btn btn-icon btn-<?= config_item('button_color') ?> btn-sm pull-right"><i
                            class="fa fa-plus"></i></a>
                <?= lang('all_proposals') ?>
            </div>
            <div class="panel-body">
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                         data-size="5px" data-color="#333333">
                        <ul class="nav"><?php

                            if (!empty($all_proposals_info)) {
                                foreach ($all_proposals_info as $key => $v_proposal) {
                                    if ($v_proposal->convert == 'Yes') {
                                        if ($v_proposal->convert_module == 'estimate') {
                                            $status = strtoupper(lang('estimated'));
                                        } else {
                                            $status = strtoupper(lang('invoiced'));
                                        }
                                        $label = 'success';
                                    } elseif ($v_proposal->emailed == 'Yes') {
                                        $status = strtoupper(lang('sent'));
                                        $label = 'info';
                                    } else {
                                        $status = strtoupper(lang($v_proposal->status));
                                        $label = 'default';
                                    }
                                    if ($v_proposal->module == 'client') {
                                        $client_info = $this->proposal_model->check_by(array('client_id' => $v_proposal->module_id), 'tbl_client');
                                        if (!empty($client_info)) {
                                            $name = $client_info->name . ' ' . '[' . lang('client') . ']';;
                                        } else {
                                            $name = '-';
                                        }
                                        $currency = $this->proposal_model->client_currency_symbol($v_proposal->module_id);
                                    } else if ($v_proposal->module == 'leads') {
                                        $client_info = $this->proposal_model->check_by(array('leads_id' => $v_proposal->module_id), 'tbl_leads');
                                        if (!empty($client_info)) {
                                            $name = $client_info->lead_name . ' ' . '[' . lang('leads') . ']';
                                        } else {
                                            $name = '-';
                                        }
                                        $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                    } else {
                                        $name = '-';
                                        $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                    }
                                    ?>
                                    <li class="<?php
                                    if ($v_proposal->proposals_id == $this->uri->segment(5)) {
                                        echo "active";
                                    }
                                    ?>">
                                        <a href="<?= base_url() ?>admin/proposals/index/email_proposals/<?= $v_proposal->proposals_id ?>">
                                            <?= $name ?>
                                            <div class="pull-right">
                                                <?= display_money($this->proposal_model->proposal_calculation('total', $v_proposal->proposals_id), $currency->symbol); ?>
                                            </div>
                                            <br>
                                            <small class="block small text-muted"><?= $v_proposal->reference_no ?> <span
                                                        class="label label-<?= $label ?>"><?= $status ?></span>
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
            <div class="panel-heading"><?= lang('email_proposal') ?></div>
            <div class="panel-body">
                <form class="form-horizontal" method="post"
                      action="<?= base_url() ?>admin/proposals/send_proposals_email/<?= $proposals_info->proposals_id ?>">
                    <input type="hidden" name="ref" value="<?= $proposals_info->reference_no ?>">
                    <?php
                    if ($proposals_info->module == 'client') {
                        $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
                        $name = $client_info->name;
                        $currency = $this->proposal_model->client_currency_symbol($proposals_info->module_id);
                    } else if ($proposals_info->module == 'leads') {
                        $client_info = $this->proposal_model->check_by(array('leads_id' => $proposals_info->module_id), 'tbl_leads');
                        $name = $client_info->lead_name;
                        $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    } else {
                        $name = '-';
                        $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    }

                    ?>
                    <input type="hidden" name="client_name" value="<?= $name ?>">
                    <input type="hidden" name="currency" value="<?= $proposals_info->currency; ?>">

                    <input type="hidden" name="amount"
                           value="<?= display_money($this->proposal_model->proposal_calculation('total', $proposals_info->proposals_id), $currency->symbol) ?>">

                    <div class="form-group">
                        <label class=" col-lg-1 control-label"><?= lang('subject') ?></label>
                        <div class="col-lg-7">
                            <?php
                            $email_template = email_templates(array('email_group' => 'proposal_email'));
                            $message = $email_template->template_body;
                            $subject = $email_template->subject;
                            ?>
                            <input type="text" class="form-control"
                                   value="<?= $subject ?> <?= $proposals_info->reference_no ?>" name="subject">
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

