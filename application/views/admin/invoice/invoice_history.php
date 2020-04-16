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
                                        <a href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_invoices->invoices_id ?>">
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
        <div class="row">

            <!-- Timeline START -->
            <section class="panel panel-custom">

                <div class="panel-body " id="chat-box">
                    <?php
                    $activities_info = $this->db->where(array('module' => 'invoice', 'module_field_id' => $invoice_info->invoices_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();
                    if (!empty($activities_info)) {
                        foreach ($activities_info as $v_activities) {
                            $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();
                            $user_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_users')->row();
                            ?>
                            <div class="timeline-2">
                                <div class="time-item">
                                    <div class="item-info">
                                        <small data-toggle="tooltip" data-placement="top" title="<?= display_datetime($v_activities->activity_date)?>"
                                            class="text-muted"><?= time_ago($v_activities->activity_date); ?></small>

                                        <p><strong>
                                                <?php if (!empty($profile_info)) {
                                                    ?>
                                                    <a href="<?= base_url() ?>admin/user/user_details/<?= $profile_info->user_id ?>"
                                                       class="text-info"><?= $profile_info->fullname ?></a>
                                                <?php } ?>
                                            </strong> <?= sprintf(lang($v_activities->activity)) ?>
                                            <strong><?= $v_activities->value1 ?></strong>
                                            <?php if (!empty($v_activities->value2)){ ?>
                                        <p class="m0 p0"><strong><?= $v_activities->value2 ?></strong></p>
                                        <?php } ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                
            </section>
        </div>
    </section>
</div>


<!-- end -->