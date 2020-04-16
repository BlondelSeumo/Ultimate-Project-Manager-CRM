<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
    .note-editor .note-editable {
        height: 150px;
    }

    a:hover {
        text-decoration: none;
    }
</style>
<?php
$edited = can_action('54', 'edited');

$can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $task_details->task_id));
// get all comments by tasks id
$comment_details = $this->db->where(array('task_id' => $task_details->task_id, 'comments_reply_id' => '0', 'task_attachment_id' => '0', 'uploaded_files_id' => '0'))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
// get all $total_timer by tasks id
$total_timer = $this->db->where(array('task_id' => $task_details->task_id, 'start_time !=' => 0, 'end_time !=' => 0,))->get('tbl_tasks_timer')->result();
$all_sub_tasks = $this->db->where(array('sub_task_id' => $task_details->task_id))->get('tbl_task')->result();
$activities_info = $this->db->where(array('module' => 'tasks', 'module_field_id' => $task_details->task_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();

$where = array('user_id' => $this->session->userdata('user_id'), 'module_id' => $task_details->task_id, 'module_name' => 'tasks');
$check_existing = $this->tasks_model->check_by($where, 'tbl_pinaction');
if (!empty($check_existing)) {
    $url = 'remove_todo/' . $check_existing->pinaction_id;
    $btn = 'danger';
    $title = lang('remove_todo');
} else {
    $url = 'add_todo_list/tasks/' . $task_details->task_id;
    $btn = 'warning';
    $title = lang('add_todo_list');
}
$sub_tasks = config_item('allow_sub_tasks');
?>
<div class="row mt-lg">
    <div class="col-sm-3">
        <!-- Tabs within a box -->
        <ul class="nav nav-pills nav-stacked navbar-custom-nav">

            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_details"
                                                               data-toggle="tab"><?= lang('tasks') . ' ' . lang('details') ?></a>
            </li>
            <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#task_comments"
                                                               data-toggle="tab"><?= lang('comments') ?> <strong
                        class="pull-right"><?= (!empty($comment_details) ? count($comment_details) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#task_attachments"
                                                               data-toggle="tab"><?= lang('attachment') ?>
                    <strong
                        class="pull-right"><?= (!empty($project_files_info) ? count($project_files_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#task_notes"
                                                               data-toggle="tab"><?= lang('notes') ?></a></li>
            <li class="<?= $active == 5 ? 'active' : '' ?>"><a href="#timesheet"
                                                               data-toggle="tab"><?= lang('timesheet') ?><strong
                        class="pull-right"><?= (!empty($total_timer) ? count($total_timer) : null) ?></strong></a></li>
            <?php if (!empty($sub_tasks)) {
                ?>
                <li class="<?= $active == 7 ? 'active' : '' ?>"><a href="#sub_tasks"
                                                                   data-toggle="tab"><?= lang('sub_tasks') ?><strong
                            class="pull-right"><?= (!empty($all_sub_tasks) ? count($all_sub_tasks) : null) ?></strong></a>
                </li>
            <?php } ?>
            <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#activities"
                                                               data-toggle="tab"><?= lang('activities') ?><strong
                        class="pull-right"></strong><strong
                        class="pull-right"><?= (!empty($activities_info) ? count($activities_info) : null) ?></strong></a>
            </li>

        </ul>
    </div>
    <?php
    $comment_type = 'tasks';
    ?>
    <div class="col-sm-9">
        <div class="tab-content" style="border: 0;padding:0;">
            <!-- Task Details tab Starts -->
            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_details"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php if (!empty($task_details->task_name)) echo $task_details->task_name; ?>
                            <div class="pull-right ml-sm">
                                <a data-toggle="tooltip" data-placement="top" title="<?= $title ?>"
                                   href="<?= base_url() ?>admin/projects/<?= $url ?>"
                                   class="btn-xs btn btn-<?= $btn ?>"><i class="fa fa-thumb-tack"></i></a>
                            </div>
                            <div class="pull-right ml-sm">
                                <a data-toggle="tooltip" data-placement="top" title="<?= lang('export_report') ?>"
                                   href="<?= base_url() ?>admin/tasks/export_report/<?= $task_details->task_id ?>"
                                   class="btn-xs btn btn-success"><i class="fa fa-file-pdf-o"></i></a>
                            </div>
                            <?php

                            if (!empty($can_edit) && !empty($edited)) {
                                ?>
                                <span class="btn-xs pull-right"><a
                                        href="<?= base_url() ?>admin/tasks/all_task/<?= $task_details->task_id ?>"><?= lang('edit') . ' ' . lang('task') ?></a>
                                </span>
                            <?php } ?>


                        </h3>
                    </div>
                    <div class="panel-body form-horizontal task_details">
                        <?php $task_details_view = config_item('task_details_view');
                        if (!empty($task_details_view) && $task_details_view == '2') {
                            ?>
                            <div class="row">
                                <div class="col-md-3 br">
                                    <p class="lead bb"></p>
                                    <form class="form-horizontal p-20">
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('task_name') ?> :</strong></div>
                                            <div class="col-sm-8">
                                                <?php
                                                if (!empty($task_details->task_name)) {
                                                    echo $task_details->task_name;
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($task_details->project_id)):
                                            $project_info = $this->db->where('project_id', $task_details->project_id)->get('tbl_project')->row();
                                            $milestones_info = $this->db->where('milestones_id', $task_details->milestones_id)->get('tbl_milestones')->row();
                                            ?>
                                            <div class="form-group ">
                                                <div class="col-sm-4"><strong><?= lang('project_name') ?>
                                                        :</strong></div>
                                                <div class="col-sm-8 ">
                                                    <?php if (!empty($project_info->project_name)) echo $project_info->project_name; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-4"><strong><?= lang('milestone') ?>
                                                        :</strong></div>
                                                <div class="col-sm-8 ">
                                                    <?php if (!empty($milestones_info->milestone_name)) echo $milestones_info->milestone_name; ?>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php
                                        if (!empty($task_details->opportunities_id)):
                                            $opportunity_info = $this->db->where('opportunities_id', $task_details->opportunities_id)->get('tbl_opportunities')->row();
                                            ?>
                                            <div class="form-group">
                                                <div class="col-sm-4"><strong
                                                        class="mr-sm"><?= lang('opportunity_name') ?></strong></div>
                                                <div class="col-sm-8">
                                                    <?php if (!empty($opportunity_info->opportunity_name)) echo $opportunity_info->opportunity_name; ?>
                                                </div>
                                            </div>
                                        <?php endif ?>

                                        <?php
                                        if (!empty($task_details->leads_id)):
                                            $leads_info = $this->db->where('leads_id', $task_details->leads_id)->get('tbl_leads')->row();
                                            ?>
                                            <div class="form-group">
                                                <div class="col-sm-4"><strong
                                                        class="mr-sm"><?= lang('leads_name') ?></strong></div>
                                                <div class="col-sm-8">
                                                    <?php if (!empty($leads_info->lead_name)) echo $leads_info->lead_name; ?>
                                                </div>
                                            </div>
                                        <?php endif ?>

                                        <?php
                                        if (!empty($task_details->bug_id)):
                                            $bugs_info = $this->db->where('bug_id', $task_details->bug_id)->get('tbl_bug')->row();
                                            ?>
                                            <div class="form-group">
                                                <div class="col-sm-4"><strong
                                                        class="mr-sm"><?= lang('bug_title') ?></strong></div>
                                                <div class="col-sm-8">
                                                    <?php if (!empty($bugs_info->bug_title)) echo $bugs_info->bug_title; ?>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php
                                        if (!empty($task_details->goal_tracking_id)):
                                            $goal_tracking_info = $this->db->where('goal_tracking_id', $task_details->goal_tracking_id)->get('tbl_goal_tracking')->row();
                                            ?>
                                            <div class="form-group">
                                                <div class="col-sm-4"><strong
                                                        class="mr-sm"><?= lang('goal_tracking') ?></strong></div>
                                                <div class="col-sm-8">
                                                    <?php if (!empty($goal_tracking_info->subject)) echo $goal_tracking_info->subject; ?>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php
                                        if (!empty($task_details->sub_task_id)):
                                            $sub_task = $this->db->where('task_id', $task_details->sub_task_id)->get('tbl_task')->row();
                                            ?>
                                            <div class="form-group">
                                                <div class="col-sm-4"><strong
                                                        class="mr-sm"><?= lang('sub_tasks') ?></strong></div>
                                                <div class="col-sm-8">
                                                    <?php if (!empty($sub_task->task_name)) echo $sub_task->task_name; ?>
                                                </div>
                                            </div>
                                        <?php endif ?>

                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('start_date') ?> :</strong></div>
                                            <div class="col-sm-8">
                                                <?php
                                                if (!empty($task_details->task_start_date)) {
                                                    echo strftime(config_item('date_format'), strtotime($task_details->task_start_date));
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                        $due_date = $task_details->due_date;
                                        $due_time = strtotime($due_date);
                                        $current_time = strtotime(date('Y-m-d'));
                                        if ($current_time > $due_time && $task_details->task_status != 'completed') {
                                            $text = 'text-danger';
                                        } else {
                                            $text = null;
                                        }
                                        ?>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong class="<?= $text ?>"><?= lang('due_date') ?>
                                                    :</strong></div>
                                            <div class="col-sm-8 <?= $text ?>">
                                                <?php
                                                if (!empty($task_details->due_date)) {
                                                    echo strftime(config_item('date_format'), strtotime($task_details->due_date));
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('task_status') ?>
                                                    :</strong></div>
                                            <div class="col-sm-8">
                                                <?php
                                                $disabled = null;
                                                if ($task_details->task_status == 'completed') {
                                                    $label = 'success';
                                                    $disabled = 'disabled';
                                                } elseif ($task_details->task_status == 'not_started') {
                                                    $label = 'info';
                                                } elseif ($task_details->task_status == 'deferred') {
                                                    $label = 'danger';
                                                } else {
                                                    $label = 'warning';
                                                }
                                                ?>
                                                <div
                                                    class="label label-<?= $label ?>  "><?= lang($task_details->task_status) ?></div>
                                                <?php
                                                ?>
                                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs btn-success dropdown-toggle"
                                                                data-toggle="dropdown">
                                                            <?= lang('change') ?>
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu animated zoomIn">
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/tasks/change_status/<?= $task_details->task_id . '/not_started' ?>"><?= lang('not_started') ?></a>
                                                            </li>
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/tasks/change_status/<?= $task_details->task_id . '/in_progress' ?>"><?= lang('in_progress') ?></a>
                                                            </li>
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/tasks/change_status/<?= $task_details->task_id . '/completed' ?>"><?= lang('completed') ?></a>
                                                            </li>
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/tasks/change_status/<?= $task_details->task_id . '/deferred' ?>"><?= lang('deferred') ?></a>
                                                            </li>
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/tasks/change_status/<?= $task_details->task_id . '/waiting_for_someone' ?>"><?= lang('waiting_for_someone') ?></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-3 br">
                                    <p class="lead bb"></p>
                                    <form class="form-horizontal p-20">
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('timer_status') ?>:</strong></div>
                                            <div class="col-sm-8">
                                                <?php if (timer_status('tasks', $task_details->task_id, 'on')) { ?>
                                                    <span class="label label-success"><?= lang('on') ?></span>

                                                    <a class="btn btn-xs btn-danger "
                                                       href="<?= base_url() ?>admin/tasks/tasks_timer/off/<?= $task_details->task_id ?>"><?= lang('stop_timer') ?> </a>
                                                <?php } else {
                                                    ?>
                                                    <span class="label label-danger"><?= lang('off') ?></span>
                                                    <?php $this_permission = $this->tasks_model->can_action('tbl_task', 'view', array('task_id' => $task_details->task_id), true);
                                                    if (!empty($this_permission)) { ?>
                                                        <a class="btn btn-xs btn-success <?= $disabled ?>"
                                                           href="<?= base_url() ?>admin/tasks/tasks_timer/on/<?= $task_details->task_id ?>"><?= lang('start_timer') ?> </a>
                                                    <?php }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('project_hourly_rate') ?> :</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                <?php
                                                if (!empty($task_details->hourly_rate)) {
                                                    echo $task_details->hourly_rate;
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('created_by') ?> :</strong></div>
                                            <div class="col-sm-8">
                                                <?php
                                                if (!empty($task_details->created_by)) {
                                                    echo $this->db->where('user_id', $task_details->created_by)->get('tbl_account_details')->row()->fullname;
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <small><?= lang('created_date') ?> :</small>
                                            </div>
                                            <div class="col-sm-8">
                                                <?php
                                                if (!empty($task_details->due_date)) {
                                                    echo strftime(config_item('date_format'), strtotime($task_details->task_created_date)) . ' ' . display_time($task_details->task_created_date);
                                                }
                                                ?>
                                            </div>
                                        </div>

                                    </form>
                                </div>

                                <div class="col-md-3 br">
                                    <p class="lead bb"></p>
                                    <form class="form-horizontal p-20">

                                        <?php $show_custom_fields = custom_form_label(3, $task_details->task_id);

                                        if (!empty($show_custom_fields)) {
                                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                                if (!empty($v_fields)) {
                                                    ?>
                                                    <div class="form-group">
                                                        <div class="col-sm-4"><strong><?= $c_label ?> :</strong></div>
                                                        <div class="col-sm-8">
                                                            <?= $v_fields ?>
                                                        </div>
                                                    </div>
                                                <?php }
                                            }
                                        }
                                        ?>

                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('estimated_hour') ?>
                                                    :</strong></div>
                                            <div class="col-sm-8 ">
                                                <?php if (!empty($task_details->task_hour)) echo $task_details->task_hour; ?> <?= lang('hours') ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('billable') ?>
                                                    :</strong></div>
                                            <div class="col-sm-8 ">
                                                <?php if (!empty($task_details->billable)) {
                                                    if ($task_details->billable == 'Yes') {
                                                        $billable = 'success';
                                                        $text = lang('yes');
                                                    } else {
                                                        $billable = 'danger';
                                                        $text = lang('no');
                                                    };
                                                } else {
                                                    $billable = '';
                                                    $text = '-';
                                                }; ?>
                                                <strong class="label label-<?= $billable ?>">
                                                    <?= $text ?>
                                                </strong>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('participants') ?>
                                                    :</strong></div>
                                            <div class="col-sm-8 ">
                                                <?php
                                                if ($task_details->permission != 'all') {
                                                    $get_permission = json_decode($task_details->permission);
                                                    if (is_object($get_permission)) :
                                                        foreach ($get_permission as $permission => $v_permission) :
                                                            $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                                            if ($user_info->role_id == 1) {
                                                                $label = 'circle-danger';
                                                            } else {
                                                                $label = 'circle-success';
                                                            }
                                                            $profile_info = $this->db->where(array('user_id' => $permission))->get('tbl_account_details')->row();
                                                            ?>


                                                            <a href="#" data-toggle="tooltip" data-placement="top"
                                                               title="<?= $profile_info->fullname ?>"><img
                                                                    src="<?= base_url() . $profile_info->avatar ?>"
                                                                    class="img-circle img-xs" alt="">
                                                <span style="margin: 0px 0 8px -10px;"
                                                      class="circle <?= $label ?>  circle-lg"></span>
                                                            </a>
                                                            <?php
                                                        endforeach;
                                                    endif;
                                                } else { ?><strong><?= lang('everyone') ?></strong>
                                                    <i
                                                        title="<?= lang('permission_for_all') ?>"
                                                        class="fa fa-question-circle" data-toggle="tooltip"
                                                        data-placement="top"></i>

                                                    <?php
                                                }
                                                ?>
                                                <?php
                                                $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $task_details->task_id));
                                                if (!empty($can_edit) && !empty($edited)) {
                                                    ?>
                                                    <span data-placement="top" data-toggle="tooltip"
                                                          title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/tasks/update_users/<?= $task_details->task_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-3">
                                    <p class="lead bb"></p>
                                    <form class="form-horizontal p-20">

                                        <?php

                                        $task_time = $this->tasks_model->task_spent_time_by_id($task_details->task_id);
                                        ?>
                                        <?= $this->tasks_model->get_time_spent_result($task_time) ?>
                                        <?php
                                        if (!empty($task_details->billable) && $task_details->billable == 'Yes') {
                                            $total_time = $task_time / 3600;
                                            $total_cost = $total_time * $task_details->hourly_rate;
                                            $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                                            ?>
                                            <h2 class="text-center"><?= lang('total_bill') ?>
                                                : <?= display_money($total_cost, $currency->symbol) ?></h2>
                                        <?php }
                                        $estimate_hours = $task_details->task_hour;
                                        $percentage = $this->tasks_model->get_estime_time($estimate_hours);

                                        if ($task_time < $percentage) {
                                            $total_time = $percentage - $task_time;
                                            $worked = '<storng style="font-size: 15px;"  class="required">' . lang('left_works') . '</storng>';
                                        } else {
                                            $total_time = $task_time - $percentage;
                                            $worked = '<storng style="font-size: 15px" class="required">' . lang('extra_works') . '</storng>';
                                        }

                                        ?>
                                        <div class="text-center">
                                            <div class="">
                                                <?= $worked ?>
                                            </div>
                                            <div class="">
                                                <?= $this->tasks_model->get_spent_time($total_time) ?>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6 br ">
                                    <p class="lead bb"></p>
                                    <form class="form-horizontal p-20">
                                        <blockquote style="font-size: 12px;word-wrap: break-word;"><?php
                                            if (!empty($task_details->task_description)) {
                                                echo $task_details->task_description;
                                            }
                                            ?></blockquote>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <p class="lead bb"></p>
                                    <form class="form-horizontal p-20">
                                        <div class="col-sm-12">
                                            <strong><?= lang('completed') ?>:</strong>
                                        </div>
                                        <div class="col-sm-12">
                                            <?php
                                            if ($task_details->task_progress < 49) {
                                                $progress = 'progress-bar-danger';
                                            } elseif ($task_details->task_progress > 50 && $task_details->task_progress < 99) {
                                                $progress = 'progress-bar-primary';
                                            } else {
                                                $progress = 'progress-bar-success';
                                            }
                                            ?>
                                            <span class="">
                                <div class="mt progress progress-striped ">
                                    <div class="progress-bar <?= $progress ?> " data-toggle="tooltip"
                                         data-original-title="<?= $task_details->task_progress ?>%"
                                         style="width: <?= $task_details->task_progress ?>%"></div>
                                </div>
                                </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-group col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('task_status') ?>
                                        :</strong></label>
                                <div class="pull-left mt">
                                    <?php
                                    $disabled = null;
                                    if ($task_details->task_status == 'completed') {
                                        $label = 'success';
                                        $disabled = 'disabled';
                                    } elseif ($task_details->task_status == 'not_started') {
                                        $label = 'info';
                                    } elseif ($task_details->task_status == 'deferred') {
                                        $label = 'danger';
                                    } else {
                                        $label = 'warning';
                                    }
                                    ?>
                                    <p class="form-control-static label label-<?= $label ?>  "><?= lang($task_details->task_status) ?></p>
                                </div>
                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                    <div class="col-sm-1 mt">
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-success dropdown-toggle"
                                                    data-toggle="dropdown">
                                                <?= lang('change') ?>
                                                <span class="caret"></span></button>
                                            <ul class="dropdown-menu animated zoomIn">
                                                <li>
                                                    <a href="<?= base_url() ?>admin/tasks/change_status/<?= $task_details->task_id . '/not_started' ?>"><?= lang('not_started') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/tasks/change_status/<?= $task_details->task_id . '/in_progress' ?>"><?= lang('in_progress') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/tasks/change_status/<?= $task_details->task_id . '/completed' ?>"><?= lang('completed') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/tasks/change_status/<?= $task_details->task_id . '/deferred' ?>"><?= lang('deferred') ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url() ?>admin/tasks/change_status/<?= $task_details->task_id . '/waiting_for_someone' ?>"><?= lang('waiting_for_someone') ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                            <div class="form-group  col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('timer_status') ?>
                                        :</strong></label>
                                <div class="col-sm-8 mt">
                                    <?php if (timer_status('tasks', $task_details->task_id, 'on')) { ?>
                                        <span class="label label-success"><?= lang('on') ?></span>

                                        <a class="btn btn-xs btn-danger "
                                           href="<?= base_url() ?>admin/tasks/tasks_timer/off/<?= $task_details->task_id ?>"><?= lang('stop_timer') ?> </a>
                                    <?php } else {
                                        ?>
                                        <span class="label label-danger"><?= lang('off') ?></span>
                                        <?php $this_permission = $this->tasks_model->can_action('tbl_task', 'view', array('task_id' => $task_details->task_id), true);
                                        if (!empty($this_permission)) { ?>
                                            <a class="btn btn-xs btn-success <?= $disabled ?>"
                                               href="<?= base_url() ?>admin/tasks/tasks_timer/on/<?= $task_details->task_id ?>"><?= lang('start_timer') ?> </a>
                                        <?php }
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            if (!empty($task_details->project_id)):
                                $project_info = $this->db->where('project_id', $task_details->project_id)->get('tbl_project')->row();
                                $milestones_info = $this->db->where('milestones_id', $task_details->milestones_id)->get('tbl_milestones')->row();
                                ?>
                                <div class="form-group  col-sm-6">
                                    <label class="control-label col-sm-5"><strong><?= lang('project_name') ?>
                                            :</strong></label>
                                    <div class="col-sm-7 ">
                                        <p class="form-control-static"><?php if (!empty($project_info->project_name)) echo $project_info->project_name; ?></p>
                                    </div>
                                </div>
                                <div class="form-group  col-sm-6">
                                    <label class="control-label col-sm-4"><strong><?= lang('milestone') ?>
                                            :</strong></label>
                                    <div class="col-sm-8 ">
                                        <p class="form-control-static"><?php if (!empty($milestones_info->milestone_name)) echo $milestones_info->milestone_name; ?></p>
                                    </div>
                                </div>
                            <?php endif ?>
                            <?php
                            if (!empty($task_details->opportunities_id)):
                                $opportunity_info = $this->db->where('opportunities_id', $task_details->opportunities_id)->get('tbl_opportunities')->row();
                                ?>
                                <div class="form-group  col-sm-10">
                                    <label class="control-label col-sm-3 "><strong
                                            class="mr-sm"><?= lang('opportunity_name') ?></strong></label>
                                    <div class="col-sm-8 " style="margin-left: -5px;">
                                        <p class="form-control-static"><?php if (!empty($opportunity_info->opportunity_name)) echo $opportunity_info->opportunity_name; ?></p>
                                    </div>
                                </div>
                            <?php endif ?>

                            <?php
                            if (!empty($task_details->leads_id)):
                                $leads_info = $this->db->where('leads_id', $task_details->leads_id)->get('tbl_leads')->row();
                                ?>
                                <div class="form-group  col-sm-10">
                                    <label class="control-label col-sm-3 "><strong
                                            class="mr-sm"><?= lang('leads_name') ?></strong></label>
                                    <div class="col-sm-8 " style="margin-left: -5px;">
                                        <p class="form-control-static"><?php if (!empty($leads_info->lead_name)) echo $leads_info->lead_name; ?></p>
                                    </div>
                                </div>
                            <?php endif ?>

                            <?php
                            if (!empty($task_details->bug_id)):
                                $bugs_info = $this->db->where('bug_id', $task_details->bug_id)->get('tbl_bug')->row();
                                ?>
                                <div class="form-group  col-sm-10">
                                    <label class="control-label col-sm-3 "><strong
                                            class="mr-sm"><?= lang('bug_title') ?></strong></label>
                                    <div class="col-sm-8 " style="margin-left: -5px;">
                                        <p class="form-control-static"><?php if (!empty($bugs_info->bug_title)) echo $bugs_info->bug_title; ?></p>
                                    </div>
                                </div>
                            <?php endif ?>
                            <?php
                            if (!empty($task_details->goal_tracking_id)):
                                $goal_tracking_info = $this->db->where('goal_tracking_id', $task_details->goal_tracking_id)->get('tbl_goal_tracking')->row();
                                ?>
                                <div class="form-group  col-sm-10">
                                    <label class="control-label col-sm-3 "><strong
                                            class="mr-sm"><?= lang('goal_tracking') ?></strong></label>
                                    <div class="col-sm-8 " style="margin-left: -5px;">
                                        <p class="form-control-static"><?php if (!empty($goal_tracking_info->subject)) echo $goal_tracking_info->subject; ?></p>
                                    </div>
                                </div>
                            <?php endif ?>
                            <?php
                            if (!empty($task_details->sub_task_id)):
                                $sub_task = $this->db->where('task_id', $task_details->sub_task_id)->get('tbl_task')->row();
                                ?>
                                <div class="form-group  col-sm-10">
                                    <label class="control-label col-sm-3 "><strong
                                            class="mr-sm"><?= lang('sub_tasks') ?></strong></label>
                                    <div class="col-sm-8 " style="margin-left: -5px;">
                                        <p class="form-control-static"><?php if (!empty($sub_task->task_name)) echo $sub_task->task_name; ?></p>
                                    </div>
                                </div>
                            <?php endif ?>
                            <div class="form-group  col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('start_date') ?>
                                        :</strong></label>
                                <div class="col-sm-7 ">
                                    <p class="form-control-static"><?php
                                        if (!empty($task_details->task_start_date)) {
                                            echo strftime(config_item('date_format'), strtotime($task_details->task_start_date));
                                        }
                                        ?></p>
                                </div>
                            </div>
                            <div class="form-group  col-sm-6">
                                <?php
                                $due_date = $task_details->due_date;
                                $due_time = strtotime($due_date);
                                $current_time = strtotime(date('Y-m-d'));
                                if ($current_time > $due_time) {
                                    $text = 'text-danger';
                                } else {
                                    $text = null;
                                }
                                ?>

                                <label class="control-label col-sm-4"><strong
                                        class="<?= $text ?>"><?= lang('due_date') ?>
                                        :</strong></label>
                                <div class="col-sm-8 ">
                                    <p class="form-control-static"><?php
                                        if (!empty($task_details->due_date)) {
                                            echo strftime(config_item('date_format'), strtotime($task_details->due_date));
                                        }
                                        ?></p>

                                </div>
                            </div>
                            <div class="form-group  col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('created_by') ?>
                                        :</strong></label>
                                <div class="col-sm-7 ">
                                    <p class="form-control-static"><?php
                                        if (!empty($task_details->created_by)) {
                                            echo $this->db->where('user_id', $task_details->created_by)->get('tbl_account_details')->row()->fullname;
                                        }
                                        ?></p>

                                </div>
                            </div>
                            <div class="form-group  col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('created_date') ?>
                                        :</strong></label>
                                <div class="col-sm-8 ">
                                    <p class="form-control-static"><?php
                                        if (!empty($task_details->due_date)) {
                                            echo strftime(config_item('date_format'), strtotime($task_details->task_created_date));
                                        }
                                        ?></p>

                                </div>
                            </div>
                            <div class="form-group  col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('project_hourly_rate') ?>
                                        :</strong></label>
                                <div class="col-sm-7 ">
                                    <p class="form-control-static"><?php
                                        if (!empty($task_details->hourly_rate)) {
                                            echo $task_details->hourly_rate;
                                        }
                                        ?></p>
                                </div>
                            </div>

                            <?php $show_custom_fields = custom_form_label(3, $task_details->task_id);

                            if (!empty($show_custom_fields)) {
                                foreach ($show_custom_fields as $c_label => $v_fields) {
                                    if (!empty($v_fields)) {
                                        if (count($v_fields) == 1) {
                                            $col = 'col-sm-10';
                                            $sub_col = 'col-sm-3';
                                            $style = 'padding-left:8px';
                                        } else {
                                            $col = 'col-sm-6';
                                            $sub_col = 'col-sm-5';
                                            $style = null;
                                        }

                                        ?>
                                        <div class="form-group  <?= $col ?>" style="<?= $style ?>">
                                            <label class="control-label <?= $sub_col ?>"><strong><?= $c_label ?>
                                                    :</strong></label>
                                            <div class="col-sm-7 ">
                                                <p class="form-control-static">
                                                    <strong><?= $v_fields ?></strong>
                                                </p>
                                            </div>
                                        </div>
                                    <?php }
                                }
                            }
                            ?>
                            <div class="form-group  col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('estimated_hour') ?>
                                        :</strong></label>
                                <div class="col-sm-7 ">
                                    <p class="form-control-static">
                                        <strong><?php if (!empty($task_details->task_hour)) echo $task_details->task_hour; ?> <?= lang('hours') ?></strong>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group  col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('billable') ?>
                                        :</strong></label>
                                <div class="col-sm-7 ">
                                    <p class="form-control-static">
                                        <?php if (!empty($task_details->billable)) {
                                            if ($task_details->billable == 'Yes') {
                                                $billable = 'success';
                                                $text = lang('yes');
                                            } else {
                                                $billable = 'danger';
                                                $text = lang('no');
                                            };
                                        } else {
                                            $billable = '';
                                            $text = '-';
                                        }; ?>
                                        <strong class="label label-<?= $billable ?>">
                                            <?= $text ?>
                                        </strong>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group  col-sm-6">
                                <label class="control-label col-sm-4"><strong><?= lang('participants') ?>
                                        :</strong></label>
                                <div class="col-sm-8 ">
                                    <?php
                                    if (!empty($task_details->permission) && $task_details->permission != 'all') {
                                        $get_permission = json_decode($task_details->permission);
                                        if (is_object($get_permission) && !empty($get_permission)) :
                                            foreach ($get_permission as $permission => $v_permission) :
                                                $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                                if (!empty($user_info)) {
                                                    if ($user_info->role_id == 1) {
                                                        $label = 'circle-danger';
                                                    } else {
                                                        $label = 'circle-success';
                                                    }
                                                    $profile_info = $this->db->where(array('user_id' => $permission))->get('tbl_account_details')->row();
                                                    ?>


                                                    <a href="#" data-toggle="tooltip" data-placement="top"
                                                       title="<?= $profile_info->fullname ?>"><img
                                                                src="<?= base_url() . $profile_info->avatar ?>"
                                                                class="img-circle img-xs" alt="">
                                                        <span style="margin: 0px 0 8px -10px;"
                                                              class="circle <?= $label ?>  circle-lg"></span>
                                                    </a>
                                                    <?php
                                                }
                                            endforeach;
                                        endif;
                                    } else { ?>
                                    <p class="form-control-static"><strong><?= lang('everyone') ?></strong>
                                        <i
                                            title="<?= lang('permission_for_all') ?>"
                                            class="fa fa-question-circle" data-toggle="tooltip"
                                            data-placement="top"></i>

                                        <?php
                                        }
                                        ?>
                                        <?php
                                        $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $task_details->task_id));
                                        if (!empty($can_edit) && !empty($edited)) {
                                        ?>
                                        <span data-placement="top" data-toggle="tooltip"
                                              title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/tasks/update_users/<?= $task_details->task_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                    </p>
                                <?php
                                }
                                ?>

                                </div>
                            </div>

                            <div class="form-group  col-sm-10">
                                <label class="control-label col-sm-3 "><strong class="mr-sm"><?= lang('completed') ?>
                                        :</strong></label>
                                <div class="col-sm-9 " style="margin-left: -5px;">
                                    <?php
                                    if ($task_details->task_progress < 49) {
                                        $progress = 'progress-bar-danger';
                                    } elseif ($task_details->task_progress > 50 && $task_details->task_progress < 99) {
                                        $progress = 'progress-bar-primary';
                                    } else {
                                        $progress = 'progress-bar-success';
                                    }
                                    ?>
                                    <span class="">
                                <div class="mt progress progress-striped ">
                                    <div class="progress-bar <?= $progress ?> " data-toggle="tooltip"
                                         data-original-title="<?= $task_details->task_progress ?>%"
                                         style="width: <?= $task_details->task_progress ?>%"></div>
                                </div>
                                </span>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <?php

                                $task_time = $this->tasks_model->task_spent_time_by_id($task_details->task_id);
                                ?>
                                <?= $this->tasks_model->get_time_spent_result($task_time) ?>
                                <?php
                                if (!empty($task_details->billable) && $task_details->billable == 'Yes') {
                                    $total_time = $task_time / 3600;
                                    $total_cost = $total_time * $task_details->hourly_rate;
                                    $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                                    ?>
                                    <h2 class="text-center"><?= lang('total_bill') ?>
                                        : <?= display_money($total_cost, $currency->symbol) ?></h2>
                                <?php }
                                $estimate_hours = $task_details->task_hour;
                                $percentage = $this->tasks_model->get_estime_time($estimate_hours);

                                if ($task_time < $percentage) {
                                    $total_time = $percentage - $task_time;
                                    $worked = '<storng style="font-size: 15px;"  class="required">' . lang('left_works') . '</storng>';
                                } else {
                                    $total_time = $task_time - $percentage;
                                    $worked = '<storng style="font-size: 15px" class="required">' . lang('extra_works') . '</storng>';
                                }

                                ?>
                                <div class="text-center">
                                    <div class="">
                                        <?= $worked ?>
                                    </div>
                                    <div class="">
                                        <?= $this->tasks_model->get_spent_time($total_time) ?>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-12">
                                <blockquote
                                    style="font-size: 12px; margin-top: 5px;word-wrap: break-word;width: 100%"><?php if (!empty($task_details->task_description)) echo $task_details->task_description; ?></blockquote>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
            <!-- Task Details tab Ends -->
            <!-- Task Comments Panel Starts --->
            <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="task_comments"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('comments') ?></h3>
                    </div>
                    <div class="panel-body chat" id="chat-box">
                        <?php echo form_open(base_url("admin/tasks/save_comments"), array("id" => $comment_type . "-comment-form", "class" => "form-horizontal general-form", "enctype" => "multipart/form-data", "role" => "form")); ?>

                        <input type="hidden" name="task_id" value="<?php
                        if (!empty($task_details->task_id)) {
                            echo $task_details->task_id;
                        }
                        ?>" class="form-control">

                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php
                                echo form_textarea(array(
                                    "id" => "comment_description",
                                    "name" => "comment",
                                    "class" => "form-control comment_description",
                                    "placeholder" => $task_details->task_name . ' ' . lang('comments'),
                                    "data-rule-required" => true,
                                    "rows" => 4,
                                    "data-msg-required" => lang("field_required"),
                                ));
                                ?>
                            </div>
                        </div>
                        <div id="new_comments_attachement">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div id="comments_file-dropzone" class="dropzone mb15">

                                    </div>
                                    <div id="comments_file-dropzone-scrollbar">
                                        <div id="comments_file-previews">
                                            <div id="file-upload-row" class="mt pull-left">
                                                <div class="preview box-content pr-lg" style="width:100px;">
                                                    <span data-dz-remove class="pull-right" style="cursor: pointer">
                                    <i class="fa fa-times"></i>
                                </span>
                                                    <img data-dz-thumbnail class="upload-thumbnail-sm"/>
                                                    <input class="file-count-field" type="hidden" name="files[]"
                                                           value=""/>
                                                    <div
                                                        class="mb progress progress-striped upload-progress-sm active mt-sm"
                                                        role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                        aria-valuenow="0">
                                                        <div class="progress-bar progress-bar-success" style="width:0%;"
                                                             data-dz-uploadprogress></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="pull-right">
                                    <button type="submit" id="file-save-button"
                                            class="btn btn-primary"><?= lang('post_comment') ?></button>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <?php echo form_close();
                        $comment_reply_type = 'tasks-reply';
                        ?>
                        <?php $this->load->view('admin/tasks/comments_list', array('comment_details' => $comment_details)) ?>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                $('#file-save-button').on('click', function (e) {
                                    var ubtn = $(this);
                                    ubtn.html('Please wait...');
                                    ubtn.addClass('disabled');
                                });
                                $("#<?php echo $comment_type; ?>-comment-form").appForm({
                                    isModal: false,
                                    onSuccess: function (result) {
                                        $(".comment_description").val("");
                                        $(".dz-complete").remove();
                                        $('#file-save-button').removeClass("disabled").html('<?= lang('post_comment')?>');
                                        $(result.data).insertAfter("#<?php echo $comment_type; ?>-comment-form");
                                        toastr[result.status](result.message);
                                    }
                                });
                                fileSerial = 0;
                                // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
                                var previewNode = document.querySelector("#file-upload-row");
                                previewNode.id = "";
                                var previewTemplate = previewNode.parentNode.innerHTML;
                                previewNode.parentNode.removeChild(previewNode);
                                Dropzone.autoDiscover = false;
                                var projectFilesDropzone = new Dropzone("#comments_file-dropzone", {
                                    url: "<?= base_url()?>admin/global_controller/upload_file",
                                    thumbnailWidth: 80,
                                    thumbnailHeight: 80,
                                    parallelUploads: 20,
                                    previewTemplate: previewTemplate,
                                    dictDefaultMessage: '<?php echo lang("file_upload_instruction"); ?>',
                                    autoQueue: true,
                                    previewsContainer: "#comments_file-previews",
                                    clickable: true,
                                    accept: function (file, done) {
                                        if (file.name.length > 200) {
                                            done("Filename is too long.");
                                            $(file.previewTemplate).find(".description-field").remove();
                                        }
                                        //validate the file
                                        $.ajax({
                                            url: "<?= base_url()?>admin/global_controller/validate_project_file",
                                            data: {file_name: file.name, file_size: file.size},
                                            cache: false,
                                            type: 'POST',
                                            dataType: "json",
                                            success: function (response) {
                                                if (response.success) {
                                                    fileSerial++;
                                                    $(file.previewTemplate).find(".description-field").attr("name", "comment_" + fileSerial);
                                                    $(file.previewTemplate).append("<input type='hidden' name='file_name_" + fileSerial + "' value='" + file.name + "' />\n\
                                     <input type='hidden' name='file_size_" + fileSerial + "' value='" + file.size + "' />");
                                                    $(file.previewTemplate).find(".file-count-field").val(fileSerial);
                                                    done();
                                                } else {
                                                    $(file.previewTemplate).find("input").remove();
                                                    done(response.message);
                                                }
                                            }
                                        });
                                    },
                                    processing: function () {
                                        $("#file-save-button").prop("disabled", true);
                                    },
                                    queuecomplete: function () {
                                        $("#file-save-button").prop("disabled", false);
                                    },
                                    fallback: function () {
                                        //add custom fallback;
                                        $("body").addClass("dropzone-disabled");
                                        $('.modal-dialog').find('[type="submit"]').removeAttr('disabled');

                                        $("#comments_file-dropzone").hide();

                                        $("#file-modal-footer").prepend("<button id='add-more-file-button' type='button' class='btn  btn-default pull-left'><i class='fa fa-plus-circle'></i> " + "<?php echo lang("add_more"); ?>" + "</button>");

                                        $("#file-modal-footer").on("click", "#add-more-file-button", function () {
                                            var newFileRow = "<div class='file-row pb pt10 b-b mb10'>"
                                                + "<div class='pb clearfix '><button type='button' class='btn btn-xs btn-danger pull-left mr remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>"
                                                + "<div class='mb5 pb5'><input class='form-control description-field'  name='comment[]'  type='text' style='cursor: auto;' placeholder='<?php echo lang("comment") ?>' /></div>"
                                                + "</div>";
                                            $("#comments_file-previews").prepend(newFileRow);
                                        });
                                        $("#add-more-file-button").trigger("click");
                                        $("#comments_file-previews").on("click", ".remove-file", function () {
                                            $(this).closest(".file-row").remove();
                                        });
                                    },
                                    success: function (file) {
                                        setTimeout(function () {
                                            $(file.previewElement).find(".progress-striped").removeClass("progress-striped").addClass("progress-bar-success");
                                        }, 1000);
                                    }
                                });

                            })
                        </script>
                    </div>
                </div>
            </div>
            <!-- Task Comments Panel Ends--->

            <!-- Task Attachment Panel Starts --->
            <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="task_attachments">
                <div class="panel panel-custom">
                    <div class="panel-heading mb0">
                        <?php
                        $attach_list = $this->session->userdata('tasks_media_view');
                        if (empty($attach_list)) {
                            $attach_list = 'list_view';
                        }
                        ?>
                        <h3 class="panel-title"><?= lang('attach_file_list') ?>
                            <a data-toggle="tooltip" data-placement="top"
                               href="<?= base_url('admin/global_controller/download_all_attachment/task_id/' . $task_details->task_id) ?>"
                               class="btn btn-default"
                               title="<?= lang('download') . ' ' . lang('all') . ' ' . lang('attachment') ?>"><i
                                    class="fa fa-cloud-download"></i></a>
                            <a data-toggle="tooltip" data-placement="top"
                               class="btn btn-default toggle-media-view <?= (!empty($attach_list) && $attach_list == 'list_view' ? 'hidden' : '') ?>"
                               data-type="list_view"
                               title="<?= lang('switch_to') . ' ' . lang('media_view') ?>"><i
                                    class="fa fa-image"></i></a>
                            <a data-toggle="tooltip" data-placement="top"
                               class="btn btn-default toggle-media-view <?= (!empty($attach_list) && $attach_list == 'media_view' ? 'hidden' : '') ?>"
                               data-type="media_view"
                               title="<?= lang('switch_to') . ' ' . lang('list_view') ?>"><i
                                    class="fa fa-list"></i></a>

                            <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                                <a href="<?= base_url() ?>admin/tasks/new_attachment/<?= $task_details->task_id ?>"
                                   class="text-purple text-sm" data-toggle="modal" data-placement="top"
                                   data-target="#myModal_extra_lg">
                                    <i class="fa fa-plus "></i> <?= lang('new') . ' ' . lang('attachment') ?></a>
                            </div>
                        </h3>
                    </div>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $(".toggle-media-view").click(function () {
                                $(".media-view-container").toggleClass('hidden');
                                $(".toggle-media-view").toggleClass('hidden');
                                $(".media-list-container").toggleClass('hidden');
                                var type = $(this).data('type');
                                var module = 'tasks';
                                $.get('<?= base_url()?>admin/global_controller/set_media_view/' + type + '/' + module, function (response) {
                                });
                            });
                        });
                    </script>
                    <?php
                    $this->load->helper('file');
                    if (empty($project_files_info)) {
                        $project_files_info = array();
                    } ?>
                    <div
                        class="p media-view-container <?= (!empty($attach_list) && $attach_list == 'media_view' ? 'hidden' : '') ?>">
                        <div class="row">
                            <?php $this->load->view('admin/tasks/attachment_list', array('project_files_info' => $project_files_info)); ?>
                        </div>
                    </div>
                    <div
                        class="media-list-container <?= (!empty($attach_list) && $attach_list == 'list_view' ? 'hidden' : '') ?>">
                        <?php
                        if (!empty($project_files_info)) {
                            foreach ($project_files_info as $key => $v_files_info) {
                                ?>
                                <div class="panel-group"
                                     id="media_list_container-<?= $files_info[$key]->task_attachment_id ?>"
                                     style="margin:8px 0px;" role="tablist"
                                     aria-multiselectable="true">
                                    <div class="box box-info" style="border-radius: 0px ">
                                        <div class="p pb-sm" role="tab" id="headingOne"
                                             style="border-bottom: 1px solid #dde6e9">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion"
                                                   href="#<?php echo $key ?>" aria-expanded="true"
                                                   aria-controls="collapseOne">
                                                    <strong
                                                        class="text-alpha-inverse"><?php echo $files_info[$key]->title; ?> </strong>
                                                    <small style="color:#ffffff " class="pull-right">
                                                        <?php if ($files_info[$key]->user_id == $this->session->userdata('user_id')) { ?>
                                                            <?php echo ajax_anchor(base_url("admin/tasks/delete_task_files/" . $files_info[$key]->task_attachment_id), "<i class='text-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#media_list_container-" . $files_info[$key]->task_attachment_id)); ?>
                                                        <?php } ?></small>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="<?php echo $key ?>" class="panel-collapse collapse <?php
                                        if (!empty($in) && $files_info[$key]->files_id == $in) {
                                            echo 'in';
                                        }
                                        ?>" role="tabpanel" aria-labelledby="headingOne">
                                            <div class="content p">
                                                <div class="table-responsive">
                                                    <table id="table-files" class="table table-striped ">
                                                        <thead>
                                                        <tr>
                                                            <th><?= lang('files') ?></th>
                                                            <th class=""><?= lang('size') ?></th>
                                                            <th><?= lang('date') ?></th>
                                                            <th><?= lang('total') . ' ' . lang('comments') ?></th>
                                                            <th><?= lang('uploaded_by') ?></th>
                                                            <th><?= lang('action') ?></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $this->load->helper('file');

                                                        if (!empty($v_files_info)) {
                                                            foreach ($v_files_info as $v_files) {
                                                                $user_info = $this->db->where(array('user_id' => $files_info[$key]->user_id))->get('tbl_users')->row();
                                                                $total_file_comment = count($this->db->where(array('uploaded_files_id' => $v_files->uploaded_files_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result());
                                                                ?>
                                                                <tr class="file-item">
                                                                    <td data-toggle="tooltip"
                                                                        data-placement="top"
                                                                        data-original-title="<?= $files_info[$key]->description ?>">
                                                                        <?php if ($v_files->is_image == 1) : ?>
                                                                            <div class="file-icon"><a
                                                                                    data-toggle="modal"
                                                                                    data-target="#myModal_extra_lg"
                                                                                    href="<?= base_url() ?>admin/tasks/attachment_details/r/<?= $files_info[$key]->task_attachment_id . '/' . $v_files->uploaded_files_id ?>">
                                                                                    <img
                                                                                        style="width: 50px;border-radius: 5px;"
                                                                                        src="<?= base_url() . $v_files->files ?>"/></a>
                                                                            </div>
                                                                        <?php else : ?>
                                                                            <div class="file-icon"><i
                                                                                    class="fa fa-file-o"></i>
                                                                                <a data-toggle="modal"
                                                                                   data-target="#myModal_extra_lg"
                                                                                   href="<?= base_url() ?>admin/tasks/attachment_details/r/<?= $files_info[$key]->task_attachment_id . '/' . $v_files->uploaded_files_id ?>"><?= $v_files->file_name ?></a>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </td>

                                                                    <td class=""><?= $v_files->size ?>Kb</td>
                                                                    <td class="col-date"><?= date('Y-m-d' . "<br/> h:m A", strtotime($files_info[$key]->upload_time)); ?></td>
                                                                    <td class=""><?= $total_file_comment ?></td>
                                                                    <td>
                                                                        <?= $user_info->username ?>
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn btn-xs btn-dark"
                                                                           data-toggle="tooltip"
                                                                           data-placement="top"
                                                                           title="Download"
                                                                           href="<?= base_url() ?>admin/tasks/download_files/<?= $v_files->uploaded_files_id ?>"><i
                                                                                class="fa fa-download"></i></a>
                                                                    </td>

                                                                </tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <tr>
                                                                <td colspan="5">
                                                                    <?= lang('nothing_to_display') ?>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <!-- Task Attachment Panel Ends --->
            <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="task_notes"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('notes') ?></h3>
                    </div>
                    <div class="panel-body">

                        <form action="<?= base_url() ?>admin/tasks/save_tasks_notes/<?php
                        if (!empty($task_details)) {
                            echo $task_details->task_id;
                        }
                        ?>" enctype="multipart/form-data" method="post" id="form" class="form-horizontal">
                            <div class="form-group">
                                <div class="col-lg-12">
                                                <textarea class="form-control textarea"
                                                          name="tasks_notes"><?= $task_details->tasks_notes ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <button type="submit" id="sbtn"
                                            class="btn btn-primary"><?= lang('updates') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 5 ? 'active' : '' ?>" id="timesheet"
                 style="position: relative;">
                <style>
                    .tooltip-inner {
                        white-space: pre-wrap;
                    }
                </style>
                <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="<?= $time_active == 1 ? 'active' : ''; ?>"><a href="#general"
                                                                                 data-toggle="tab"><?= lang('timesheet') ?></a>
                        </li>
                        <li class="<?= $time_active == 2 ? 'active' : ''; ?>"><a href="#contact"
                                                                                 data-toggle="tab"><?= lang('manual_entry') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane <?= $time_active == 1 ? 'active' : ''; ?>" id="general">
                            <div class="table-responsive">
                                <table id="table-tasks-timelog" class="table table-striped     DataTables">
                                    <thead>
                                    <tr>
                                        <th><?= lang('user') ?></th>
                                        <th><?= lang('start_time') ?></th>
                                        <th><?= lang('stop_time') ?></th>
                                        <th><?= lang('task_name') ?></th>
                                        <th class="col-time"><?= lang('time_spend') ?></th>
                                        <th><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($total_timer)) {
                                        foreach ($total_timer as $v_tasks) {
                                            $task_info = $this->db->where(array('task_id' => $v_tasks->task_id))->get('tbl_task')->row();
                                            if (!empty($task_info)) {
                                                ?>
                                                <tr id="table_tasks_timer-<?= $v_tasks->tasks_timer_id ?>">
                                                    <td class="small">

                                                        <a class="pull-left recect_task  ">
                                                            <?php
                                                            $profile_info = $this->db->where(array('user_id' => $v_tasks->user_id))->get('tbl_account_details')->row();
                                                            $user_info = $this->db->where(array('user_id' => $v_tasks->user_id))->get('tbl_users')->row();
                                                            if (!empty($user_info)) {
                                                                ?>
                                                                <img style="width: 30px;margin-left: 18px;
                                                                             height: 29px;
                                                                             border: 1px solid #aaa;"
                                                                     src="<?= base_url() . $profile_info->avatar ?>"
                                                                     class="img-circle">

                                                                <?= ucfirst($user_info->username) ?>
                                                            <?php } else {
                                                                echo '-';
                                                            } ?>
                                                        </a>


                                                    </td>

                                                    <td><span
                                                            class="label label-success"><?= strftime(config_item('date_format'), $v_tasks->start_time) . ' ' . display_time($v_tasks->start_time, true) ?></span>
                                                    </td>
                                                    <td><span
                                                            class="label label-danger"><?= strftime(config_item('date_format'), $v_tasks->end_time) . ' ' . display_time($v_tasks->end_time, true) ?></span>
                                                    </td>

                                                    <td>
                                                        <a href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_tasks->task_id ?>"
                                                           class="text-info small"><?= $task_info->task_name ?>
                                                            <?php
                                                            if (!empty($v_tasks->reason)) {
                                                                $edit_user_info = $this->db->where(array('user_id' => $v_tasks->edited_by))->get('tbl_users')->row();
                                                                echo '<i class="text-danger" data-html="true" data-toggle="tooltip" data-placement="top" title="Reason : ' . $v_tasks->reason . '<br>' . ' Edited By : ' . $edit_user_info->username . '">Edited</i>';
                                                            }
                                                            ?>
                                                        </a></td>
                                                    <td>
                                                        <small
                                                            class="small text-muted"><?= $this->tasks_model->get_time_spent_result($v_tasks->end_time - $v_tasks->start_time) ?></small>
                                                    </td>
                                                    <td>
                                                        <?= btn_edit('admin/tasks/view_task_details/' . $v_tasks->tasks_timer_id . '/5/edit') ?>
                                                        <?php if ($v_tasks->user_id == $this->session->userdata('user_id')) { ?>
                                                            <?php echo ajax_anchor(base_url("admin/tasks/update_tasks_timer/" . $v_tasks->tasks_timer_id . '/delete_task_timmer'), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_tasks_timer-" . $v_tasks->tasks_timer_id)); ?>
                                                        <?php } ?>
                                                    </td>

                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane <?= $time_active == 2 ? 'active' : ''; ?>" id="contact">
                            <form role="form" enctype="multipart/form-data" id="form"
                                  action="<?php echo base_url(); ?>admin/tasks/update_tasks_timer/<?php
                                  if (!empty($tasks_timer_info)) {
                                      echo $tasks_timer_info->tasks_timer_id;
                                  }
                                  ?>" method="post" class="form-horizontal">
                                <?php
                                if (!empty($tasks_timer_info)) {
                                    $start_date = date('Y-m-d', $tasks_timer_info->start_time);
                                    $start_time = date('H:i', $tasks_timer_info->start_time);
                                    $end_date = date('Y-m-d', $tasks_timer_info->end_time);
                                    $end_time = date('H:i', $tasks_timer_info->end_time);
                                } else {
                                    $start_date = '';
                                    $start_time = '';
                                    $end_date = '';
                                    $end_time = '';
                                }
                                ?>
                                <?php if ($this->session->userdata('user_type') == '1' && empty($tasks_timer_info->tasks_timer_id)) { ?>
                                    <div class="form-group margin">
                                        <div class="col-sm-8 center-block">
                                            <label
                                                class="control-label"><?= lang('select') . ' ' . lang('tasks') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <select class="form-control select_box" name="task_id"
                                                    required="" style="width: 100%">
                                                <?php
                                                $all_tasks_info = $this->db->get('tbl_task')->result();
                                                if (!empty($all_tasks_info)):foreach ($all_tasks_info as $v_task_info):
                                                    ?>
                                                    <option
                                                        value="<?= $v_task_info->task_id ?>" <?= $v_task_info->task_id == $task_details->task_id ? 'selected' : null ?>><?= $v_task_info->task_name ?></option>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <input type="hidden" name="task_id"
                                           value="<?= $task_details->task_id ?>">
                                <?php } ?>
                                <div class="form-group margin">
                                    <div class="col-sm-4">
                                        <label class="control-label"><?= lang('start_date') ?> </label>
                                        <div class="input-group">
                                            <input type="text" name="start_date"
                                                   class="form-control start_date"
                                                   value="<?= $start_date ?>"
                                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="control-label"><?= lang('start_time') ?></label>
                                        <div class="input-group">
                                            <input type="text" name="start_time"
                                                   class="form-control timepicker2"
                                                   value="<?= $start_time ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-clock-o"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group margin">
                                    <div class="col-sm-4">
                                        <label class="control-label"><?= lang('end_date') ?></label>
                                        <div class="input-group">
                                            <input type="text" name="end_date"
                                                   class="form-control end_date" value="<?= $end_date ?>"
                                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="control-label"><?= lang('end_time') ?></label>
                                        <div class="input-group">
                                            <input type="text" name="end_time"
                                                   class="form-control timepicker2"
                                                   value="<?= $end_time ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-clock-o"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group margin">
                                    <div class="col-sm-8 center-block">
                                        <label class="control-label"><?= lang('edit_reason') ?><span
                                                class="required">*</span></label>
                                        <div>
                                                <textarea class="form-control" name="reason" required="" rows="6"><?php
                                                    if (!empty($tasks_timer_info)) {
                                                        echo $tasks_timer_info->reason;
                                                    }
                                                    ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 20px;">
                                    <div class="col-lg-6">
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
            <?php if (!empty($sub_tasks)) { ?>
                <div class="tab-pane <?= $active == 7 ? 'active' : '' ?>" id="sub_tasks">
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#sub_general"
                                                  data-toggle="tab"><?= lang('all') . ' ' . lang('sub_tasks') ?></a>
                            </li>
                            <li>
                                <a href="<?= base_url('admin/tasks/all_task/sub_tasks/' . $task_details->task_id) ?>"><?= lang('new') . ' ' . lang('sub_tasks') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content bg-white">
                            <!-- ************** general *************-->
                            <div class="tab-pane <?= $time_active == 1 ? 'active' : ''; ?>" id="sub_general">
                                <div class="table-responsive">
                                    <table id="table-tasks" class="table table-striped     DataTables">
                                        <thead>
                                        <tr>
                                            <th data-check-all>

                                            </th>
                                            <th><?= lang('task_name') ?></th>
                                            <th><?= lang('due_date') ?></th>
                                            <th class="col-sm-1"><?= lang('progress') ?></th>
                                            <th class="col-sm-1"><?= lang('status') ?></th>
                                            <th class="col-sm-2"><?= lang('changes/view') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $t_edited = can_action('54', 'edited');
                                        if (!empty($all_sub_tasks)):foreach ($all_sub_tasks as $key => $v_task):
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="is_complete checkbox c-checkbox">
                                                        <label>
                                                            <input type="checkbox" data-id="<?= $v_task->task_id ?>"
                                                                   style="position: absolute;" <?php
                                                            if ($v_task->task_progress >= 100) {
                                                                echo 'checked';
                                                            }
                                                            ?>>
                                                            <span class="fa fa-check"></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td><a class="text-info" style="<?php
                                                    if ($v_task->task_progress >= 100) {
                                                        echo 'text-decoration: line-through;';
                                                    }
                                                    ?>"
                                                       href="<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>"><?php echo $v_task->task_name; ?></a>
                                                </td>

                                                <td><?php
                                                    $due_date = $v_task->due_date;
                                                    $due_time = strtotime($due_date);
                                                    $current_time = strtotime(date('Y-m-d'));
                                                    ?>
                                                    <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                    <?php if ($current_time > $due_time && $v_task->task_progress < 100) { ?>
                                                        <span class="label label-danger"><?= lang('overdue') ?></span>
                                                    <?php } ?></td>
                                                <td>
                                                    <div class="inline ">
                                                        <div class="easypiechart text-success" style="margin: 0px;"
                                                             data-percent="<?= $v_task->task_progress ?>"
                                                             data-line-width="5" data-track-Color="#f0f0f0"
                                                             data-bar-color="#<?php
                                                             if ($v_task->task_progress == 100) {
                                                                 echo '8ec165';
                                                             } else {
                                                                 echo 'fb6b5b';
                                                             }
                                                             ?>" data-rotate="270" data-scale-Color="false"
                                                             data-size="50" data-animate="2000">
                                                            <span class="small text-muted"><?= $v_task->task_progress ?>
                                                                %</span>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td>
                                                    <?php
                                                    if ($v_task->task_status == 'completed') {
                                                        $label = 'success';
                                                    } elseif ($v_task->task_status == 'not_started') {
                                                        $label = 'info';
                                                    } elseif ($v_task->task_status == 'deferred') {
                                                        $label = 'danger';
                                                    } else {
                                                        $label = 'warning';
                                                    }
                                                    ?>
                                                    <span
                                                        class="label label-<?= $label ?>"><?= lang($v_task->task_status) ?> </span>
                                                </td>
                                                <td>
                                                    <?php echo btn_view('admin/tasks/view_task_details/' . $v_task->task_id) ?>
                                                    <?php if (!empty($t_edited)) { ?>
                                                        <?php echo btn_edit('admin/tasks/all_task/' . $v_task->task_id) ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane <?= $time_active == 2 ? 'active' : ''; ?>" id="contact">
                                <form role="form" enctype="multipart/form-data" id="form"
                                      action="<?php echo base_url(); ?>admin/tasks/update_tasks_timer/<?php
                                      if (!empty($tasks_timer_info)) {
                                          echo $tasks_timer_info->tasks_timer_id;
                                      }
                                      ?>" method="post" class="form-horizontal">
                                    <?php
                                    if (!empty($tasks_timer_info)) {
                                        $start_date = date('Y-m-d', $tasks_timer_info->start_time);
                                        $start_time = date('H:i', $tasks_timer_info->start_time);
                                        $end_date = date('Y-m-d', $tasks_timer_info->end_time);
                                        $end_time = date('H:i', $tasks_timer_info->end_time);
                                    } else {
                                        $start_date = '';
                                        $start_time = '';
                                        $end_date = '';
                                        $end_time = '';
                                    }
                                    ?>
                                    <?php if ($this->session->userdata('user_type') == '1' && empty($tasks_timer_info->tasks_timer_id)) { ?>
                                        <div class="form-group margin">
                                            <div class="col-sm-8 center-block">
                                                <label
                                                    class="control-label"><?= lang('select') . ' ' . lang('tasks') ?>
                                                    <span
                                                        class="required">*</span></label>
                                                <select class="form-control select_box" name="task_id"
                                                        required="" style="width: 100%">
                                                    <?php
                                                    $all_tasks_info = $this->db->get('tbl_task')->result();
                                                    if (!empty($all_tasks_info)):foreach ($all_tasks_info as $v_task_info):
                                                        ?>
                                                        <option
                                                            value="<?= $v_task_info->task_id ?>" <?= $v_task_info->task_id == $task_details->task_id ? 'selected' : null ?>><?= $v_task_info->task_name ?></option>
                                                    <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <input type="hidden" name="task_id"
                                               value="<?= $task_details->task_id ?>">
                                    <?php } ?>
                                    <div class="form-group margin">
                                        <div class="col-sm-4">
                                            <label class="control-label"><?= lang('start_date') ?> </label>
                                            <div class="input-group">
                                                <input type="text" name="start_date"
                                                       class="form-control datepicker"
                                                       value="<?= $start_date ?>"
                                                       data-date-format="<?= config_item('date_picker_format'); ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="control-label"><?= lang('start_time') ?></label>
                                            <div class="input-group">
                                                <input type="text" name="start_time"
                                                       class="form-control timepicker2"
                                                       value="<?= $start_time ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="fa fa-clock-o"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group margin">
                                        <div class="col-sm-4">
                                            <label class="control-label"><?= lang('end_date') ?></label>
                                            <div class="input-group">
                                                <input type="text" name="end_date"
                                                       class="form-control datepicker" value="<?= $end_date ?>"
                                                       data-date-format="<?= config_item('date_picker_format'); ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="control-label"><?= lang('end_time') ?></label>
                                            <div class="input-group">
                                                <input type="text" name="end_time"
                                                       class="form-control timepicker2"
                                                       value="<?= $end_time ?>">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="fa fa-clock-o"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group margin">
                                        <div class="col-sm-8 center-block">
                                            <label class="control-label"><?= lang('edit_reason') ?><span
                                                    class="required">*</span></label>
                                            <div>
                                                <textarea class="form-control" name="reason" required="" rows="6"><?php
                                                    if (!empty($tasks_timer_info)) {
                                                        echo $tasks_timer_info->reason;
                                                    }
                                                    ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-top: 20px;">
                                        <div class="col-lg-6">
                                            <button type="submit"
                                                    class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>


                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="tab-pane " id="activities">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('activities') ?>
                            <?php
                            $role = $this->session->userdata('user_type');
                            if ($role == 1) {
                                ?>
                                <span class="btn-xs pull-right">
                            <a href="<?= base_url() ?>admin/tasks/claer_activities/tasks/<?= $task_details->task_id ?>"><?= lang('clear') . ' ' . lang('activities') ?></a>
                            </span>
                            <?php } ?>
                        </h3>
                    </div>
                    <div class="panel-body " id="chat-box">
                        <?php
                        if (!empty($activities_info)) {
                            foreach ($activities_info as $v_activities) {
                                $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();
                                $user_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_users')->row();
                                ?>
                                <div class="timeline-2">
                                    <div class="time-item">
                                        <div class="item-info">
                                            <small data-toggle="tooltip" data-placement="top"
                                                   title="<?= display_datetime($v_activities->activity_date) ?>"
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
                </div>
            </div>
        </div>
    </div>
</div>