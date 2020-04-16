<?php

$profile_info = get_staff_details($this->session->userdata('user_id'));
$activities_info = $this->db->where(array('user' => $profile_info->user_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();

$user_info = $this->db->where('user_id', $profile_info->user_id)->get('tbl_users')->row();
?>
<div class="unwrap">

    <div class="cover-photo bg-cover">
        <div class="p-xl text-white">
            <div class="text-center">
                <div class=" ">
                    <?php if ($profile_info->avatar): ?>
                        <img src="<?php echo base_url() . $profile_info->avatar; ?>"
                             class="img-thumbnail img-circle thumb128 ">
                    <?php else: ?>
                        <img src="<?php echo base_url() ?>assets/img/user/02.jpg" alt="Employee_Image"
                             class="img-thumbnail img-circle thumb128">
                        ;
                    <?php endif; ?>
                </div>

                <h3 class="m0 text-center"><?= $profile_info->fullname ?>
                </h3>
            </div>
        </div>

    </div>
    <div class="text-center bg-gray-dark p-lg mb-xl">
        <div class="row row-table">
            <?= lang('client').' '.lang('user')?>
            <style type="text/css">
                .user-timer ul.timer {
                    margin: 0px;
                }

                .user-timer ul.timer > li.dots {
                    padding: 6px 2px;
                    font-size: 14px;
                }

                .user-timer ul.timer li {
                    color: #fff;
                    font-size: 24px;
                    font-weight: bold;
                }

                .user-timer ul.timer li span {
                    display: none;
                }
            </style>

        </div>
    </div>

</div>


<div class="row mt-lg">
    <div class="col-sm-3">
        <ul class="nav nav-pills nav-stacked navbar-custom-nav">

            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#basic_info"
                                                               data-toggle="tab"><?= lang('basic_info') ?></a></li>
            <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#bank_details"
                                                               data-toggle="tab"><?= lang('bank_details') ?></a>
            </li>

            <li class="<?= $active == 13 ? 'active' : '' ?>" style="margin-right: 0px; "><a href="#activities"
                                                                                            data-toggle="tab"><?= lang('activities') ?>
                    <strong
                        class="pull-right"><?= (!empty($activities_info) ? count($activities_info) : null) ?></strong></a>
            </li>
        </ul>
    </div>
    <div class="col-sm-9">
        <div class="tab-content" style="border: 0;padding:0;">
            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="basic_info" style="position: relative;">
                <div class="panel panel-custom">
                    <!-- Default panel contents -->
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong><?= $profile_info->fullname ?></strong>

                        </div>
                    </div>
                    <div class="panel-body form-horizontal">
                        <div class="form-group mb0  col-sm-6">
                            <label class="control-label col-sm-5"><strong><?= lang('emp_id') ?>
                                    :</strong></label>
                            <div class="col-sm-7 ">
                                <p class="form-control-static"><?= $profile_info->employment_id ?></p>

                            </div>
                        </div>
                        <div class="form-group mb0  col-sm-6">
                            <label class="control-label col-sm-5"><strong><?= lang('fullname') ?>
                                    :</strong></label>
                            <div class="col-sm-7 ">
                                <p class="form-control-static"><?= $profile_info->fullname ?></p>

                            </div>
                        </div>
                        <?php if ($this->session->userdata('user_type') == 1) { ?>
                            <div class="form-group mb0  col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('username') ?>
                                        :</strong></label>
                                <div class="col-sm-7 ">
                                    <p class="form-control-static"><?= $user_info->username ?></p>

                                </div>
                            </div>
                            <div class="form-group mb0  col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('password') ?>
                                        :</strong></label>
                                <div class="col-sm-7 ">
                                    <p class="form-control-static"><a data-toggle="modal" data-target="#myModal"
                                                                      href="<?= base_url() ?>admin/user/reset_password/<?= $user_info->user_id ?>"><?= lang('reset_password') ?></a>
                                    </p>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group mb0  col-sm-6">
                            <label class="col-sm-5 control-label"><?= lang('joining_date') ?>: </label>
                            <div class="col-sm-7">
                                <?php if (!empty($profile_info->joining_date)) { ?>
                                    <p class="form-control-static"><?php echo strftime(config_item('date_format'), strtotime($profile_info->joining_date)); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group mb0  col-sm-6">
                            <label class="col-sm-5 control-label"><?= lang('gender') ?>:</label>
                            <div class="col-sm-7">
                                <?php if (!empty($profile_info->gender)) { ?>
                                    <p class="form-control-static"><?php echo lang($profile_info->gender); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group mb0  col-sm-6">

                            <label class="col-sm-5 control-label"><?= lang('date_of_birth') ?>: </label>
                            <div class="col-sm-7">
                                <?php if (!empty($profile_info->date_of_birth)) { ?>
                                    <p class="form-control-static"><?php echo strftime(config_item('date_format'), strtotime($profile_info->date_of_birth)); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group mb0  col-sm-6">
                            <label class="col-sm-5 control-label"><?= lang('maratial_status') ?>:</label>
                            <div class="col-sm-7">
                                <?php if (!empty($profile_info->maratial_status)) { ?>
                                    <p class="form-control-static"><?php echo lang($profile_info->maratial_status); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group mb0  col-sm-6">
                            <label class="col-sm-5 control-label"><?= lang('fathers_name') ?>: </label>
                            <div class="col-sm-7">
                                <?php if (!empty($profile_info->father_name)) { ?>
                                    <p class="form-control-static"><?php echo "$profile_info->father_name"; ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group mb0  col-sm-6">
                            <label class="col-sm-5 control-label"><?= lang('mother_name') ?>: </label>
                            <div class="col-sm-7">
                                <?php if (!empty($profile_info->mother_name)) { ?>
                                    <p class="form-control-static"><?php echo "$profile_info->mother_name"; ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group mb0  col-sm-6">
                            <label class="col-sm-5 control-label"><?= lang('email') ?> : </label>
                            <div class="col-sm-7">
                                <p class="form-control-static"><?php echo "$user_info->email"; ?></p>
                            </div>
                        </div>
                        <div class="form-group mb0  col-sm-6">
                            <label class="col-sm-5 control-label"><?= lang('phone') ?> : </label>
                            <div class="col-sm-7">
                                <p class="form-control-static"><?php echo "$profile_info->phone"; ?></p>
                            </div>
                        </div>
                        <div class="form-group mb0  col-sm-6">
                            <label class="col-sm-5 control-label"><?= lang('mobile') ?> : </label>
                            <div class="col-sm-7">
                                <p class="form-control-static"><?php echo "$profile_info->mobile"; ?></p>
                            </div>
                        </div>
                        <div class="form-group mb0  col-sm-6">
                            <label class="col-sm-5 control-label"><?= lang('skype_id') ?> : </label>
                            <div class="col-sm-7">
                                <p class="form-control-static"><?php echo "$profile_info->skype"; ?></p>
                            </div>
                        </div>
                        <?php if (!empty($profile_info->passport)) { ?>
                            <div class="form-group mb0  col-sm-6">
                                <label class="col-sm-5 control-label"><?= lang('passport') ?>
                                    : </label>
                                <div class="col-sm-7">
                                    <p class="form-control-static"><?php echo "$profile_info->passport"; ?></p>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group mb0  col-sm-6">
                            <label class="col-sm-5 control-label"><?= lang('present_address') ?>
                                : </label>
                            <div class="col-sm-7">
                                <p class="form-control-static"><?php echo "$profile_info->present_address"; ?></p>
                            </div>
                        </div>
                        <?php $show_custom_fields = custom_form_label(13, $profile_info->user_id);

                        if (!empty($show_custom_fields)) {
                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                if (!empty($v_fields)) {
                                    ?>
                                    <div class="form-group mb0  col-sm-6">
                                        <label class="col-sm-5 control-label"><?= $c_label ?> : </label>
                                        <div class="col-sm-7">
                                            <p class="form-control-static"><?= $v_fields ?></p>
                                        </div>
                                    </div>
                                <?php }
                            }
                        }
                        ?>

                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="bank_details" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?= lang('bank_information') ?>
                            <?php if (!empty($edited)) { ?>
                                <div class="pull-right hidden-print">
                                         <span data-placement="top" data-toggle="tooltip"
                                               title="<?= lang('new_bank') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/user/new_bank/<?= $profile_info->user_id ?>"
                                               class="text-default text-sm ml"><?= lang('update') ?></a>
                                                </span>
                                </div>
                            <?php } ?>
                        </h4>
                    </div>
                    <?php
                    $all_bank_info = $this->db->where('user_id', $profile_info->user_id)->get('tbl_employee_bank')->result();
                    ?>
                    <div class="panel-body form-horizontal">
                        <table class="table table-striped " cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th><?= lang('bank') ?></th>
                                <th><?= lang('name_of_account') ?></th>
                                <th><?= lang('routing_number') ?></th>
                                <th><?= lang('account_number') ?></th>
                                <th class="hidden-print"><?= lang('action') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($all_bank_info)) {
                                foreach ($all_bank_info as $v_bank_info) { ?>
                                    <tr>
                                        <td><?= $v_bank_info->bank_name ?></td>
                                        <td><?= $v_bank_info->account_name ?></td>
                                        <td><?= $v_bank_info->routing_number ?></td>
                                        <td><?= $v_bank_info->account_number ?></td>
                                        <td class="hidden-print">
                                            <?= btn_edit_modal('admin/user/new_bank/' . $v_bank_info->user_id . '/' . $v_bank_info->employee_bank_id) ?>
                                            <?= btn_delete('admin/user/delete_user_bank/' . $v_bank_info->user_id . '/' . $v_bank_info->employee_bank_id) ?>
                                        </td>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?= $active == 'notifications' ? 'active' : '' ?>" id="notifications">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title"><?= lang('all') . ' ' . lang('notification'); ?></div>
                    </div>
                    <div class="panel-body">

                        <table class="table" id="Transation_DataTables">
                            <thead>
                            <tr>
                                <th><a href="#"
                                       onclick="mark_all_as_read(); return false;"><?php echo lang('mark_all_as_read'); ?></a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $user_notifications = $this->global_model->get_user_notifications(false, true);
                            if (!empty($user_notifications)) {
                                foreach ($user_notifications as $notification) {
                                    if (!empty($notification->link)) {
                                        $link = base_url() . $notification->link;
                                    } else {
                                        $link = '#';
                                    }
                                    ?>
                                    <tr>
                                        <td class="<?php if ($notification->read_inline == 0) {
                                            echo 'unread';
                                        } ?>"><?php
                                            $description = lang($notification->description, $notification->value);
                                            if ($notification->from_user_id != 0) {
                                                $description = fullname($notification->from_user_id) . ' - ' . $description;
                                            }
                                            echo '<span class="n-title text-sm block">' . $description . '</span>'; ?>
                                            <small class="text-muted pull-left" style="margin-top: -4px"><i
                                                    class="fa fa-clock-o"></i> <?php echo time_ago($notification->date); ?>
                                            </small>
                                            <?php if ($notification->read_inline == 0) { ?>
                                                <span class="text-muted pull-right mark-as-read-inline"
                                                      onclick="read_inline(<?php echo $notification->notifications_id; ?>);"
                                                      data-placement="top"
                                                      data-toggle="tooltip"
                                                      data-title="<?php echo lang('mark_as_read'); ?>">
                                    <small><i class="fa fa-circle-thin"></i></small>
                                </span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 13 ? 'active' : '' ?>" id="activities"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title"><?= lang('all_activities'); ?></div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped" id="Transation_DataTables">
                            <thead>
                            <tr>
                                <th style="width: 200px"><?= lang('date') ?></th>
                                <th style="width: 10px"><?= lang('module') ?></th>
                                <th style="width: 500px"><?= lang('activity') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            if (!empty($activities_info)) {
                                foreach ($activities_info as $v_activity) {
                                    ?>
                                    <tr>
                                        <td><?= display_datetime($v_activity->activity_date); ?></td>
                                        <td><?= $this->db->where('user_id', $v_activity->user)->get('tbl_account_details')->row()->fullname; ?></td>
                                        <td><?= lang($v_activity->module) ?></td>
                                        <td>
                                            <?= lang($v_activity->activity) ?>
                                            <strong> <?= $v_activity->value1 . ' ' . $v_activity->value2 ?></strong>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#Transation_DataTables').dataTable({
            paging: false,
            "bSort": false
        });
    });
</script>
