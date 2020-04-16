<?php include_once 'assets/admin-ajax.php'; ?>
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
$mdate = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('today - 7 days'));
$all_goal_tracking = $this->tasks_model->get_permission('tbl_goal_tracking');

$all_goal = 0;
$bank_goal = 0;
$complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->tasks_model->get_progress($v_goal_track, true);

        if ($v_goal_track->goal_type_id == 8) {

            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->tasks_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->tasks_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }
            $all_goal += $v_goal_track->achievement;
            $complete_achivement += $goal_achieve['achievement'];
        }
    }
}
// 30 days before
$last_weeks = 0;
for ($iDay = 7; $iDay >= 0; $iDay--) {
    $date = date('Y-m-d', strtotime('today - ' . $iDay . 'days'));
    $where = array('task_created_date >=' => $date . " 00:00:00", 'task_created_date <=' => $date . " 23:59:59", 'task_status' => 'completed');

    $invoice_result[$date] = count($this->db->where($where)->get('tbl_task')->result());
    $last_weeks += count($this->db->where($where)->get('tbl_task')->result());
}

$terget_achievement = $this->db->where(array('goal_type_id' => 8, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}
$tolal_goal = $all_goal + $bank_goal;
$curency = $this->tasks_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:20px';
    ?>
    <div class="col-sm-12 bg-white p0" style="<?= $margin ?>">
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($tolal_goal) ?></p>
                    <p class="m0">
                        <small><?= lang('achievement') ?></small>
                    </p>
                </div>
                <div class="col-xs-6 ">
                    <p class="m0 lead"><?= ($last_weeks) ?></p>
                    <p class="m0">
                        <small><?= lang('last_weeks') . ' ' . lang('created') ?></small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($complete_achivement) ?></p>
                    <p class="m0">
                        <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>
                <div class="col-xs-6 pt">
                    <div data-sparkline="" data-bar-color="#23b7e5" data-height="60" data-bar-width="8"
                         data-bar-spacing="6" data-chart-range-min="0" values="<?php
                    if (!empty($invoice_result)) {
                        foreach ($invoice_result as $v_invoice_result) {
                            echo $v_invoice_result . ',';
                        }
                    }
                    ?>">
                    </div>
                    <p class="m0">
                        <small>
                            <?php
                            if (!empty($invoice_result)) {
                                foreach ($invoice_result as $date => $v_invoice_result) {
                                    echo date('d', strtotime($date)) . ' ';
                                }
                            }
                            ?>
                        </small>
                    </p>

                </div>
            </div>

        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead">
                        <?php
                        if ($tolal_goal < $complete_achivement) {
                            $pending_goal = 0;
                        } else {
                            $pending_goal = $tolal_goal - $complete_achivement;
                        } ?>
                        <?= $pending_goal ?></p>
                    <p class="m0">
                        <small><?= lang('pending') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>
                <?php
                if (!empty($tolal_goal)) {
                    if ($tolal_goal <= $complete_achivement) {
                        $total_progress = 100;
                    } else {
                        $progress = ($complete_achivement / $tolal_goal) * 100;
                        $total_progress = round($progress);
                    }
                } else {
                    $total_progress = 0;
                }
                ?>
                <div class="col-xs-6 text-center pt">
                    <div class="inline ">
                        <div class="easypiechart text-success"
                             data-percent="<?= $total_progress ?>"
                             data-line-width="5" data-track-Color="#f0f0f0"
                             data-bar-color="#<?php
                             if ($total_progress == 100) {
                                 echo '8ec165';
                             } elseif ($total_progress >= 40 && $total_progress <= 50) {
                                 echo '5d9cec';
                             } elseif ($total_progress >= 51 && $total_progress <= 99) {
                                 echo '7266ba';
                             } else {
                                 echo 'fb6b5b';
                             }
                             ?>" data-rotate="270" data-scale-Color="false"
                             data-size="50"
                             data-animate="2000">
                                                        <span class="small "><?= $total_progress ?>
                                                            %</span>
                            <span class="easypie-text"><strong><?= lang('done') ?></strong></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
    $complete = 0;
    $not_started = 0;
    $in_progress = 0;
    $deferred = 0;
    $waiting_for_someone = 0;
    if (!empty($all_task_info)):foreach ($all_task_info as $v_task):
        if ($v_task->task_status == 'completed') {
            $complete += count($v_task->task_id);
        }
        if ($v_task->task_status == 'not_started') {
            $not_started += count($v_task->task_id);
        }
        if ($v_task->task_status == 'in_progress') {
            $in_progress += count($v_task->task_id);
        }
        if ($v_task->task_status == 'deferred') {
            $deferred += count($v_task->task_id);
        }
        if ($v_task->task_status == 'waiting_for_someone') {
            $waiting_for_someone += count($v_task->task_id);
        }
    endforeach;
    endif;
    if (!empty($all_task_info)) {
        $not_started_width = ($not_started / count($all_task_info)) * 100;
        $deferred_width = ($deferred / count($all_task_info)) * 100;
        $in_progress_width = ($in_progress / count($all_task_info)) * 100;
        $waiting_for_someone_width = ($waiting_for_someone / count($all_task_info)) * 100;
        $complete_width = ($complete / count($all_task_info)) * 100;
    } else {
        $not_started_width = 0;
        $deferred_width = 0;
        $in_progress_width = 0;
        $waiting_for_someone_width = 0;
        $complete_width = 0;
    }
    ?>
    <div class="row">
        <div class="col-lg-5ths pl-lg">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="t_status" search-type="<?= ('not_started') ?>"
                               id="not_started"
                               href="#"><?= lang('not_started') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $not_started ?>
                            / <?= count($all_task_info) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                             data-original-title="<?= $not_started_width ?>%"
                             style="width: <?= $not_started_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-5ths">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="t_status" search-type="<?= ('in_progress') ?>"
                               id="in_progress"
                               href="#"><?= lang('in_progress') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $in_progress ?>
                            / <?= count($all_task_info) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-warning " data-toggle="tooltip"
                             data-original-title="<?= $in_progress_width ?>%"
                             style="width: <?= $in_progress_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-5ths">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="t_status" search-type="<?= ('deferred') ?>"
                               id="deferred"
                               href="#"><?= lang('deferred') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $deferred ?>
                            / <?= count($all_task_info) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-info " data-toggle="tooltip"
                             data-original-title="<?= $deferred_width ?>%"
                             style="width: <?= $deferred_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-5ths">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="t_status" search-type="<?= ('waiting_for_someone') ?>"
                               id="waiting_for_someone"
                               href="#"><?= lang('waiting_for_someone') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $waiting_for_someone ?>
                            / <?= count($all_task_info) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-danger " data-toggle="tooltip"
                             data-original-title="<?= $waiting_for_someone_width ?>%"
                             style="width: <?= $waiting_for_someone_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-5ths pr-lg">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="t_status" search-type="<?= ('complete') ?>" id="completed"
                               href="#"><?= lang('complete') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $complete ?>
                            / <?= count($all_task_info) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-success " data-toggle="tooltip"
                             data-original-title="<?= $complete_width ?>%"
                             style="width: <?= $complete_width ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
    </div>
<?php } ?>


<?php

$created = can_action('54', 'created');
$edited = can_action('54', 'edited');
$deleted = can_action('54', 'deleted');

$kanban = $this->session->userdata('task_kanban');
$uri_segment = $this->uri->segment(4);
if (!empty($kanban)) {
    $tasks = 'kanban';
} elseif ($uri_segment == 'kanban') {
    $tasks = 'kanban';
} else {
    $tasks = 'list';
}

if ($tasks == 'kanban') {
    $text = 'list';
    $btn = 'purple';
} else {
    $text = 'kanban';
    $btn = 'danger';
}

?>
<div class="mb-lg pull-left">
    <div class="pull-left pr-lg">
        <a href="<?= base_url() ?>admin/tasks/all_task/<?= $text ?>"
           class="btn btn-xs btn-<?= $btn ?> pull-right"
           data-toggle="tooltip"
           data-placement="top" title="<?= lang('switch_to_' . $text) ?>">
            <i class="fa fa-undo"> </i><?= ' ' . lang('switch_to_' . $text) ?>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <?php if ($tasks == 'kanban') { ?>
            <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/kanban/kan-app.css"/>
            <div class="app-wrapper">
                <p class="total-card-counter" id="totalCards"></p>
                <div class="board" id="board"></div>
            </div>
            <?php include_once 'assets/plugins/kanban/tasks_kan-app.php'; ?>
        <?php } else { ?>
            <?php $is_department_head = is_department_head();
            if ($this->session->userdata('user_type') == 1 || !empty($is_department_head)) { ?>
                <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
                     data-title="<?php echo lang('filter_by'); ?>">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu group animated zoomIn"
                        style="width:300px;">
                        <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
                        <li class="divider"></li>

                        <li class="t_status" id="billable"><a href="#"><?php echo lang('billable'); ?></a></li>
                        <li class="t_status" id="not_billable"><a href="#"><?php echo lang('not_billable'); ?></a></li>
                        <li class="t_status" id="assigned_to_me"><a href="#"><?php echo lang('assigned_to_me'); ?></a>
                        </li>
                        <?php if (admin()) { ?>
                            <li class="filter_by" id="everyone"
                                search-type="by_staff">
                                <a href="#"><?php echo lang('assigned_to') . ' ' . lang('everyone'); ?></a>
                            </li>
                        <?php } ?>
                        <li class="dropdown-submenu pull-left  " id="from_account">
                            <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('project'); ?></a>
                            <ul class="dropdown-menu dropdown-menu-left from_account"
                                style="">
                                <?php
                                $cproject_info = $this->items_model->get_permission('tbl_project');
                                if (!empty($cproject_info)) {
                                    foreach ($cproject_info as $v_cproject) {
                                        ?>
                                        <li class="filter_by" id="<?= $v_cproject->project_id ?>"
                                            search-type="by_project">
                                            <a href="#"><?php echo $v_cproject->project_name; ?></a>
                                        </li>
                                    <?php }
                                }
                                ?>
                            </ul>
                        </li>
                        <div class="clearfix"></div>
                        <li class="dropdown-submenu pull-left " id="to_account">
                            <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('staff'); ?></a>
                            <ul class="dropdown-menu dropdown-menu-left to_account"
                                style="">
                                <?php
                                if (count($assign_user) > 0) { ?>
                                    <?php foreach ($assign_user as $v_staff) {
                                        ?>
                                        <li class="filter_by" id="<?= $v_staff->user_id ?>"
                                            search-type="by_staff">
                                            <a href="#"><?php echo fullname($v_staff->user_id); ?></a>
                                        </li>
                                    <?php }
                                    ?>
                                    <div class="clearfix"></div>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            <?php } ?>
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs">
                    <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#task_list"
                                                                       data-toggle="tab"><?= lang('all_task') ?></a>
                    </li>
                    <?php if (!empty($created) || !empty($edited)) { ?>
                        <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#assign_task"
                                                                           data-toggle="tab"><?= lang('assign_task') ?></a>
                        </li>
                        <li><a style="background-color: #1797be;color: #ffffff"
                               href="<?= base_url() ?>admin/tasks/import"><?= lang('import') . ' ' . lang('tasks') ?></a>
                        </li>
                    <?php } ?>
                </ul>
                <style type="text/css">
                    .custom-bulk-button {
                        display: initial;
                    }
                </style>
                <div class="tab-content bg-white">
                    <!-- Stock Category List tab Starts -->
                    <div
                        class="tab-pane <?= $active == 1 || $active == 'not_started' || $active == 'in_progress' || $active == 'completed' || $active == 'deferred' || $active == 'waiting_for_someone' ? 'active' : '' ?>"
                        id="task_list">
                        <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                            <div class="box-body">
                                <!-- Table -->
                                <table class="table table-striped DataTables bulk_table" id="DataTables" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <?php if (!empty($deleted)) { ?>
                                            <th data-orderable="false">
                                                <div class="checkbox c-checkbox">
                                                    <label class="needsclick">
                                                        <input id="select_all" type="checkbox">
                                                        <span class="fa fa-check"></span></label>
                                                </div>
                                            </th>
                                        <?php } ?>
                                        <th ><?= lang('task_name') ?></th>
                                        <th ><?= lang('due_date') ?></th>
                                        <th ><?= lang('status') ?></th>
                                        <th ><?= lang('assigned_to') ?></th>
                                        <?php $show_custom_fields = custom_form_table(3, null);
                                        if (!empty($show_custom_fields)) {
                                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                                if (!empty($c_label)) {
                                                    ?>
                                                    <th><?= $c_label ?> </th>
                                                <?php }
                                            }
                                        }
                                        ?>
                                        <th><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        list = base_url + "admin/tasks/tasksList";
                                        bulk_url = base_url + 'admin/tasks/bulk_delete';
                                        <?php if (admin_head()) { ?>
                                        $('.filtered > .dropdown-toggle').on('click', function () {
                                            if ($('.group').css('display') == 'block') {
                                                $('.group').css('display', 'none');
                                            } else {
                                                $('.group').css('display', 'block')
                                            }
                                        });
                                        $('.all_filter').on('click', function () {
                                            $('.to_account').removeAttr("style");
                                            $('.from_account').removeAttr("style");
                                        });
                                        $('.from_account li').on('click', function () {
                                            if ($('.to_account').css('display') == 'block') {
                                                $('.to_account').removeAttr("style");
                                                $('.from_account').css('display', 'block');
                                            } else {
                                                $('.from_account').css('display', 'block')
                                            }
                                        });

                                        $('.to_account li').on('click', function () {
                                            if ($('.from_account').css('display') == 'block') {
                                                $('.from_account').removeAttr("style");
                                                $('.to_account').css('display', 'block');
                                            } else {
                                                $('.to_account').css('display', 'block');
                                            }
                                        });
                                        $('.filter_by').on('click', function () {
                                            $('.filter_by').removeClass('active');
                                            $('.group').css('display', 'block');
                                            $(this).addClass('active');
                                            var filter_by = $(this).attr('id');
                                            if (filter_by) {
                                                filter_by = filter_by;
                                            } else {
                                                filter_by = '';
                                            }
                                            var search_type = $(this).attr('search-type');
                                            if (search_type) {
                                                search_type = '/' + search_type;
                                            } else {
                                                search_type = '';
                                            }
                                            table_url(base_url + "admin/tasks/tasksList/" + filter_by + search_type);
                                        });
                                        <?php }?>
                                        $('.t_status').on('click', function () {
                                            var result = $(this).attr('id');
                                            table_url(base_url + "admin/tasks/tasksList/" + result);
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($created) || !empty($edited)) { ?>
                        <!-- Add Stock Category tab Starts -->
                        <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="assign_task"
                             style="position: relative;">
                            <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                                <div class="panel-body">
                                    <form data-parsley-validate="" novalidate=""
                                          action="<?php echo base_url() ?>admin/tasks/save_task/<?php if (!empty($task_info->task_id)) echo $task_info->task_id; ?>"
                                          method="post" class="form-horizontal">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?= lang('task_name') ?><span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">
                                                <input type="text" name="task_name" required class="form-control"
                                                       value="<?php if (!empty($task_info->task_name)) echo $task_info->task_name; ?>"/>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($task_info->project_id)) {
                                            $project_id = $task_info->project_id;
                                        } elseif (!empty($project_id)) {
                                            $project_id = $project_id; ?>
                                            <input type="hidden" name="un_project_id" required class="form-control"
                                                   value="<?php echo $project_id ?>"/>
                                        <?php }
                                        if (!empty($task_info->opportunities_id)) {
                                            $opportunities_id = $task_info->opportunities_id;
                                        } elseif (!empty($opportunities_id)) {
                                            $opportunities_id = $opportunities_id; ?>
                                            <input type="hidden" name="un_opportunities_id" required
                                                   class="form-control"
                                                   value="<?php echo $opportunities_id ?>"/>
                                        <?php }
                                        if (!empty($task_info->leads_id)) {
                                            $leads_id = $task_info->leads_id;
                                        } elseif (!empty($leads_id)) {
                                            $leads_id = $leads_id; ?>
                                            <input type="hidden" name="un_leads_id" required class="form-control"
                                                   value="<?php echo $leads_id ?>"/>
                                        <?php }
                                        if (!empty($task_info->bug_id)) {
                                            $bug_id = $task_info->bug_id;
                                        } elseif (!empty($bug_id)) {
                                            $bug_id = $bug_id; ?>
                                            <input type="hidden" name="un_bug_id" required class="form-control"
                                                   value="<?php echo $bug_id ?>"/>
                                        <?php }
                                        if (!empty($task_info->goal_tracking_id)) {
                                            $goal_tracking_id = $task_info->goal_tracking_id;
                                        } elseif (!empty($goal_tracking_id)) {
                                            $goal_tracking_id = $goal_tracking_id; ?>
                                            <input type="hidden" name="un_goal_tracking_id" required
                                                   class="form-control"
                                                   value="<?php echo $goal_tracking_id ?>"/>
                                        <?php } ?>
                                        <?php
                                        if (!empty($task_info->sub_task_id)) {
                                            $sub_task_id = $task_info->sub_task_id;
                                        } elseif (!empty($sub_task_id)) {
                                            $sub_task_id = $sub_task_id; ?>
                                            <input type="hidden" name="un_sub_task_id" required
                                                   class="form-control"
                                                   value="<?php echo $sub_task_id ?>"/>
                                        <?php } ?>
                                        <?php
                                        if (!empty($task_info->transactions_id)) {
                                            $transactions_id = $task_info->transactions_id;
                                        } elseif (!empty($transactions_id)) {
                                            $transactions_id = $transactions_id; ?>
                                            <input type="hidden" name="un_transactions_id" required
                                                   class="form-control"
                                                   value="<?php echo $transactions_id ?>"/>
                                        <?php } ?>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('related_to') ?> </label>
                                            <div class="col-sm-5">
                                                <select name="related_to" class="form-control" id="check_related"
                                                        onchange="get_related_moduleName(this.value)">
                                                    <option
                                                        value="0"> <?= lang('none') ?> </option>
                                                    <option
                                                        value="project" <?= (!empty($project_id) ? 'selected' : '') ?>> <?= lang('project') ?> </option>
                                                    <option
                                                        value="opportunities" <?= (!empty($opportunities_id) ? 'selected' : '') ?>> <?= lang('opportunities') ?> </option>
                                                    <option
                                                        value="leads" <?= (!empty($leads_id) ? 'selected' : '') ?>> <?= lang('leads') ?> </option>
                                                    <option
                                                        value="bug" <?= (!empty($bug_id) ? 'selected' : '') ?>> <?= lang('bugs') ?> </option>
                                                    <option
                                                        value="goal" <?= (!empty($goal_tracking_id) ? 'selected' : '') ?>> <?= lang('goal_tracking') ?> </option>
                                                    <option
                                                        value="sub_task" <?= (!empty($sub_task_id) ? 'selected' : '') ?>> <?= lang('tasks') ?> </option>
                                                    <option
                                                            value="expenses" <?= (!empty($transactions_id) ? 'selected' : '') ?>> <?= lang('expenses') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="related_to">

                                        </div>
                                        <?php if (empty($project_id)) { ?>
                                            <div class="form-group company"
                                                 id="milestone_show">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('milestones') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="milestones_id" id="milestone"
                                                            class="form-control company">
                                                        <?php
                                                        if (!empty($project_id)) {
                                                            $all_milestones_info = $this->db->where('project_id', $project_id)->get('tbl_milestones')->result();
                                                        } else {
                                                            $project_milestone = $this->db->get('tbl_project')->row();
                                                            $all_milestones_info = $this->db->where('project_id', $project_milestone->project_id)->get('tbl_milestones')->result();
                                                        }
                                                        if (!empty($all_milestones_info)) {
                                                            foreach ($all_milestones_info as $v_milestones) {
                                                                ?>
                                                                <option
                                                                    value="<?= $v_milestones->milestones_id ?>" <?php
                                                                if (!empty($task_info->milestones_id)) {
                                                                    echo $v_milestones->milestones_id == $task_info->milestones_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_milestones->milestone_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php

                                        if (!empty($project_id)):
                                            $project_info = $this->db->where('project_id', $project_id)->get('tbl_project')->row();
                                            $all_project = $this->tasks_model->get_permission('tbl_project');

                                            ?>
                                            <div class="form-group <?= $project_id ? 'project_module' : 'company' ?>">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('project') ?> <span
                                                        class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="project_id" style="width: 100%"
                                                            class="select_box <?= $project_id ? 'project_module' : 'company' ?>"
                                                            required="1" onchange="get_milestone_by_id(this.value)">
                                                        <?php
                                                        if (!empty($all_project)) {
                                                            foreach ($all_project as $v_project) {
                                                                ?>
                                                                <option value="<?= $v_project->project_id ?>" <?php
                                                                if (!empty($project_id)) {
                                                                    echo $v_project->project_id == $project_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_project->project_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="form-group <?= $project_id ? 'milestone_module' : 'company' ?>"
                                                 id="milestone_show">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('milestones') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="milestones_id" id="milestone"
                                                            class="form-control <?= $project_id ? 'milestone_module' : 'company' ?>">
                                                        <option><?= lang('none') ?></option>
                                                        <?php
                                                        $all_milestones_info = $this->db->where('project_id', $project_id)->get('tbl_milestones')->result();
                                                        if (!empty($all_milestones_info)) {
                                                            foreach ($all_milestones_info as $v_milestones) {
                                                                ?>
                                                                <option
                                                                    value="<?= $v_milestones->milestones_id ?>" <?php
                                                                if (!empty($task_info->milestones_id)) {
                                                                    echo $v_milestones->milestones_id == $task_info->milestones_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_milestones->milestone_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($opportunities_id)): ?>
                                            <div
                                                class="form-group <?= $opportunities_id ? 'opportunities_module' : 'company' ?>"
                                                id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('opportunities') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="opportunities_id" style="width: 100%"
                                                            class="select_box <?= $opportunities_id ? 'opportunities_module' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_opportunities_info = $this->tasks_model->get_permission('tbl_opportunities');
                                                        if (!empty($all_opportunities_info)) {
                                                            foreach ($all_opportunities_info as $v_opportunities) {
                                                                ?>
                                                                <option
                                                                    value="<?= $v_opportunities->opportunities_id ?>" <?php
                                                                if (!empty($opportunities_id)) {
                                                                    echo $v_opportunities->opportunities_id == $opportunities_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_opportunities->opportunity_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($leads_id)): ?>
                                            <div class="form-group <?= $leads_id ? 'leads_module' : 'company' ?>"
                                                 id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('leads') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="leads_id" style="width: 100%"
                                                            class="select_box <?= $leads_id ? 'leads_module' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_leads_info = $this->tasks_model->get_permission('tbl_leads');
                                                        if (!empty($all_leads_info)) {
                                                            foreach ($all_leads_info as $v_leads) {
                                                                ?>
                                                                <option value="<?= $v_leads->leads_id ?>" <?php
                                                                if (!empty($leads_id)) {
                                                                    echo $v_leads->leads_id == $leads_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_leads->lead_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>

                                        <?php if (!empty($bug_id)): ?>
                                            <div class="form-group <?= $bug_id ? 'bugs_module' : 'company' ?>"
                                                 id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('bugs') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="bug_id" style="width: 100%"
                                                            class="select_box <?= $bug_id ? 'bugs_module' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_bugs_info = $this->tasks_model->get_permission('tbl_bug');
                                                        if (!empty($all_bugs_info)) {
                                                            foreach ($all_bugs_info as $v_bugs) {
                                                                ?>
                                                                <option value="<?= $v_bugs->bug_id ?>" <?php
                                                                if (!empty($bug_id)) {
                                                                    echo $v_bugs->bug_id == $bug_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_bugs->bug_title ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($goal_tracking_id)): ?>
                                            <div
                                                class="form-group <?= $goal_tracking_id ? 'goal_tracking' : 'company' ?>"
                                                id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('goal_tracking') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="goal_tracking_id" style="width: 100%"
                                                            class="select_box <?= $goal_tracking_id ? 'goal_tracking' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_goal_info = $this->tasks_model->get_permission('tbl_goal_tracking');
                                                        if (!empty($all_goal_info)) {
                                                            foreach ($all_goal_info as $v_goal) {
                                                                ?>
                                                                <option value="<?= $v_goal->goal_tracking_id ?>" <?php
                                                                if (!empty($goal_tracking_id)) {
                                                                    echo $v_goal->goal_tracking_id == $goal_tracking_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_goal->subject ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($sub_task_id)): ?>
                                            <div
                                                class="form-group <?= $sub_task_id ? 'sub_tasks' : 'company' ?>"
                                                id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('tasks') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="sub_task_id" style="width: 100%"
                                                            class="select_box <?= $sub_task_id ? 'sub_tasks' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_sub_tasks = $this->tasks_model->get_permission('tbl_task');
                                                        if (!empty($all_sub_tasks)) {
                                                            foreach ($all_sub_tasks as $v_s_tasks) {
                                                                ?>
                                                                <option value="<?= $v_s_tasks->task_id ?>" <?php
                                                                if (!empty($sub_task_id)) {
                                                                    echo $v_s_tasks->task_id == $sub_task_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_s_tasks->task_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($transactions_id)): ?>
                                            <div
                                                    class="form-group <?= $transactions_id ? 'expenses' : 'company' ?>"
                                                    id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('expenses') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-5">
                                                    <select name="transactions_id" style="width: 100%"
                                                            class="select_box <?= $transactions_id ? 'expenses' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_expenses = $this->tasks_model->get_permission('tbl_transactions');
                                                        if (!empty($all_expenses)) {
                                                            foreach ($all_expenses as $v_expenses) {
                                                                ?>
                                                                <option value="<?= $v_expenses->transactions_id ?>" <?php
                                                                if (!empty($transactions_id)) {
                                                                    echo $v_expenses->transactions_id == $transactions_id ? 'selected' : '';
                                                                }
                                                                ?>><?php
                                                                    echo $v_expenses->name;
                                                                    if (!empty($v_expenses->reference)) {
                                                                        echo '#' . $v_expenses->reference;
                                                                    } ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('start_date') ?></label>
                                            <div class="col-lg-5">
                                                <div class="input-group">
                                                    <input type="text" name="task_start_date"
                                                           class="form-control start_date"
                                                           value="<?php
                                                           if (!empty($task_info->task_start_date)) {
                                                               echo $task_info->task_start_date;
                                                           } ?>"
                                                           data-date-format="<?= config_item('date_picker_format'); ?>">
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('due_date') ?><span
                                                    class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <div class="input-group">
                                                    <input type="text" name="due_date" required="" value="<?php
                                                    if (!empty($task_info->due_date)) {
                                                        echo $task_info->due_date;
                                                    }
                                                    ?>" class="form-control end_date"
                                                           data-date-format="<?= config_item('date_picker_format'); ?>">
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label
                                                class="col-sm-3 control-label"><?= lang('project_hourly_rate') ?></label>
                                            <div class="col-sm-5">
                                                <input type="text" data-parsley-type="number" name="hourly_rate"
                                                       class="form-control"
                                                       value="<?php if (!empty($task_info->hourly_rate)) echo $task_info->hourly_rate; ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?= lang('estimated_hour') ?></label>
                                            <div class="col-sm-5">
                                                <input type="number" step="0.01" data-parsley-type="number"
                                                       name="task_hour"
                                                       class="form-control"
                                                       value="<?php
                                                       if (!empty($task_info->task_hour)) {
                                                           $result = explode(':', $task_info->task_hour);
                                                           if (empty($result[1])) {
                                                               $result1 = 0;
                                                           } else {
                                                               $result1 = $result[1];
                                                           }
                                                           echo $result[0] . '.' . $result1;
                                                       }
                                                       ?>"/>
                                            </div>

                                        </div>
                                        <script src="<?= base_url() ?>assets/js/jquery-ui.js"></script>
                                        <?php $direction = $this->session->userdata('direction');
                                        if (!empty($direction) && $direction == 'rtl') {
                                            $RTL = 'on';
                                        } else {
                                            $RTL = config_item('RTL');
                                        }
                                        ?>
                                        <?php
                                        if (!empty($RTL)) { ?>
                                            <!-- bootstrap-editable -->
                                            <script type="text/javascript"
                                                    src="<?= base_url() ?>assets/plugins/jquery-ui/jquery.ui.slider-rtl.js"></script>
                                        <?php }
                                        ?>
                                        <style>

                                            .ui-widget.ui-widget-content {
                                                border: 1px solid #dde6e9;
                                            }

                                            .ui-corner-all, .ui-corner-bottom, .ui-corner-left, .ui-corner-bl {
                                                border: 7px solid #28a9f1;
                                            }

                                            .ui-widget-content {
                                                border: 1px solid #dddddd;
                                                /*background: #E1E4E9;*/
                                                color: #333333;
                                            }

                                            .ui-slider {
                                                position: relative;
                                                text-align: left;
                                            }

                                            .ui-slider-horizontal {
                                                height: 1em;
                                            }

                                            .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active {
                                                border: 1px solid #1797be;
                                                background: #1797be;
                                                font-weight: normal;
                                                color: #454545;
                                            }

                                            .ui-slider-horizontal .ui-slider-handle {
                                                top: -.3em;
                                                margin-left: -.1em;;
                                                margin-right: -.1em;;
                                            }

                                            .ui-slider .ui-slider-handle:hover {
                                                background: #1797be;
                                            }

                                            .ui-slider .ui-slider-handle {
                                                position: absolute;
                                                z-index: 2;
                                                width: 1.2em;;
                                                height: 1.5em;
                                                cursor: default;
                                                -ms-touch-action: none;
                                                touch-action: none;

                                            }

                                            .ui-state-disabled, .ui-widget-content .ui-state-disabled, .ui-widget-header .ui-state-disabled {
                                                opacity: .35;
                                                filter: Alpha(Opacity=35);
                                                background-image: none;
                                            }

                                            .ui-state-disabled {
                                                cursor: default !important;
                                                pointer-events: none;
                                            }

                                            .ui-slider.ui-state-disabled .ui-slider-handle, .ui-slider.ui-state-disabled .ui-slider-range {
                                                filter: inherit;
                                            }

                                            .ui-slider-range, .ui-widget-header, .ui-slider-handle:before, .list-group-item.active, .list-group-item.active:hover, .list-group-item.active:focus, .icon-frame {
                                                background-image: none;
                                                background: #28a9f1;
                                            }

                                        </style>

                                        <?php
                                        if (!empty($task_info)) {
                                            $value = $this->tasks_model->get_task_progress($task_info->task_id);
                                        } else {
                                            $value = 0;
                                        }
                                        ?>
                                        <div class="form-group">
                                            <label
                                                class="col-lg-3 control-label"><?php echo lang('progress'); ?> </label>
                                            <div class="col-lg-5">
                                                <?php echo form_hidden('task_progress', $value); ?>
                                                <div
                                                    class="project_progress_slider project_progress_slider_horizontal mbot15"></div>

                                                <div class="input-group">
                                <span class="input-group-addon">
                                     <div class="">
                                         <div class="pull-left mt">
                                             <?php echo lang('progress'); ?>
                                             <span class="label_progress "><?php echo $value; ?>%</span>
                                         </div>
                                         <div class="checkbox c-checkbox pull-right" data-toggle="tooltip"
                                              data-placement="top"
                                              title="<?php echo lang('calculate_progress_through_sub_tasks'); ?>">
                                             <label class="needsclick">
                                                 <input class="select_one"
                                                        type="checkbox" <?php if ((!empty($task_info) && $task_info->calculate_progress == 'through_sub_tasks')) {
                                                     echo 'checked';
                                                 } ?> name="calculate_progress" value="through_sub_tasks"
                                                        id="through_sub_tasks">
                                                 <span class="fa fa-check"></span>
                                                 <small><?php echo lang('through_sub_tasks'); ?></small>
                                             </label>
                                         </div>
                                         <div class="checkbox c-checkbox pull-right" data-toggle="tooltip"
                                              data-placement="top"
                                              title="<?php echo lang('calculate_progress_through_task_hours'); ?>">
                                             <label class="needsclick">
                                                 <input class="select_one"
                                                        type="checkbox" <?php if ((!empty($task_info) && $task_info->calculate_progress == 'through_tasks_hours')) {
                                                     echo 'checked';
                                                 } ?> name="calculate_progress" value="through_tasks_hours"
                                                        id="through_tasks_hours">
                                                 <span class="fa fa-check"></span>
                                                 <small><?php echo lang('through_tasks_hours'); ?></small>
                                             </label>
                                         </div>
                                     </div>
                                </span>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            $(document).ready(function () {
                                                var progress_input = $('input[name="task_progress"]');
                                                <?php if ((!empty($task_info) && $task_info->calculate_progress == 'through_tasks_hours')) {?>
                                                var progress_from_tasks = $('#through_tasks_hours');
                                                <?php }elseif ((!empty($task_info) && $task_info->calculate_progress == 'through_sub_tasks')){?>
                                                var progress_from_tasks = $('#through_sub_tasks');
                                                <?php }else{?>
                                                var progress_from_tasks = $('.select_one');
                                                <?php } ?>

                                                var progress = progress_input.val();
                                                $('.project_progress_slider').slider({
                                                    range: "min",
                                                    <?php
                                                    if (!empty($RTL)) { ?>
                                                    isRTL: true,
                                                    <?php }
                                                    ?>
                                                    min: 0,
                                                    max: 100,
                                                    value: progress,
                                                    disabled: progress_from_tasks.prop('checked'),
                                                    slide: function (event, ui) {
                                                        progress_input.val(ui.value);
                                                        $('.label_progress').html(ui.value + '%');
                                                    }
                                                });
                                                progress_from_tasks.on('change', function () {
                                                    var _checked = $(this).prop('checked');
                                                    $('.project_progress_slider').slider({
                                                        disabled: _checked,
                                                    });
                                                });
                                            })
                                            ;
                                        </script>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('task_status') ?> <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-5">
                                                <select name="task_status" class="form-control" required>
                                                    <option
                                                        value="not_started" <?= (!empty($task_info->task_status) && $task_info->task_status == 'not_started' ? 'selected' : '') ?>> <?= lang('not_started') ?> </option>
                                                    <option
                                                        value="in_progress" <?= (!empty($task_info->task_status) && $task_info->task_status == 'in_progress' ? 'selected' : '') ?>> <?= lang('in_progress') ?> </option>
                                                    <option
                                                        value="completed" <?= (!empty($task_info->task_status) && $task_info->task_status == 'completed' ? 'selected' : '') ?>> <?= lang('completed') ?> </option>
                                                    <option
                                                        value="deferred" <?= (!empty($task_info->task_status) && $task_info->task_status == 'deferred' ? 'selected' : '') ?>> <?= lang('deferred') ?> </option>
                                                    <option
                                                        value="waiting_for_someone" <?= (!empty($task_info->task_status) && $task_info->task_status == 'waiting_for_someone' ? 'selected' : '') ?>> <?= lang('waiting_for_someone') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('task_description') ?>
                                            </label>
                                            <div class="col-sm-8">
                                        <textarea class="form-control textarea"
                                                  name="task_description"><?php if (!empty($task_info->task_description)) echo $task_info->task_description; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('billable') ?>
                                                <span class="required">*</span></label>
                                            <div class="col-sm-8">
                                                <input data-toggle="toggle" name="billable" value="Yes" <?php
                                                if (!empty($task_info) && $task_info->billable == 'Yes') {
                                                    echo 'checked';
                                                }
                                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                       data-onstyle="success" data-offstyle="danger" type="checkbox">
                                            </div>
                                        </div>
                                        <?php if (!empty($project_id)): ?>
                                            <div class="form-group">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('visible_to_client') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input data-toggle="toggle" name="client_visible" value="Yes" <?php
                                                    if (!empty($task_info) && $task_info->client_visible == 'Yes') {
                                                        echo 'checked';
                                                    }
                                                    ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                           data-onstyle="success" data-offstyle="danger"
                                                           type="checkbox">
                                                </div>
                                            </div>
                                        <?php endif ?>

                                        <?php
                                        if (!empty($task_info)) {
                                            $task_id = $task_info->task_id;
                                        } else {
                                            $task_id = null;
                                        }
                                        ?>
                                        <?= custom_form_Fields(3, $task_id); ?>

                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('assined_to') ?> <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-9">
                                                <div class="checkbox c-radio needsclick">
                                                    <label class="needsclick">
                                                        <input id="" <?php
                                                        if (!empty($task_info->permission) && $task_info->permission == 'all') {
                                                            echo 'checked';
                                                        } elseif (empty($task_info)) {
                                                            echo 'checked';
                                                        }
                                                        ?> type="radio" name="permission" value="everyone">
                                                        <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                                        <i title="<?= lang('permission_for_all') ?>"
                                                           class="fa fa-question-circle" data-toggle="tooltip"
                                                           data-placement="top"></i>
                                                    </label>
                                                </div>
                                                <div class="checkbox c-radio needsclick">
                                                    <label class="needsclick">
                                                        <input id="" <?php
                                                        if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                                            echo 'checked';
                                                        }
                                                        ?> type="radio" name="permission" value="custom_permission"
                                                        >
                                                        <span
                                                            class="fa fa-circle"></span><?= lang('custom_permission') ?>
                                                        <i
                                                            title="<?= lang('permission_for_customization') ?>"
                                                            class="fa fa-question-circle" data-toggle="tooltip"
                                                            data-placement="top"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group <?php
                                        if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                            echo 'show';
                                        }
                                        ?>" id="permission_user_1">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <div class="col-sm-9">
                                                <?php
                                                if (!empty($assign_user)) {
                                                    foreach ($assign_user as $key => $v_user) {

                                                        if ($v_user->role_id == 1) {
                                                            $disable = true;
                                                            $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                                        } else {
                                                            $disable = false;
                                                            $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                                        }

                                                        ?>
                                                        <div class="checkbox c-checkbox needsclick">
                                                            <label class="needsclick">
                                                                <input type="checkbox"
                                                                    <?php
                                                                    if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                                                        $get_permission = json_decode($task_info->permission);
                                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                                            if ($user_id == $v_user->user_id) {
                                                                                echo 'checked';
                                                                            }
                                                                        }

                                                                    }
                                                                    ?>
                                                                       value="<?= $v_user->user_id ?>"
                                                                       name="assigned_to[]"
                                                                       class="needsclick">
                                                        <span
                                                            class="fa fa-check"></span><?= $v_user->username . ' ' . $role ?>
                                                            </label>

                                                        </div>
                                                        <div class="action_1 p
                                                <?php

                                                        if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                                            $get_permission = json_decode($task_info->permission);

                                                            foreach ($get_permission as $user_id => $v_permission) {
                                                                if ($user_id == $v_user->user_id) {
                                                                    echo 'show';
                                                                }
                                                            }

                                                        }
                                                        ?>
                                                " id="action_1<?= $v_user->user_id ?>">
                                                            <label class="checkbox-inline c-checkbox">
                                                                <input id="<?= $v_user->user_id ?>" checked
                                                                       type="checkbox"
                                                                       name="action_1<?= $v_user->user_id ?>[]"
                                                                       disabled
                                                                       value="view">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                                            </label>
                                                            <label class="checkbox-inline c-checkbox">
                                                                <input <?php if (!empty($disable)) {
                                                                    echo 'disabled' . ' ' . 'checked';
                                                                } ?> id="<?= $v_user->user_id ?>"
                                                                    <?php

                                                                    if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                                                        $get_permission = json_decode($task_info->permission);

                                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                                            if ($user_id == $v_user->user_id) {
                                                                                if (in_array('edit', $v_permission)) {
                                                                                    echo 'checked';
                                                                                };

                                                                            }
                                                                        }

                                                                    }
                                                                    ?>
                                                                     type="checkbox"
                                                                     value="edit"
                                                                     name="action_<?= $v_user->user_id ?>[]">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                                            </label>
                                                            <label class="checkbox-inline c-checkbox">
                                                                <input <?php if (!empty($disable)) {
                                                                    echo 'disabled' . ' ' . 'checked';
                                                                } ?> id="<?= $v_user->user_id ?>"
                                                                    <?php

                                                                    if (!empty($task_info->permission) && $task_info->permission != 'all') {
                                                                        $get_permission = json_decode($task_info->permission);
                                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                                            if ($user_id == $v_user->user_id) {
                                                                                if (in_array('delete', $v_permission)) {
                                                                                    echo 'checked';
                                                                                };
                                                                            }
                                                                        }

                                                                    }
                                                                    ?>
                                                                     name="action_<?= $v_user->user_id ?>[]"
                                                                     type="checkbox"
                                                                     value="delete">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('delete') ?>
                                                            </label>
                                                            <input id="<?= $v_user->user_id ?>" type="hidden"
                                                                   name="action_<?= $v_user->user_id ?>[]" value="view">

                                                        </div>


                                                        <?php
                                                    }
                                                }
                                                ?>


                                            </div>
                                        </div>

                                        <div class="btn-bottom-toolbar text-right">
                                            <?php
                                            if (!empty($task_info)) { ?>
                                                <button type="submit"
                                                        class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                                <button type="button" onclick="goBack()"
                                                        class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                                            <?php } else {
                                                ?>
                                                <button type="submit"
                                                        class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                                            <?php }
                                            ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
