<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <a style="margin-top: -5px" href="<?= base_url() ?>admin/estimates/index/edit_estimates"
                   data-original-title="<?= lang('new_estimate') ?>" data-toggle="tooltip" data-placement="top"
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
                                        <a href="<?= base_url() ?>admin/proposals/index/proposals_history/<?= $v_proposal->proposals_id ?>">
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
        <div class="row">

            <!-- Timeline START -->
            <section class="panel panel-custom">
                <div class="panel-body " id="chat-box">
                    <?php
                    $activities_info = $this->db->where(array('module' => 'proposals', 'module_field_id' => $proposals_info->proposals_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();
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