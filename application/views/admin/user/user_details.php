<?php
$activities_info = $this->db->where(array('user' => $profile_info->user_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();

$edited = can_action('24', 'edited');
$user_info = $this->db->where('user_id', $profile_info->user_id)->get('tbl_users')->row();
$designation = $this->db->where('designations_id', $profile_info->designations_id)->get('tbl_designations')->row();
if (!empty($designation)) {
    $department = $this->db->where('departments_id', $designation->departments_id)->get('tbl_departments')->row();
}
$all_project_info = $this->user_model->my_permission('tbl_project', $profile_info->user_id);
$p_started = 0;
$p_in_progress = 0;
$p_completed = 0;
$project_time = 0;
$project_time = $this->user_model->my_spent_time($profile_info->user_id, true);

if (!empty($all_project_info)) {
    foreach ($all_project_info as $v_user_project) {
        if ($v_user_project->project_status == 'started') {
            $p_started += count($v_user_project->project_status);
        }
        if ($v_user_project->project_status == 'in_progress') {
            $p_in_progress += count($v_user_project->project_status);
        }
        if ($v_user_project->project_status == 'completed') {
            $p_completed += count($v_user_project->project_status);
        }
    }
}

$tasks_info = $this->user_model->my_permission('tbl_task', $profile_info->user_id);

$t_not_started = 0;
$t_in_progress = 0;
$t_completed = 0;
$t_deferred = 0;
$t_waiting_for_someone = 0;
$task_time = 0;
$task_time = $this->user_model->my_spent_time($profile_info->user_id);
if (!empty($tasks_info)):foreach ($tasks_info as $v_tasks):
    if ($v_tasks->task_status == 'not_started') {
        $t_not_started += count($v_tasks->task_status);
    }
    if ($v_tasks->task_status == 'in_progress') {
        $t_in_progress += count($v_tasks->task_status);
    }
    if ($v_tasks->task_status == 'completed') {
        $t_completed += count($v_tasks->task_status);
    }

endforeach;
endif;
?>
<div class="unwrap">

    <div class="cover-photo bg-cover">
        <div class="p-xl text-white">

            <div class="row col-sm-4">
                <div class="row pull-left col-sm-6">
                    <div class=" row-table row-flush">
                        <div class="pull-left text-white ">
                            <div class="">
                                <h4 class="mt-sm mb0"><?php
                                    echo $p_in_progress + $p_started;
                                    ?>
                                </h4>
                                <p class="mb0 text-muted"><?= lang('open') . ' ' . lang('project') ?></p>
                                <small><a href="<?= base_url() ?>admin/projects"
                                          class="mt0 mb0"><?= lang('more_info') ?> <i
                                            class="fa fa-arrow-circle-right"></i></a></small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-lg row-table row-flush">

                        <div class="pull-left">
                            <div class="">
                                <h4 class="mt-sm mb0"><?php
                                    echo $p_completed;
                                    ?>
                                </h4>
                                <p class="mb0 text-muted"><?= lang('complete') . ' ' . lang('project') ?></p>
                                <small><a href="<?= base_url() ?>admin/projects/index/completed"
                                          class="mt0 mb0"><?= lang('more_info') ?> <i
                                            class="fa fa-arrow-circle-right"></i></a></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pull-right col-sm-6">
                    <div class=" row-table row-flush">

                        <div class="pull-left text-white ">
                            <div class="">
                                <h4 class="mt-sm mb0"><?php
                                    echo $t_in_progress + $t_not_started;
                                    ?>
                                </h4>
                                <p class="mb0 text-muted"><?= lang('open') . ' ' . lang('tasks') ?></p>
                                <small><a href="<?= base_url() ?>admin/tasks/all_task"
                                          class="mt0 mb0"><?= lang('more_info') ?> <i
                                            class="fa fa-arrow-circle-right"></i></a></small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-lg row-table row-flush">

                        <div class="pull-left">
                            <div class="">
                                <h4 class="mt-sm mb0"><?php
                                    echo $t_in_progress;
                                    ?>
                                </h4>
                                <p class="mb0 text-muted"><?= lang('complete') . ' ' . lang('tasks') ?></p>
                                <small><a href="<?= base_url() ?>admin/tasks/all_task/completed"
                                          class="mt0 mb0"><?= lang('more_info') ?><i
                                            class="fa fa-arrow-circle-right"></i></a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="text-center ">
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
                <p class="text-center"><?= lang('emp_id') ?>: <?php echo $profile_info->employment_id ?></p>
                <p class="text-center"><?php
                    if (!empty($department)) {
                        $dname = $department->deptname;
                    } else {
                        $dname = lang('undefined_department');
                    }
                    if (!empty($designation->designations)) {
                        $des = ' &rArr; ' . $designation->designations;
                    } else {
                        $des = '& ' . lang('designation');;
                    }
                    echo $dname . ' ' . $des;
                    if (!empty($department->department_head_id) && $department->department_head_id == $profile_info->user_id) { ?>
                        <strong
                            class="label label-warning"><?= lang('department_head') ?></strong>
                    <?php }
                    ?>

                </p>
            </div>
            <div class="col-sm-5">
                <div class="pull-left col-sm-6">
                    <div class=" row-table row-flush">
                        <div class="pull-left text-white ">
                            <div class="">
                                <h4 class="mt-sm mb0"><?php
                                    if (!empty($total_attendance)) {
                                        echo $total_attendance;
                                    } else {
                                        echo '0';
                                    }
                                    ?> / <?php echo $total_days; ?>
                                </h4>
                                <p class="mb0 text-muted"><?= lang('monthly') . ' ' . lang('attendance') ?></p>
                                <small><a href="<?= base_url() ?>admin/attendance/attendance_report"
                                          class="mt0 mb0"><?= lang('more_info') ?> <i
                                            class="fa fa-arrow-circle-right"></i></a></small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-lg row-table row-flush">

                        <div class="pull-left">
                            <div class="">
                                <h4 class="mt-sm mb0"><?php
                                    if (!empty($total_leave)) {
                                        echo $total_leave;
                                    } else {
                                        echo '0';
                                    }
                                    ?>
                                </h4>
                                <p class="mb0 text-muted"><?= lang('monthly') . ' ' . lang('leave') ?></p>
                                <small><a href="<?= base_url() ?>admin/leave_management"
                                          class="mt0 mb0"><?= lang('more_info') ?> <i
                                            class="fa fa-arrow-circle-right"></i></a></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pull-right col-sm-6">
                    <div class=" row-table row-flush">

                        <div class="pull-left text-white ">
                            <div class="">
                                <h4 class="mt-sm mb0"><?php
                                    if (!empty($total_absent)) {
                                        echo $total_absent;
                                    } else {
                                        echo '0';
                                    }
                                    ?>
                                </h4>
                                <p class="mb0 text-muted"><?= lang('monthly') . ' ' . lang('absent') ?></p>
                                <small><a href="<?= base_url() ?>admin/attendance/attendance_report"
                                          class="mt0 mb0"><?= lang('more_info') ?> <i
                                            class="fa fa-arrow-circle-right"></i></a></small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-lg row-table row-flush">

                        <div class="pull-left">
                            <div class="">
                                <h4 class="mt-sm mb0"><?php
                                    if (!empty($total_award)) {
                                        echo $total_award;
                                    } else {
                                        echo '0';
                                    }
                                    ?>
                                </h4>
                                <p class="mb0 text-muted"><?= lang('total') . ' ' . lang('award') ?></p>
                                <small><a href="<?= base_url() ?>admin/award"
                                          class="mt0 mb0"><?= lang('more_info') ?> <i
                                            class="fa fa-arrow-circle-right"></i></a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="text-center bg-gray-dark p-lg mb-xl">
        <div class="row row-table">
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
            <div class="col-xs-3 br user-timer">
                <h3 class="m0"><?= $this->user_model->get_time_spent_result($project_time) ?></h3>
                <p class="m0">
                    <span class="hidden-xs"><?= lang('project') . ' ' . lang('hours') ?></span>
                </p>
            </div>
            <div class="col-xs-3 br user-timer">
                <h3 class="m0"><?= $this->user_model->get_time_spent_result($task_time) ?></h3>
                <span class="hidden-xs"><?= lang('tasks') . ' ' . lang('hours') ?></span>
            </div>
            <div class="col-xs-3 br user-timer">
                <h3 class="m0"><?php
                    $m_min = 0;
                    $m_hour = 0;

                    if (!empty($this_month_working_hour)) {
                        foreach ($this_month_working_hour as $v_month_hour) {
                            $m_min += $v_month_hour['minute'];
                            $m_hour += $v_month_hour['hour'];
                        }
                    }
                    if ($m_min >= 60) {
                        $m_hour += intval($m_min / 60);
                        $m_min = intval($m_min % 60);
                    }
                    echo round($m_hour) . " : " . round($m_min) . " m";;
                    ?></h3>
                <span class="hidden-xs"><?= lang('this_month_working') . ' ' . lang('hours') ?></span>
            </div>
            <div class="col-xs-3 user-timer">
                <h3 class="m0"><?php
                    $min = 0;
                    $hour = 0;
                    if (!empty($all_working_hour)) {
                        foreach ($all_working_hour as $v_all_hours) {
                            $min += $v_all_hours['minute'];
                            $hour += $v_all_hours['hour'];

                        }
                    }
                    if ($min >= 60) {
                        $hour += intval($min / 60);
                        $min = intval($min % 60);
                    }
                    echo round($hour) . " : " . round($min) . " m";;
                    ?></h3>
                <span class="hidden-xs"><?= lang('working') . ' ' . lang('hours') ?></span>
            </div>
        </div>
    </div>

</div>
<?php include_once 'asset/admin-ajax.php'; ?>
<?= message_box('success'); ?>
<?= message_box('error'); ?>


<div class="row mt-lg">
    <div class="col-sm-3">
        <ul class="nav nav-pills nav-stacked navbar-custom-nav">

            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#basic_info"
                                                               data-toggle="tab"><?= lang('basic_info') ?></a></li>
            <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#bank_details"
                                                               data-toggle="tab"><?= lang('bank_details') ?></a>
            </li>
            <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#document_details"
                                                               data-toggle="tab"><?= lang('document_details') ?></a>
            </li>
            <li class="<?= $active == 5 ? 'active' : '' ?>"><a href="#salary_details"
                                                               data-toggle="tab"><?= lang('salary_details') ?></a>
            </li>
            <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#timecard_details"
                                                               data-toggle="tab"><?= lang('timecard_details') ?></a>
            </li>
            <li class="<?= $active == 7 ? 'active' : '' ?>"><a href="#leave_details"
                                                               data-toggle="tab"><?= lang('leave_details') ?></a>
            </li>
            <li class="<?= $active == 8 ? 'active' : '' ?>"><a href="#provident_found"
                                                               data-toggle="tab"><?= lang('provident_found') ?></a>
            </li>
            <li class="<?= $active == 9 ? 'active' : '' ?>"><a href="#Overtime_details"
                                                               data-toggle="tab"><?= lang('Overtime_details') ?></a>
            </li>
            <li class="<?= $active == 10 ? 'active' : '' ?>"><a href="#tasks_details"
                                                                data-toggle="tab"><?= lang('tasks') ?></a></li>
            <li class="<?= $active == 11 ? 'active' : '' ?>"><a href="#projects_details"
                                                                data-toggle="tab"><?= lang('projects') ?></a></li>
            <li class="<?= $active == 12 ? 'active' : '' ?>"><a href="#bugs_details"
                                                                data-toggle="tab"><?= lang('bugs') ?></a>
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
                            <?php if (!empty($edited)) { ?>
                                <div class="pull-right">
                                         <span data-placement="top" data-toggle="tooltip"
                                               title="<?= lang('update_conatct') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/user/update_contact/1/<?= $profile_info->account_details_id ?>"
                                               class="text-default text-sm ml"><?= lang('update') ?></a>
                                                </span>
                                </div>
                            <?php } ?>
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
                    <div class="panel-body form-horizontal table-responsive">
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
            <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="document_details"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?= lang('user_documents') ?>
                            <?php if (!empty($edited)) { ?>
                                <div class="pull-right hidden-print">
                                         <span data-placement="top" data-toggle="tooltip"
                                               title="<?= lang('update_conatct') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/user/user_documents/<?= $profile_info->user_id ?>"
                                               class="text-default text-sm ml"><?= lang('update') ?></a>
                                                </span>
                                </div>
                            <?php } ?>
                        </h4>
                    </div>
                    <div class="panel-body form-horizontal">
                        <!-- CV Upload -->
                        <?php
                        $document_info = $this->db->where('user_id', $profile_info->user_id)->get('tbl_employee_document')->row();
                        if (!empty($document_info->resume)): ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?= lang('resume') ?> : </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <a href="<?php echo base_url() . $document_info->resume; ?>"
                                           target="_blank"
                                           style="text-decoration: underline;"><?= lang('view') . ' ' . lang('resume') ?></a>
                                        <a href="<?= base_url('admin/user/delete_documents/resume/' . $document_info->document_id) ?>"
                                           class="btn btn-xs" title="" data-toggle="tooltip"
                                           data-placement="top"
                                           onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');"
                                           data-original-title="Delete"><i class="fa fa-trash-o"></i></a>
                                    </p>
                                </div>
                            </div>

                        <?php endif; ?>
                        <?php if (!empty($document_info->offer_letter)): ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?= lang('offer_latter') ?> : </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <a href="<?php echo base_url() . $document_info->offer_letter; ?>"
                                           target="_blank"
                                           style="text-decoration: underline;"><?= lang('view') . ' ' . lang('offer_latter') ?></a>
                                        <a href="<?= base_url('admin/user/delete_documents/offer_letter/' . $document_info->document_id) ?>"
                                           class="btn btn-xs" title="" data-toggle="tooltip"
                                           data-placement="top"
                                           onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');"
                                           data-original-title="Delete"><i class="fa fa-trash-o"></i></a>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($document_info->joining_letter)): ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?= lang('joining_latter') ?>
                                    : </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <a href="<?php echo base_url() . $document_info->joining_letter; ?>"
                                           target="_blank"
                                           style="text-decoration: underline;"><?= lang('view') . ' ' . lang('joining_latter') ?></a>
                                        <a href="<?= base_url('admin/user/delete_documents/joining_letter/' . $document_info->document_id) ?>"
                                           class="btn btn-xs" title="" data-toggle="tooltip"
                                           data-placement="top"
                                           onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');"
                                           data-original-title="Delete"><i class="fa fa-trash-o"></i></a>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($document_info->contract_paper)): ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?= lang('contract_paper') ?>
                                    : </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <a href="<?php echo base_url() . $document_info->contract_paper; ?>"
                                           target="_blank"
                                           style="text-decoration: underline;"><?= lang('view') . ' ' . lang('contract_paper') ?></a>
                                        <a href="<?= base_url('admin/user/delete_documents/contract_paper/' . $document_info->document_id) ?>"
                                           class="btn btn-xs" title="" data-toggle="tooltip"
                                           data-placement="top"
                                           onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');"
                                           data-original-title="Delete"><i class="fa fa-trash-o"></i></a>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($document_info->id_proff)): ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?= lang('id_prof') ?> : </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <a href="<?php echo base_url() . $document_info->id_proff; ?>"
                                           target="_blank"
                                           style="text-decoration: underline;"><?= lang('view') . ' ' . lang('id_prof') ?></a>
                                        <a href="<?= base_url('admin/user/delete_documents/id_proff/' . $document_info->document_id) ?>"
                                           class="btn btn-xs" title="" data-toggle="tooltip"
                                           data-placement="top"
                                           onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');"
                                           data-original-title="Delete"><i class="fa fa-trash-o"></i></a>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($document_info->other_document)): ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?= lang('other_document') ?>
                                    : </label>
                                <div class="col-sm-8">
                                    <?php
                                    $uploaded_file = json_decode($document_info->other_document);

                                    if (!empty($uploaded_file)):
                                        foreach ($uploaded_file as $sl => $v_files):

                                            if (!empty($v_files)):
                                                ?>
                                                <p class="form-control-static">
                                                    <a href="<?php echo base_url() . 'uploads/' . $v_files->fileName; ?>"
                                                       target="_blank"
                                                       style="text-decoration: underline;"><?= $sl + 1 . '. ' . lang('view') . ' ' . lang('other_document') ?></a>
                                                </p>
                                                <?php
                                            endif;
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 5 ? 'active' : '' ?>" id="salary_details"
                 style="position: relative;">
                <?php $this->load->view('admin/user/salary_details') ?>
            </div>
            <div class="tab-pane <?= $active == 6 ? 'active' : '' ?>" id="timecard_details"
                 style="position: relative;">
                <?php $this->load->view('admin/user/timecard_details') ?>
            </div>
            <div class="tab-pane <?= $active == 7 ? 'active' : '' ?>" id="leave_details"
                 style="position: relative;">
                <?php $this->load->view('admin/user/leave_details') ?>
            </div>
            <div class="tab-pane <?= $active == 8 ? 'active' : '' ?>" id="provident_found"
                 style="position: relative;">
                <?php $this->load->view('admin/user/provident_found') ?>
            </div>
            <div class="tab-pane <?= $active == 9 ? 'active' : '' ?>" id="Overtime_details"
                 style="position: relative;">
                <?php $this->load->view('admin/user/Overtime_details') ?>
            </div>
            <div class="tab-pane <?= $active == 10 ? 'active' : '' ?>" id="tasks_details"
                 style="position: relative;">
                <?php $this->load->view('admin/user/tasks_details') ?>
            </div>
            <div class="tab-pane <?= $active == 11 ? 'active' : '' ?>" id="projects_details"
                 style="position: relative;">
                <?php $this->load->view('admin/user/projects_details') ?>
            </div>

            <div class="tab-pane <?= $active == 12 ? 'active' : '' ?>" id="bugs_details"
                 style="position: relative;">
                <?php $this->load->view('admin/user/bugs_details') ?>
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
<?php
$color = array('37bc9b', '7266ba', 'f05050', 'ff902b', '7266ba', 'f532e5', '5d9cec', '7cd600', '91ca00', 'ff7400', '1cc200', 'bb9000', '40c400');
?>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.tooltip.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.resize.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.pie.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.time.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.categories.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.spline.min.js"></script>
