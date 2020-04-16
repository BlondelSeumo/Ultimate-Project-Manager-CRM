<div class="row">    
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">  
                <a style="margin-top: -5px" href="<?= base_url() ?>admin/credit_note/index/edit_credit_note" data-original-title="<?= lang('new').' '.lang('credit_note') ?>" data-toggle="tooltip" data-placement="top" class="btn btn-icon btn-<?= config_item('button_color') ?> btn-sm pull-right"><i class="fa fa-plus"></i></a>
                <?= lang('all_credit_note') ?>
            </div>
            <div class="panel-body">    
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
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
        <div class="row">    

            <!-- Timeline START -->
            <section class="panel panel-custom">
                <div class="panel-body " id="chat-box">
                    <?php
                    $activities_info = $this->db->where(array('module' => 'credit_note', 'module_field_id' => $credit_note_info->credit_note_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();
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