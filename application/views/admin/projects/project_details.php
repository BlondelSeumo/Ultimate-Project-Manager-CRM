<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
    .note-editor .note-editable {
        height: 150px;
    }
</style>
<?php
$open_tasks = null;
$comment_type = 'projects';
$can_edit = $this->items_model->can_action('tbl_project', 'edit', array('project_id' => $project_details->project_id));
if (!empty($project_details->client_id)) {
    $currency = $this->items_model->client_currency_symbol($project_details->client_id);
} else {
    $currency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
}
$comment_details = $this->db->where(array('project_id' => $project_details->project_id, 'comments_reply_id' => '0', 'task_attachment_id' => '0', 'uploaded_files_id' => '0'))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
$all_milestones_info = $this->db->where('project_id', $project_details->project_id)->get('tbl_milestones')->result();
$all_task_info = $this->db->where('project_id', $project_details->project_id)->order_by('task_id', 'DESC')->get('tbl_task')->result();
$all_bugs_info = $this->db->where('project_id', $project_details->project_id)->order_by('bug_id', 'DESC')->get('tbl_bug')->result();
$total_timer = $this->db->where(array('project_id' => $project_details->project_id, 'start_time !=' => 0, 'end_time !=' => 0,))->get('tbl_tasks_timer')->result();
$all_invoice_info = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_invoices')->result();
$all_credit_note_info = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_credit_note')->result();
$all_estimates_info = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_estimates')->result();

$all_tickets_info = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_tickets')->result();

$all_expense_info = $this->db->where(array('project_id' => $project_details->project_id, 'type' => 'Expense'))->get('tbl_transactions')->result();

$total_expense = $this->db->select_sum('amount')->where(array('project_id' => $project_details->project_id, 'type' => 'Expense'))->get('tbl_transactions')->row();
$billable_expense = $this->db->select_sum('amount')->where(array('project_id' => $project_details->project_id, 'type' => 'Expense', 'billable' => 'Yes'))->get('tbl_transactions')->row();
$not_billable_expense = $this->db->select_sum('amount')->where(array('project_id' => $project_details->project_id, 'type' => 'Expense', 'billable' => 'No'))->get('tbl_transactions')->row();

$activities_info = $this->db->where(array('module' => 'projects', 'module_field_id' => $project_details->project_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();

$project_hours = $this->items_model->calculate_project('project_hours', $project_details->project_id);

if ($project_details->billing_type == 'tasks_hours' || $project_details->billing_type == 'tasks_and_project_hours' || $project_details->billing_type == 'tasks_sub_tasks_and_project_hours') {
    $tasks_hours = $this->items_model->total_project_hours($project_details->project_id, '', true);
    $open_tasks = true;
}

$project_cost = $this->items_model->calculate_project('project_cost', $project_details->project_id);
$where = array('user_id' => $this->session->userdata('user_id'), 'module_id' => $project_details->project_id, 'module_name' => 'project');
$check_existing = $this->items_model->check_by($where, 'tbl_pinaction');
if (!empty($check_existing)) {
    $url = 'remove_todo/' . $check_existing->pinaction_id;
    $btn = 'danger';
    $title = lang('remove_todo');
} else {
    $url = 'add_todo_list/project/' . $project_details->project_id;
    $btn = 'warning';
    $title = lang('add_todo_list');
}
$this->load->helper('date');
$totalDays = round((human_to_unix($project_details->end_date . ' 00:00') - human_to_unix($project_details->start_date . ' 00:00')) / 3600 / 24);
$TotalGone = $totalDays;
$tprogress = 100;
if (human_to_unix($project_details->start_date . ' 00:00') < time() && human_to_unix($project_details->end_date . ' 00:00') > time()) {
    $TotalGone = round((human_to_unix($project_details->end_date . ' 00:00') - time()) / 3600 / 24);
    $tprogress = $TotalGone / $totalDays * 100;

}
if (human_to_unix($project_details->end_date . ' 00:00') < time()) {
    $TotalGone = 0;
    $tprogress = 0;
}
if (strtotime(date('Y-m-d')) > strtotime($project_details->end_date . '00:00')) {
    $lang = lang('days_gone');
} else {
    $lang = lang('days_left');
}
$project_settings = json_decode($project_details->project_settings);
if (empty(admin())) {
    if (!empty($project_settings) && in_array('show_staff_finance_overview', $project_settings)) {
        $staff_finance = true;
    } else {
        $staff_finance = false;
    }
} else {
    $staff_finance = true;
}
$edited = can_action('57', 'edited');
?>
<div class="row">
    <div class="col-sm-3">
        <?php if (!empty($can_edit) && !empty($edited)) { ?>
            <span data-placement="top" data-toggle="tooltip" title="<?= lang('generate_invoice') ?>">
             <a data-toggle="modal" data-target="#myModal"
                href="<?= base_url() ?>admin/projects/invoice/<?= $project_details->project_id ?>"
                class="mr-lg btn btn-info"><i class="fa fa-money"></i> <?= lang('invoice') ?>
             </a>
            </span>

        <?php } ?>
        <?php if (timer_status('projects', $project_details->project_id, 'on')) { ?>
            <a data-toggle="tooltip" data-placement="top" title="<?= lang('stop_timer') ?>" class="btn btn-danger "
               href="<?= base_url() ?>admin/projects/tasks_timer/off/<?= $project_details->project_id ?>"><i
                        class="fa fa-clock-o fa-spin"></i></a>
        <?php } else {
            ?>
            <a data-toggle="tooltip" data-placement="top" title="<?= lang('start_timer') ?>" class="btn btn-success"
               href="<?= base_url() ?>admin/projects/tasks_timer/on/<?= $project_details->project_id ?>"><i
                        class="fa fa-clock-o"></i></a>
        <?php }
        ?>
        <?php if (!empty($can_edit) && !empty($edited)) { ?>
            <a data-toggle="modal" data-target="#myModal" title="<?= lang('clone_project') ?>"
               href="<?= base_url() ?>admin/projects/clone_project/<?= $project_details->project_id ?>"
               class="btn btn-purple pull-right"><i class="fa fa-copy"></i></a>
        <?php } ?>

        <a data-toggle="tooltip" data-placement="top" title="<?= $title ?>"
           href="<?= base_url() ?>admin/projects/<?= $url ?>"
           class="btn btn-<?= $btn ?>"><i class="fa fa-thumb-tack"></i></a>


        <ul class="mt nav nav-pills nav-stacked navbar-custom-nav">
            <li class="btn-success" style="margin-right: 0px; "></li>
            <li class="<?= $active == 1 ? 'active' : '' ?>" style="margin-right: 0px; "><a href="#task_details"
                                                                                           data-toggle="tab"><?= lang('project_details') ?></a>
            </li>

            <li class="<?= $active == 15 ? 'active' : '' ?>"><a
                        href="<?= base_url() ?>admin/projects/project_details/<?= $project_details->project_id ?>/15"><?= lang('calendar') ?></a>
            </li>

            <li class="<?= $active == 3 ? 'active' : '' ?>"><a href="#task_comments"
                                                               data-toggle="tab"><?= lang('comments') ?><strong
                            class="pull-right"><?= (!empty($comment_details) ? count($comment_details) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#task_attachments"
                                                               data-toggle="tab"><?= lang('attachment') ?><strong
                            class="pull-right"><?= (!empty($project_files_info) ? count($project_files_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 5 ? 'active' : '' ?>"><a href="#milestones"
                                                               data-toggle="tab"><?= lang('milestones') ?><strong
                            class="pull-right"><?= (!empty($all_milestones_info) ? count($all_milestones_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#task" data-toggle="tab"><?= lang('tasks') ?>
                    <strong
                            class="pull-right"><?= (!empty($all_task_info) ? count($all_task_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 9 ? 'active' : '' ?>"><a href="#bugs" data-toggle="tab"><?= lang('bugs') ?><strong
                            class="pull-right"><?= (!empty($all_bugs_info) ? count($all_bugs_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 13 ? 'active' : '' ?>"><a
                        href="<?= base_url() ?>admin/projects/project_details/<?= $project_details->project_id ?>/13"><?= lang('gantt') ?></a>
            </li>
            <li class="<?= $active == 8 ? 'active' : '' ?>"><a href="#task_notes"
                                                               data-toggle="tab"><?= lang('notes') ?></a></li>
            <li class="<?= $active == 7 ? 'active' : '' ?>"><a href="#timesheet"
                                                               data-toggle="tab"><?= lang('timesheet') ?><strong
                            class="pull-right"><?= (!empty($total_timer) ? count($total_timer) : null) ?></strong></a>
            </li>

            <li class="<?= $active == 14 ? 'active' : '' ?>"><a href="#project_tickets"
                                                                data-toggle="tab"><?= lang('tickets') ?><strong
                            class="pull-right"><?= (!empty($all_tickets_info) ? count($all_tickets_info) : null) ?></strong></a>
            </li>

            <li class="<?= $active == 11 ? 'active' : '' ?>"><a href="#invoice"
                                                                data-toggle="tab"><?= lang('invoice') ?><strong
                            class="pull-right"><?= (!empty($all_invoice_info) ? count($all_invoice_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 18 ? 'active' : '' ?>"><a href="#credit_note"
                                                                data-toggle="tab"><?= lang('credit_note') ?><strong
                            class="pull-right"><?= (!empty($all_credit_note_info) ? count($all_credit_note_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 12 ? 'active' : '' ?>"><a href="#estimates"
                                                                data-toggle="tab"><?= lang('estimates') ?><strong
                            class="pull-right"><?= (!empty($all_estimates_info) ? count($all_estimates_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 10 ? 'active' : '' ?>"><a href="#expense"
                                                                data-toggle="tab"><?= lang('expense') ?><strong
                            class="pull-right"><?php
                        echo(!empty($all_expense_info) ? display_money($total_expense->amount, $currency->symbol) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 16 ? 'active' : '' ?>"><a href="#project_settings"
                                                                data-toggle="tab"><?= lang('project_settings') ?></a>
            </li>
            <li class="<?= $active == 2 ? 'active' : '' ?>" style="margin-right: 0px; "><a href="#activities"
                                                                                           data-toggle="tab"><?= lang('activities') ?>
                    <strong
                            class="pull-right"><?= (!empty($activities_info) ? count($activities_info) : null) ?></strong></a>
            </li>
        </ul>
    </div>
    <div class="col-sm-9 ">
        <a data-toggle="tooltip" data-placement="top" title="<?= lang('export_report') ?>"
           href="<?= base_url() ?>admin/projects/export_project/<?= $project_details->project_id ?>"
           class="btn btn-danger"><i class="fa fa-file-pdf-o"></i></a>
        <!-- Tabs within a box -->
        <div class="tab-content mt" style="border: 0;padding:0;">
            <!-- Task Details tab Starts -->
            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="task_details" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?php if (!empty($project_details->project_name)) echo $project_details->project_name; ?>
                            <div class="pull-right text-sm">
                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                    <a href="<?= base_url() ?>admin/projects/index/<?= $project_details->project_id ?>"><?= lang('edit') . ' ' . lang('project') ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body form-horizontal task_details">
                        <?php
                        $client_info = $this->db->where('client_id', $project_details->client_id)->get('tbl_client')->row();
                        if (!empty($client_info)) {
                            $name = $client_info->name;
                        } else {
                            $name = '-';
                        }
                        ?>
                        <?php $project_details_view = config_item('project_details_view');
                        if (!empty($project_details_view) && $project_details_view == '2') {
                            ?>
                            <div class="row">
                                <div class="col-md-3 br">
                                    <p class="lead bb"></p>
                                    <form class="form-horizontal p-20">
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('project_no') ?> :</strong></div>
                                            <div class="col-sm-8">
                                                <?php
                                                if (!empty($project_details->project_no)) {
                                                    echo $project_details->project_no;
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('project_name') ?> :</strong></div>
                                            <div class="col-sm-8">
                                                <?php
                                                if (!empty($project_details->project_name)) {
                                                    echo $project_details->project_name;
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('client') ?> :</strong></div>
                                            <div class="col-sm-8">
                                                <strong><?php echo $name; ?></strong>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('start_date') ?> :</strong></div>
                                            <div class="col-sm-8">
                                                <strong><?= strftime(config_item('date_format'), strtotime($project_details->start_date)) ?></strong>
                                            </div>
                                        </div>
                                        <?php
                                        $text = '';
                                        if ($project_details->project_status != 'completed') {
                                            if ($totalDays < 0) {
                                                $overdueDays = $totalDays . ' ' . lang('days_gone');
                                                $text = 'text-danger';
                                            }
                                        }
                                        ?>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('end_date') ?> :</strong></div>
                                            <div class="col-sm-8 <?= $text ?>">
                                                <strong><?= strftime(config_item('date_format'), strtotime($project_details->end_date)) ?>
                                                    <?php if (!empty($overdueDays)) {
                                                        echo lang('overdue') . ' ' . $overdueDays;
                                                    } ?></strong>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('demo_url') ?> :</strong></div>
                                            <div class="col-sm-8">
                                                <strong><?php
                                                    if (!empty($project_details->demo_url)) {
                                                        ?>
                                                        <a href="<?php echo $project_details->demo_url; ?>"
                                                           target="_blank"><?php echo $project_details->demo_url ?></a>
                                                        <?php
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?></strong>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('status') ?>
                                                    :</strong></div>
                                            <div class="col-sm-8">
                                                <?php
                                                $disabled = null;
                                                if (!empty($project_details->project_status)) {
                                                    if ($project_details->project_status == 'completed') {
                                                        $status = "<div class='label label-success'>" . lang($project_details->project_status) . "</div>";
                                                        $disabled = 'disabled';
                                                    } elseif ($project_details->project_status == 'in_progress') {
                                                        $status = "<div class='label label-primary'>" . lang($project_details->project_status) . "</div>";
                                                    } elseif ($project_details->project_status == 'cancel') {
                                                        $status = "<div class='label label-danger'>" . lang($project_details->project_status) . "</div>";
                                                    } else {
                                                        $status = "<div class='label label-warning'>" . lang($project_details->project_status) . "</div>";
                                                    } ?>
                                                    <?= $status; ?>
                                                <?php }
                                                ?>
                                                <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs btn-success dropdown-toggle"
                                                                data-toggle="dropdown">
                                                            <?= lang('change') ?>
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu animated zoomIn">
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/projects/change_status/<?= $project_details->project_id . '/started' ?>"><?= lang('started') ?></a>
                                                            </li>
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/projects/change_status/<?= $project_details->project_id . '/in_progress' ?>"><?= lang('in_progress') ?></a>
                                                            </li>
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/projects/change_status/<?= $project_details->project_id . '/on_hold' ?>"><?= lang('on_hold') ?></a>
                                                            </li>
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/projects/change_status/<?= $project_details->project_id . '/cancel' ?>"><?= lang('cancel') ?></a>
                                                            </li>
                                                            <li>
                                                                <a href="<?= base_url() ?>admin/projects/change_status/<?= $project_details->project_id . '/completed' ?>"><?= lang('completed') ?></a>
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
                                                <?php if (timer_status('projects', $project_details->project_id, 'on')) { ?>

                                                    <span class="label label-success"><?= lang('on') ?></span>
                                                    <a class="btn btn-xs btn-danger "
                                                       href="<?= base_url() ?>admin/projects/tasks_timer/off/<?= $project_details->project_id ?>"><?= lang('stop_timer') ?> </a>
                                                <?php } else {
                                                    ?>
                                                    <span class="label label-danger"><?= lang('off') ?></span>
                                                    <?php $this_permission = $this->items_model->can_action('tbl_project', 'view', array('project_id' => $project_details->project_id), true);
                                                    if (!empty($this_permission)) { ?>
                                                        <a class="btn btn-xs btn-success <?= $disabled ?>"
                                                           href="<?= base_url() ?>admin/projects/tasks_timer/on/<?= $project_details->project_id ?>"><?= lang('start_timer') ?> </a>
                                                    <?php }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('billing_type') ?> :</strong></div>
                                            <div class="col-sm-8">
                                                <strong><?= lang($project_details->billing_type); ?></strong>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <small><?= lang('estimate_hours') ?> :</small>
                                            </div>
                                            <div class="col-sm-8">
                                                <?= ($project_details->estimate_hours); ?> m
                                                <?php if (!empty($project_details) && $project_details->billing_type == 'project_hours' || !empty($project_details) && $project_details->billing_type == 'tasks_and_project_hours' || !empty($project_details) && $project_details->billing_type == 'tasks_sub_tasks_and_project_hours') { ?>
                                                <small class="small text-muted">
                                                    <?= $project_details->hourly_rate . "/" . lang('hour') ?>
                                                    <?php } ?>
                                                </small>
                                            </div>
                                        </div>
                                        <?php if (!empty($staff_finance)) { ?>
                                            <div class="form-group">
                                                <div class="col-sm-4"><strong><?= lang('project_cost') ?> :</strong>
                                                </div>
                                                <div class="col-sm-8">
                                                    <strong><?= display_money($project_cost, $currency->symbol); ?></strong>
                                                    <?php if (!empty($project_details) && $project_details->billing_type == 'project_hours' || !empty($project_details) && $project_details->billing_type == 'tasks_and_project_hours' || !empty($project_details) && $project_details->billing_type == 'tasks_sub_tasks_and_project_hours') { ?>
                                                    <small class="small text-muted">
                                                        <?= $project_details->hourly_rate . "/" . lang('hour') ?>
                                                        <?php } ?>
                                                    </small>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="form-group">
                                            <div class="col-sm-4"><strong><?= lang('participants') ?>
                                                    :</strong></div>
                                            <div class="col-sm-8">
                                                <div class="col-sm-8 ">
                                                    <?php
                                                    if ($project_details->permission != 'all') {
                                                        $get_permission = json_decode($project_details->permission);
                                                        if (!empty($get_permission)) :
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
                                                    } else { ?>
                                                        <strong><?= lang('everyone') ?></strong>
                                                        <i
                                                                title="<?= lang('permission_for_all') ?>"
                                                                class="fa fa-question-circle" data-toggle="tooltip"
                                                                data-placement="top"></i>

                                                        <?php
                                                    }
                                                    ?>
                                                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                        <span data-placement="top" data-toggle="tooltip"
                                                              title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/projects/update_users/<?= $project_details->project_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $show_custom_fields = custom_form_label(4, $project_details->project_id);

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
                                    </form>
                                </div>
                                <?php if (!empty($staff_finance)) { ?>
                                    <div class="col-md-3 br">
                                        <?php
                                        $paid_expense = 0;
                                        foreach ($all_expense_info as $v_expenses) {
                                            if ($v_expenses->invoices_id != 0) {
                                                $paid_expense += $this->invoice_model->calculate_to('paid_amount', $v_expenses->invoices_id);
                                            }
                                        }
                                        ?>
                                        <p class="lead bb"></p>
                                        <form class="form-horizontal p-20">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <strong><?= lang('total') . ' ' . lang('expense') ?></strong>:
                                                </div>
                                                <div class="col-sm-8">
                                                    <strong><?= display_money($total_expense->amount, $currency->symbol) ?></strong>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <strong><?= lang('billable') . ' ' . lang('expense') ?></strong>:
                                                </div>
                                                <div class="col-sm-8">
                                                    <strong><?= display_money($billable_expense->amount, $currency->symbol) ?></strong>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <strong><?= lang('not_billable') . ' ' . lang('expense') ?></strong>:
                                                </div>
                                                <div class="col-sm-8">
                                                    <strong><?= display_money($not_billable_expense->amount, $currency->symbol) ?></strong>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <strong><?= lang('billed') . ' ' . lang('expense') ?></strong>:
                                                </div>
                                                <div class="col-sm-8">
                                                    <strong><?= display_money($paid_expense, $currency->symbol) ?></strong>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <strong><?= lang('unbilled') . ' ' . lang('expense') ?></strong>:
                                                </div>
                                                <div class="col-sm-8">
                                                    <strong><?= display_money($billable_expense->amount - $paid_expense, $currency->symbol) ?></strong>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                <?php } ?>
                                <div class="col-md-3">
                                    <p class="lead bb"></p>
                                    <form class="form-horizontal p-20">
                                        <?php
                                        $estimate_hours = $project_details->estimate_hours;
                                        $percentage = $this->items_model->get_estime_time($estimate_hours);
                                        $logged_hour = $this->items_model->calculate_project('project_hours', $project_details->project_id);
                                        if (!empty($tasks_hours)) {
                                            $logged_tasks_hours = $tasks_hours;
                                        } else {
                                            $logged_tasks_hours = 0;
                                        }
                                        $total_logged_hours = $logged_hour + $logged_tasks_hours;

                                        if ($total_logged_hours < $percentage) {
                                            $total_time = $percentage - $total_logged_hours;
                                            $worked = '<storng style="font-size: 15px;"  class="required">' . lang('left_works') . '</storng>';
                                        } else {
                                            $total_time = $total_logged_hours - $percentage;
                                            $worked = '<storng style="font-size: 15px" class="required">' . lang('extra_works') . '</storng>';
                                        }
                                        $completed = count($this->db->where(array('project_id' => $project_details->project_id, 'task_status' => 'completed'))->get('tbl_task')->result());

                                        $total_task = count($all_task_info);
                                        if (!empty($total_task)) {
                                            if ($total_task != 0) {
                                                $task_progress = $completed / $total_task * 100;
                                            }
                                            if ($task_progress > 100) {
                                                $task_progress = 100;
                                            }
                                            if ($tprogress > 50) {
                                                $p_bar = 'bar-success';
                                            } else {
                                                $p_bar = 'bar-danger';
                                            }
                                            if ($task_progress < 49) {
                                                $t_bar = 'bar-danger';
                                            } elseif ($task_progress < 79) {
                                                $t_bar = 'bar-warning';
                                            } else {
                                                $t_bar = 'bar-success';
                                            }
                                        } else {
                                            $p_bar = 'bar-danger';
                                            $t_bar = 'bar-success';
                                            $task_progress = 0;

                                        }
                                        if (!empty($open_tasks)) {
                                            $col_ = 'col-sm-6';
                                        } else {
                                            $col_ = '';
                                        }
                                        ?>
                                        <div class="<?= $col_ ?>">
                                            <?php if (!empty($col_)) { ?>
                                            <div class="panel panel-custom">
                                                <div class="panel-heading">
                                                    <div class="panel-title"><?= lang('project_hours') ?></div>
                                                </div>
                                                <?php } ?>
                                                <?= $this->items_model->get_time_spent_result($project_hours); ?>

                                                <?php if ($project_details->billing_type == 'tasks_and_project_hours') {
                                                    $total_hours = $project_hours + $tasks_hours;
                                                    ?>
                                                    <h2 style="font-size: 22px"><?= lang('total') ?>
                                                        <span
                                                                style="font-size: 20px">: <?= $this->items_model->get_spent_time($total_hours); ?></span>
                                                    </h2>

                                                <?php } ?>
                                                <?php if (!empty($col_)) { ?>
                                            </div>

                                        <?php } ?>
                                        </div>
                                        <div class="text-center">
                                            <div class="">
                                                <?= $worked ?>
                                            </div>
                                            <div class="">
                                                <?= $this->items_model->get_spent_time($total_time) ?>
                                            </div>
                                        </div>
                                        <div class="<?= $col_ ?>">
                                            <?php if (!empty($col_)) { ?>
                                            <div class="panel panel-custom mb-lg">
                                                <div class="panel-heading">
                                                    <div class="panel-title"><?= lang('task_hours') ?></div>
                                                </div>
                                                <?= $this->items_model->get_time_spent_result($tasks_hours); ?>
                                                <div class="ml-lg">
                                                    <p class="p0 m0">
                                                        <strong><?= lang('billable') ?></strong>: <?= $this->items_model->get_spent_time($tasks_hours) ?>
                                                    </p>
                                                    <p class="p0 m0"><strong><?= lang('not_billable') ?></strong>:
                                                        <?php
                                                        $non_billable_time = 0;
                                                        foreach ($all_task_info as $v_n_tasks) {
                                                            if (!empty($v_n_tasks->billable) && $v_n_tasks->billable == 'No') {
                                                                $non_billable_time += $this->items_model->task_spent_time_by_id($v_n_tasks->task_id);
                                                            }
                                                        }
                                                        echo $this->items_model->get_spent_time($non_billable_time);
                                                        ?>
                                                    </p>
                                                </div>
                                                <?php } ?>
                                                <?php if (!empty($staff_finance)) { ?>
                                                    <h2 class="text-center mt"><?= lang('total_bill') ?>
                                                        : <?= display_money($project_cost, $currency->symbol) ?></h2>
                                                <?php } ?>
                                                <?php if (!empty($col_)) { ?>
                                            </div>
                                        <?php } ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 br ">
                                    <p class="lead bb"></p>
                                    <form class="form-horizontal p-20">
                                        <blockquote style="font-size: 12px;word-wrap: break-word;"><?php
                                            if (!empty($project_details->description)) {
                                                echo $project_details->description;
                                            }
                                            ?></blockquote>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <p class="lead bb"></p>
                                    <form class="form-horizontal p-20">
                                        <div class="col-sm-12">
                                            <strong><?= $TotalGone . ' / ' . $totalDays . ' ' . $lang . ' (' . round($tprogress, 2) . '% )'; ?></strong>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mt progress progress-striped progress-xs">
                                                <div class="progress-bar progress-<?= $p_bar ?> " data-toggle="tooltip"
                                                     data-original-title="<?= round($tprogress, 2) ?>%"
                                                     style="width: <?= round($tprogress, 2) ?>%"></div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <strong><?= $completed . ' / ' . $total_task . ' ' . lang('open') . ' ' . lang('tasks') . ' (' . round($task_progress, 2) . '% )'; ?> </strong>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mt progress progress-striped progress-xs">
                                                <div class="progress-bar progress-<?= $t_bar ?> " data-toggle="tooltip"
                                                     data-original-title="<?= $task_progress ?>%"
                                                     style="width: <?= $task_progress ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <strong><?= lang('completed') ?>:</strong>
                                        </div>
                                        <div class="col-sm-12">
                                            <?php
                                            $progress = $this->items_model->get_project_progress($project_details->project_id);

                                            if ($progress < 49) {
                                                $progress_b = 'progress-bar-danger';
                                            } elseif ($progress > 50 && $progress < 99) {
                                                $progress_b = 'progress-bar-primary';
                                            } else {
                                                $progress_b = 'progress-bar-success';
                                            }
                                            ?>
                                            <span class="">
                                <div class="mt progress progress-striped progress-xs">
                                    <div class="progress-bar <?= $progress_b ?> " data-toggle="tooltip"
                                         data-original-title="<?= $progress ?>%"
                                         style="width: <?= $progress ?>%"></div>
                                </div>
                                </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2"><strong><?= lang('project_no') ?> :</strong>
                                </label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">
                                        <?php
                                        if (!empty($project_details->project_no)) {
                                            echo $project_details->project_no;
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-sm-2"><strong><?= lang('project_name') ?> :</strong>
                                </label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">
                                        <?php
                                        if (!empty($project_details->project_name)) {
                                            echo $project_details->project_name;
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label class="control-label col-sm-4"><strong><?= lang('client') ?>
                                            :</strong></label>
                                    <p class="form-control-static">
                                        <strong><?php echo $name; ?></strong>
                                    </p>
                                </div>
                                <?php
                                $disabled = null;
                                if (!empty($project_details->project_status)) {
                                    if ($project_details->project_status == 'completed') {
                                        $disabled = 'disabled';
                                    }
                                }
                                ?>
                                <div class="col-sm-6">
                                    <label class="control-label col-sm-4"><strong><?= lang('timer_status') ?>
                                            :</strong></label>
                                    <div class="col-sm-8 mt">
                                        <?php if ($project_details->timer_status == 'on') { ?>

                                            <span class="label label-success"><?= lang('on') ?></span>
                                            <a class="btn btn-xs btn-danger "
                                               href="<?= base_url() ?>admin/projects/tasks_timer/off/<?= $project_details->project_id ?>"><?= lang('stop_timer') ?> </a>
                                        <?php } else {
                                            ?>
                                            <span class="label label-danger"><?= lang('off') ?></span>
                                            <?php $this_permission = $this->items_model->can_action('tbl_project', 'view', array('project_id' => $project_details->project_id), true);
                                            if (!empty($this_permission)) { ?>
                                                <a class="btn btn-xs btn-success <?= $disabled ?>"
                                                   href="<?= base_url() ?>admin/projects/tasks_timer/on/<?= $project_details->project_id ?>"><?= lang('start_timer') ?> </a>
                                            <?php }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label class="control-label col-sm-4"><strong><?= lang('start_date') ?> :</strong>
                                    </label>
                                    <p class="form-control-static">
                                        <?= strftime(config_item('date_format'), strtotime($project_details->start_date)) ?>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label col-sm-4"><strong><?= lang('end_date') ?>
                                            :</strong></label>
                                    <?php
                                    $text = '';
                                    if ($project_details->project_status != 'completed') {
                                        if ($totalDays < 0) {
                                            $overdueDays = $totalDays . ' ' . lang('days_gone');
                                            $text = 'text-danger';
                                        }
                                    }
                                    ?>
                                    <p class="form-control-static <?= $text ?>">
                                        <?= strftime(config_item('date_format'), strtotime($project_details->end_date)) ?>
                                        <?php if (!empty($overdueDays)) {
                                            echo lang('overdue') . ' ' . $overdueDays;
                                        } ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label class="control-label col-sm-4"><strong><?= lang('billing_type') ?> :</strong>
                                    </label>
                                    <p class="form-control-static">
                                        <strong><?= lang($project_details->billing_type); ?></strong>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label col-sm-4">
                                        <small><?= lang('estimate_hours') ?> :</small>
                                    </label>
                                    <p class="form-control-static">
                                        <strong><?= ($project_details->estimate_hours); ?> m
                                        </strong>
                                        <?php if (!empty($project_details) && $project_details->billing_type == 'project_hours' || !empty($project_details) && $project_details->billing_type == 'tasks_and_project_hours' || !empty($project_details) && $project_details->billing_type == 'tasks_sub_tasks_and_project_hours') { ?>
                                        <small class="small text-muted">
                                            <?= $project_details->hourly_rate . "/" . lang('hour') ?>
                                            <?php } ?>
                                        </small>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label class="control-label col-sm-4"><strong><?= lang('demo_url') ?> :</strong>
                                    </label>
                                    <p class="form-control-static" style="overflow: hidden;">
                                        <?php
                                        if (!empty($project_details->demo_url)) {
                                            ?>
                                            <a href="<?php echo $project_details->demo_url; ?>"
                                               target="_blank"><?php echo $project_details->demo_url ?></a>
                                            <?php
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </p>
                                </div>
                                <?php if (!empty($staff_finance)) { ?>
                                    <div class="col-sm-6">
                                        <label class="control-label col-sm-4"><strong><?= lang('project_cost') ?>
                                                :</strong>
                                        </label>
                                        <p class="form-control-static">
                                            <strong><?php echo display_money($project_cost, $currency->symbol); ?></strong>
                                            <?php if (!empty($project_details) && $project_details->billing_type == 'project_hours' || !empty($project_details) && $project_details->billing_type == 'tasks_and_project_hours' || !empty($project_details) && $project_details->billing_type == 'tasks_sub_tasks_and_project_hours') { ?>
                                            <small class="small text-muted">
                                                <?= $project_details->hourly_rate . "/" . lang('hour') ?>
                                                <?php } ?>
                                            </small>
                                        </p>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label class="control-label col-sm-4"><strong><?= lang('status') ?>
                                            :</strong></label>
                                    <div class="pull-left">
                                        <?php
                                        $disabled = null;
                                        if (!empty($project_details->project_status)) {
                                            if ($project_details->project_status == 'completed') {
                                                $status = "<span class='label label-success'>" . lang($project_details->project_status) . "</span>";
                                                $disabled = 'disabled';
                                            } elseif ($project_details->project_status == 'in_progress') {
                                                $status = "<span class='label label-primary'>" . lang($project_details->project_status) . "</span>";
                                            } elseif ($project_details->project_status == 'cancel') {
                                                $status = "<span class='label label-danger'>" . lang($project_details->project_status) . "</span>";
                                            } else {
                                                $status = "<span class='label label-warning'>" . lang($project_details->project_status) . "</span>";
                                            } ?>
                                            <p class="form-control-static"><?= $status; ?></p>
                                        <?php }
                                        ?>
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
                                                        <a href="<?= base_url() ?>admin/projects/change_status/<?= $project_details->project_id . '/started' ?>"><?= lang('started') ?></a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url() ?>admin/projects/change_status/<?= $project_details->project_id . '/in_progress' ?>"><?= lang('in_progress') ?></a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url() ?>admin/projects/change_status/<?= $project_details->project_id . '/on_hold' ?>"><?= lang('on_hold') ?></a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url() ?>admin/projects/change_status/<?= $project_details->project_id . '/cancel' ?>"><?= lang('cancel') ?></a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url() ?>admin/projects/change_status/<?= $project_details->project_id . '/completed' ?>"><?= lang('completed') ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label col-sm-4"><strong><?= lang('participants') ?>
                                            :</strong></label>
                                    <div class="col-sm-8 ">
                                        <?php
                                        if ($project_details->permission != 'all') {
                                            $get_permission = json_decode($project_details->permission);
                                            if (!empty($get_permission)) :
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
                                        } else { ?>
                                        <p class="form-control-static"><strong><?= lang('everyone') ?></strong>
                                            <i
                                                    title="<?= lang('permission_for_all') ?>"
                                                    class="fa fa-question-circle" data-toggle="tooltip"
                                                    data-placement="top"></i>

                                            <?php
                                            }
                                            ?>
                                            <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                            <span data-placement="top" data-toggle="tooltip"
                                                  title="<?= lang('add_more') ?>">
                                            <a data-toggle="modal" data-target="#myModal"
                                               href="<?= base_url() ?>admin/projects/update_users/<?= $project_details->project_id ?>"
                                               class="text-default ml"><i class="fa fa-plus"></i></a>
                                                </span>
                                        </p>
                                    <?php
                                    }
                                    ?>
                                    </div>
                                </div>
                            </div>
                            <?php $show_custom_fields = custom_form_label(4, $project_details->project_id);

                            if (!empty($show_custom_fields)) {
                                foreach ($show_custom_fields as $c_label => $v_fields) {
                                    if (!empty($v_fields)) {
                                        if (count($v_fields) == 1) {
                                            $col = 'col-sm-12';
                                            $sub_col = 'col-sm-2';
                                            $style = null;
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
                            <div class="form-group  col-sm-12 mt">
                                <label class="control-label col-sm-2 "><strong class="mr-sm"><?= lang('completed') ?>
                                        :</strong></label>
                                <div class="col-sm-8 " style="margin-left: -5px;">
                                    <?php
                                    $progress = $this->items_model->get_project_progress($project_details->project_id);

                                    if ($progress < 49) {
                                        $progress_b = 'progress-bar-danger';
                                    } elseif ($progress > 50 && $progress < 99) {
                                        $progress_b = 'progress-bar-primary';
                                    } else {
                                        $progress_b = 'progress-bar-success';
                                    }
                                    ?>
                                    <span class="">
                                <div class="mt progress progress-striped ">
                                    <div class="progress-bar <?= $progress_b ?> " data-toggle="tooltip"
                                         data-original-title="<?= $progress ?>%"
                                         style="width: <?= $progress ?>%"></div>
                                </div>
                                </span>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <?php
                                $estimate_hours = $project_details->estimate_hours;
                                $percentage = $this->items_model->get_estime_time($estimate_hours);
                                $logged_hour = $this->items_model->calculate_project('project_hours', $project_details->project_id);
                                if (!empty($tasks_hours)) {
                                    $logged_tasks_hours = $tasks_hours;
                                } else {
                                    $logged_tasks_hours = 0;
                                }
                                $total_logged_hours = $logged_hour + $logged_tasks_hours;

                                if ($total_logged_hours < $percentage) {
                                    $total_time = $percentage - $total_logged_hours;
                                    $worked = '<storng style="font-size: 15px;"  class="required">' . lang('left_works') . '</storng>';
                                } else {
                                    $total_time = $total_logged_hours - $percentage;
                                    $worked = '<storng style="font-size: 15px" class="required">' . lang('extra_works') . '</storng>';
                                }

                                $completed = count($this->db->where(array('project_id' => $project_details->project_id, 'task_status' => 'completed'))->get('tbl_task')->result());

                                $total_task = count($all_task_info);
                                if (!empty($total_task)) {
                                    if ($total_task != 0) {
                                        $task_progress = $completed / $total_task * 100;
                                    }
                                    if ($task_progress > 100) {
                                        $task_progress = 100;
                                    }
                                    if ($tprogress > 50) {
                                        $p_bar = 'bar-success';
                                    } else {
                                        $p_bar = 'bar-danger';
                                    }
                                    if ($task_progress < 49) {
                                        $t_bar = 'bar-danger';
                                    } elseif ($task_progress < 79) {
                                        $t_bar = 'bar-warning';
                                    } else {
                                        $t_bar = 'bar-success';
                                    }
                                } else {
                                    $p_bar = 'bar-danger';
                                    $t_bar = 'bar-success';
                                    $task_progress = 0;

                                }
                                if (!empty($open_tasks)) {
                                    $col_ = 'col-sm-6';
                                } else {
                                    $col_ = '';
                                }
                                ?>
                                <div class="<?= $col_ ?>">
                                    <?php if (!empty($col_)) { ?>
                                    <div class="panel panel-custom">
                                        <div class="panel-heading">
                                            <div class="panel-title"><?= lang('project_hours') ?></div>
                                        </div>
                                        <?php } ?>
                                        <?= $this->items_model->get_time_spent_result($project_hours); ?>
                                        <?php
                                        $paid_expense = 0;
                                        foreach ($all_expense_info as $v_expenses) {
                                            if ($v_expenses->invoices_id != 0) {
                                                $paid_expense += $this->invoice_model->calculate_to('paid_amount', $v_expenses->invoices_id);
                                            }
                                        }
                                        ?>
                                        <?php if (!empty($staff_finance)) { ?>
                                            <div class="ml-lg mb-lg text-center">
                                                <p class="p0 m0">
                                                    <strong><?= lang('total') . ' ' . lang('expense') ?></strong>: <?= display_money($total_expense->amount, $currency->symbol) ?>
                                                </p>
                                                <p class="p0 m0">
                                                    <strong><?= lang('billable') . ' ' . lang('expense') ?></strong>: <?= display_money($billable_expense->amount, $currency->symbol) ?>
                                                </p>
                                                <p class="p0 m0">
                                                    <strong><?= lang('not_billable') . ' ' . lang('expense') ?></strong>: <?= display_money($not_billable_expense->amount, $currency->symbol) ?>
                                                </p>
                                                <p class="p0 m0">
                                                    <strong><?= lang('billed') . ' ' . lang('expense') ?></strong>: <?= display_money($paid_expense, $currency->symbol) ?>
                                                </p>
                                                <p class="p0 m0">
                                                    <strong><?= lang('unbilled') . ' ' . lang('expense') ?></strong>: <?= display_money($billable_expense->amount - $paid_expense, $currency->symbol) ?>
                                                </p>
                                            </div>
                                        <?php } ?>
                                        <?php if ($project_details->billing_type == 'tasks_and_project_hours') {
                                            $total_hours = $project_hours + $tasks_hours;
                                            ?>
                                            <h2 style="font-size: 22px"><?= lang('total') ?>
                                                <span
                                                        style="font-size: 20px">: <?= $this->items_model->get_spent_time($total_hours); ?></span>
                                            </h2>
                                        <?php } ?>
                                        <?php if (!empty($col_)) { ?>
                                    </div>

                                <?php } ?>
                                </div>
                                <div class="<?= $col_ ?>">
                                    <?php if (!empty($col_)) { ?>
                                    <div class="panel panel-custom mb-lg">
                                        <div class="panel-heading">
                                            <div class="panel-title"><?= lang('task_hours') ?></div>
                                        </div>
                                        <?= $this->items_model->get_time_spent_result($tasks_hours); ?>
                                        <div class="ml-lg">
                                            <p class="p0 m0">
                                                <strong><?= lang('billable') ?></strong>: <?= $this->items_model->get_spent_time($tasks_hours) ?>
                                            </p>
                                            <p class="p0 m0"><strong><?= lang('not_billable') ?></strong>:
                                                <?php
                                                $non_billable_time = 0;
                                                foreach ($all_task_info as $v_n_tasks) {
                                                    if (!empty($v_n_tasks->billable) && $v_n_tasks->billable == 'No') {
                                                        $non_billable_time += $this->items_model->task_spent_time_by_id($v_n_tasks->task_id);
                                                    }
                                                }
                                                echo $this->items_model->get_spent_time($non_billable_time);
                                                ?>
                                            </p>
                                        </div>
                                        <?php } ?>
                                        <?php if (!empty($staff_finance)) { ?>
                                            <h2 class="text-center mt"><?= lang('total_bill') ?>
                                                : <?= display_money($project_cost, $currency->symbol) ?></h2>
                                        <?php } ?>
                                        <?php if (!empty($col_)) { ?>
                                    </div>
                                <?php } ?>
                                </div>
                                <div class="col-sm-12 mt-lg">
                                    <div class="col-sm-4">
                                        <strong><?= $TotalGone . ' / ' . $totalDays . ' ' . $lang . ' (' . round($tprogress, 2) . '% )'; ?></strong>
                                        <div class="mt progress progress-striped progress-xs">
                                            <div class="progress-bar progress-<?= $p_bar ?> " data-toggle="tooltip"
                                                 data-original-title="<?= round($tprogress, 2) ?>%"
                                                 style="width: <?= round($tprogress, 2) ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-center">
                                            <div class="">
                                                <?= $worked ?>
                                            </div>
                                            <div class="">
                                                <?= $this->items_model->get_spent_time($total_time) ?>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-sm-4">
                                        <strong><?= $completed . ' / ' . $total_task . ' ' . lang('open') . ' ' . lang('tasks') . ' (' . round($task_progress, 2) . '% )'; ?> </strong>
                                        <div class="mt progress progress-striped progress-xs">
                                            <div class="progress-bar progress-<?= $t_bar ?> " data-toggle="tooltip"
                                                 data-original-title="<?= $task_progress ?>%"
                                                 style="width: <?= $task_progress ?>%"></div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <div class="form-group col-sm-12">
                                <div class="col-sm-12">
                                    <blockquote style="font-size: 12px;word-wrap: break-word;"><?php
                                        if (!empty($project_details->description)) {
                                            echo $project_details->description;
                                        }
                                        ?></blockquote>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 15 ? 'active' : '' ?>" id="project_calendar"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('calendar') ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="">
                            <div class="panel-heading mb0" style="border-bottom: 1px solid #D8D8D8"></div>
                            <div id="calendar"></div>
                        </div>
                        <link href="<?php echo base_url() ?>asset/css/fullcalendar.css" rel="stylesheet"
                              type="text/css">
                        <style type="text/css">
                            .datepicker {
                                z-index: 1151 !important;
                            }

                            .mt-sm {
                                font-size: 14px;
                            }

                            .fc-state-default {
                                background: none !important;
                                color: inherit !important;;
                            }
                        </style>
                        <?php
                        $gcal_api_key = config_item('gcal_api_key');
                        $gcal_id = config_item('gcal_id');
                        ?>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                if ($('#calendar').length) {
                                    var date = new Date();
                                    var d = date.getDate();
                                    var m = date.getMonth();
                                    var y = date.getFullYear();
                                    var calendar = $('#calendar').fullCalendar({
                                        googleCalendarApiKey: '<?=$gcal_api_key?>',
                                        eventAfterRender: function (event, element, view) {
                                            if (event.type == 'fo') {
                                                $(element).attr('data-toggle', 'ajaxModal').addClass('ajaxModal');
                                            }
                                        },
                                        header: {
                                            center: 'prev title next',
                                            left: 'month agendaWeek agendaDay today',
                                            right: ''
                                        },
                                        buttonText: {
                                            prev: '<i class="fa fa-angle-left" />',
                                            next: '<i class="fa fa-angle-right" />'
                                        },
                                        selectable: true,
                                        selectHelper: true,
                                        select: function (start, end, allDay) {
                                            var endtime = $.fullCalendar.formatDate(end, 'h:mm tt');
                                            var starttime = $.fullCalendar.formatDate(start, 'yyyy/MM/dd');
                                            var mywhen = starttime + ' - ' + endtime;
                                            $('#event_modal #apptStartTime').val(starttime);
                                            $('#event_modal #apptEndTime').val(starttime);
                                            $('#event_modal #apptAllDay').val(allDay);
                                            $('#event_modal #when').text(mywhen);
                                            $('#event_modal').modal('show');
                                        },
                                        events: [
                                            <?php
                                            $invoice_info = $this->db->where('project_id', $project_details->project_id)->get('tbl_invoices')->result();
                                            if (!empty($invoice_info)) {
                                            foreach ($invoice_info as $v_invoice) :
                                            $start_day = date('d', strtotime($v_invoice->due_date));
                                            $smonth = date('n', strtotime($v_invoice->due_date));
                                            $start_month = $smonth - 1;
                                            $start_year = date('Y', strtotime($v_invoice->due_date));
                                            $end_year = date('Y', strtotime($v_invoice->due_date));
                                            $end_day = date('d', strtotime($v_invoice->due_date));
                                            $emonth = date('n', strtotime($v_invoice->due_date));
                                            $end_month = $emonth - 1;
                                            ?>
                                            {
                                                title: "<?php echo $v_invoice->reference_no ?>",
                                                start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                color: '<?= config_item('invoice_color') ?>',
                                                url: '<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_invoice->invoices_id ?>'
                                            },
                                            <?php
                                            endforeach;
                                            }
                                            $estimates_info = $this->db->where('project_id', $project_details->project_id)->get('tbl_estimates')->result();;
                                            if (!empty($estimates_info)) {
                                            foreach ($estimates_info as $v_estimates) :
                                            $start_day = date('d', strtotime($v_estimates->due_date));
                                            $smonth = date('n', strtotime($v_estimates->due_date));
                                            $start_month = $smonth - 1;
                                            $start_year = date('Y', strtotime($v_estimates->due_date));
                                            $end_year = date('Y', strtotime($v_estimates->due_date));
                                            $end_day = date('d', strtotime($v_estimates->due_date));
                                            $emonth = date('n', strtotime($v_estimates->due_date));
                                            $end_month = $emonth - 1;
                                            ?>
                                            {
                                                title: "<?php echo $v_estimates->reference_no ?>",
                                                start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                color: '<?= config_item('estimate_color') ?>',
                                                url: '<?= base_url() ?>admin/estimates/index/estimates_details/<?= $v_estimates->estimates_id ?>'
                                            },
                                            <?php
                                            endforeach;
                                            }
                                            $start_day = date('d', strtotime($project_details->end_date));
                                            $smonth = date('n', strtotime($project_details->end_date));
                                            $start_month = $smonth - 1;
                                            $start_year = date('Y', strtotime($project_details->end_date));
                                            $end_year = date('Y', strtotime($project_details->end_date));
                                            $end_day = date('d', strtotime($project_details->end_date));
                                            $emonth = date('n', strtotime($project_details->end_date));
                                            $end_month = $emonth - 1;
                                            ?>
                                            {
                                                title: "<?php echo $project_details->project_name  ?>",
                                                start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                color: '<?= config_item('project_color') ?>',
                                                url: '<?= base_url() ?>admin/projects/project_details/<?= $project_details->project_id ?>'
                                            },
                                            <?php

                                            $milestone_info = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_milestones')->result();
                                            if (!empty($milestone_info)) {
                                            foreach ($milestone_info as $v_milestone) :
                                            $start_day = date('d', strtotime($v_milestone->end_date));
                                            $smonth = date('n', strtotime($v_milestone->end_date));
                                            $start_month = $smonth - 1;
                                            $start_year = date('Y', strtotime($v_milestone->end_date));
                                            $end_year = date('Y', strtotime($v_milestone->end_date));
                                            $end_day = date('d', strtotime($v_milestone->end_date));
                                            $emonth = date('n', strtotime($v_milestone->end_date));
                                            $end_month = $emonth - 1;
                                            ?>
                                            {
                                                title: '<?php echo $v_milestone->milestone_name ?>',
                                                start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                color: '<?= config_item('milestone_color') ?>',
                                                url: '<?= base_url() ?>admin/projects/project_details/<?= $project_details->project_id ?>/5'
                                            },
                                            <?php
                                            endforeach;
                                            }
                                            $task_info = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_task')->result();
                                            if (!empty($task_info)) {
                                            foreach ($task_info as $v_task) :
                                            $start_day = date('d', strtotime($v_task->due_date));
                                            $smonth = date('n', strtotime($v_task->due_date));
                                            $start_month = $smonth - 1;
                                            $start_year = date('Y', strtotime($v_task->due_date));
                                            $end_year = date('Y', strtotime($v_task->due_date));
                                            $end_day = date('d', strtotime($v_task->due_date));
                                            $emonth = date('n', strtotime($v_task->due_date));
                                            $end_month = $emonth - 1;
                                            ?>
                                            {
                                                title: "<?php echo $v_task->task_name ?>",
                                                start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                color: '<?= config_item('tasks_color') ?>',
                                                url: '<?= base_url() ?>admin/tasks/view_task_details/<?= $v_task->task_id ?>'
                                            },
                                            <?php
                                            endforeach;
                                            }
                                            $bug_info = $this->db->where(array('project_id' => $project_details->project_id))->get('tbl_bug')->result();
                                            if (!empty($bug_info)) {
                                            foreach ($bug_info as $v_bug) :
                                            $start_day = date('d', strtotime($v_bug->created_time));
                                            $smonth = date('n', strtotime($v_bug->created_time));
                                            $start_month = $smonth - 1;
                                            $start_year = date('Y', strtotime($v_bug->created_time));
                                            $end_year = date('Y', strtotime($v_bug->created_time));
                                            $end_day = date('d', strtotime($v_bug->created_time));
                                            $emonth = date('n', strtotime($v_bug->created_time));
                                            $end_month = $emonth - 1;
                                            ?>
                                            {
                                                title: "<?php echo $v_bug->bug_title ?>",
                                                start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                                                end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                                                color: '<?= config_item('bugs_color') ?>',
                                                url: '<?= base_url() ?>admin/bugs/view_bug_details/<?= $v_bug->bug_id ?>'
                                            },
                                            <?php
                                            endforeach;
                                            }
                                            ?>
                                        ],
                                        eventColor: '#3A87AD',
                                    });
                                }

                            });</script>
                        <?php include_once 'assets/plugins/fullcalendar/fullcalendar.php'; ?>
                        <script src="<?php echo base_url(); ?>asset/js/jquery-ui.min.js"></script>
                    </div>
                </div>
            </div>
            <?php
            $type = $this->uri->segment(6);
            $category_id = $this->uri->segment(7);
            $expense_category = $this->db->get('tbl_expense_category')->result();
            ?>
            <!-- Task Details tab Ends -->
            <div class="tab-pane <?= $active == 10 ? 'active' : '' ?>" id="expense" style="position: relative;">
                <div class="box" style="border: none; " data-collapsed="0">
                    <div class="btn-group pull-right btn-with-tooltip-group" data-toggle="tooltip"
                         data-title="<?php echo lang('filter_by'); ?>">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left"
                            style="width:300px;<?php if (!empty($type) && $type == 'category') {
                                echo 'display:block';
                            } ?>">
                            <li class="<?php
                            if (empty($type)) {
                                echo 'active';
                            } ?>"><a
                                        href="<?= base_url() ?>admin/projects/project_details/<?= $project_details->project_id ?>/10"><?php echo lang('all'); ?></a>
                            </li>
                            <li class="divider"></li>
                            <?php if (count($expense_category) > 0) { ?>
                                <?php foreach ($expense_category as $v_category) {
                                    ?>
                                    <li class="<?php if (!empty($category_id)) {
                                        if ($type == 'category') {
                                            if ($category_id == $v_category->expense_category_id) {
                                                echo 'active';
                                            }
                                        }
                                    } ?>">
                                        <a href="<?= base_url() ?>admin/projects/project_details/<?= $project_details->project_id ?>/10/category/<?php echo $v_category->expense_category_id; ?>"><?php echo $v_category->expense_category; ?></a>
                                    </li>
                                <?php }
                                ?>
                                <div class="clearfix"></div>
                                <li class="divider"></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#manage_expense" data-toggle="tab"><?= lang('expense') ?></a>
                            </li>
                            <li class=""><a
                                        href="<?= base_url() ?>admin/transactions/expense/project_expense/<?= $project_details->project_id ?>"><?= lang('new_expense') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content bg-white">
                            <!-- ************** general *************-->
                            <div class="tab-pane active" id="manage_expense">
                                <div class="table-responsive">
                                    <table id="manage_expense" class="table table-striped ">
                                        <thead>
                                        <tr>
                                            <th class="col-date"><?= lang('name') . '/' . lang('title') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th><?= lang('categories') ?></th>
                                            <th class="col-currency"><?= lang('amount') ?></th>
                                            <th><?= lang('attachment') ?></th>
                                            <th class="col-options no-sort"><?= lang('action') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if (!empty($type) && $type == 'category') {
                                            $cate_expense_info = array();
                                            $expense_id = $this->uri->segment(7);
                                            if (!empty($all_expense_info)) {
                                                foreach ($all_expense_info as $v_expense) {
                                                    if ($v_expense->type == 'Expense' && $v_expense->category_id == $expense_id) {
                                                        array_push($cate_expense_info, $v_expense);
                                                    }
                                                }
                                            }
                                            $all_expense_info = $cate_expense_info;
                                        }
                                        $all_expense_info = array_reverse($all_expense_info);
                                        if (!empty($all_expense_info)):
                                            foreach ($all_expense_info as $v_expense) :
                                                if ($v_expense->type == 'Expense'):
                                                    $category_info = $this->db->where('expense_category_id', $v_expense->category_id)->get('tbl_expense_category')->row();
                                                    if (!empty($category_info)) {
                                                        $category = $category_info->expense_category;
                                                    } else {
                                                        $category = lang('undefined_category');
                                                    }

                                                    $can_edit = $this->items_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $v_expense->transactions_id));
                                                    $can_delete = $this->items_model->can_action('tbl_transactions', 'delete', array('transactions_id' => $v_expense->transactions_id));
                                                    $e_edited = can_action('31', 'edited');
                                                    $e_deleted = can_action('31', 'deleted');

                                                    $account_info = $this->items_model->check_by(array('account_id' => $v_expense->account_id), 'tbl_accounts');
                                                    ?>
                                                    <tr id="table-expense-<?= $v_expense->transactions_id ?>">
                                                        <td>
                                                            <a href="<?= base_url() ?>admin/transactions/view_expense/<?= $v_expense->transactions_id ?>">
                                                                <?= (!empty($v_expense->name) ? $v_expense->name : '-') ?>
                                                            </a>
                                                        </td>
                                                        <td><?= strftime(config_item('date_format'), strtotime($v_expense->date)); ?></td>
                                                        <td><?= $category ?></td>
                                                        <td><?= display_money($v_expense->amount, $currency->symbol) ?></td>

                                                        <td>
                                                            <?php
                                                            $attachement_info = json_decode($v_expense->attachement);
                                                            if (!empty($attachement_info)) { ?>
                                                                <a href="<?= base_url() ?>admin/transactions/download/<?= $v_expense->transactions_id ?>"><?= lang('download') ?></a>
                                                            <?php } ?>
                                                        </td>

                                                        <td class="">
                                                            <a class="btn btn-info btn-xs"
                                                               href="<?= base_url() ?>admin/transactions/view_expense/<?= $v_expense->transactions_id ?>">
                                                                <span class="fa fa-list-alt"></span>
                                                            </a>
                                                            <?php if (!empty($can_edit) && !empty($e_edited)) { ?>
                                                                <?= btn_edit('admin/transactions/expense/' . $v_expense->transactions_id) ?>
                                                            <?php }
                                                            if (!empty($can_delete) && !empty($e_deleted)) {
                                                                ?>
                                                                <?php echo ajax_anchor(base_url("admin/transactions/delete_expense/" . $v_expense->transactions_id), "<i class='btn btn-danger btn-xs fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table-expense-" . $v_expense->transactions_id)); ?>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php
                                                endif;
                                            endforeach;
                                        endif;
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- End Tasks Management-->

                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="activities" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('activities') ?>
                            <?php
                            $role = $this->session->userdata('user_type');
                            if ($role == 1) {
                                ?>
                                <span class="btn-xs pull-right mt">
                            <a href="<?= base_url() ?>admin/tasks/claer_activities/projects/<?= $project_details->project_id ?>"><?= lang('clear') . ' ' . lang('activities') ?></a>
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

            <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="task_comments" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('comments') ?></h3>
                    </div>
                    <div class="panel-body chat">
                        <?php echo form_open(base_url("admin/projects/save_comments"), array("id" => $comment_type . "-comment-form", "class" => "form-horizontal general-form", "enctype" => "multipart/form-data", "role" => "form")); ?>
                        <input type="hidden" name="project_id" value="<?php
                        if (!empty($project_details->project_id)) {
                            echo $project_details->project_id;
                        }
                        ?>" class="form-control">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php
                                echo form_textarea(array(
                                    "id" => "comment_description",
                                    "name" => "description",
                                    "class" => "form-control comment_description",
                                    "placeholder" => $project_details->project_name  . ' ' . lang('comments'),
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
                        $comment_reply_type = 'projects-reply';
                        ?>
                        <?php $this->load->view('admin/projects/comments_list', array('comment_details' => $comment_details)) ?>
                    </div>
                </div>
            </div>
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


            <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="task_attachments" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading mb0">
                        <?php
                        $attach_list = $this->session->userdata('projects_media_view');
                        if (empty($attach_list)) {
                            $attach_list = 'list_view';
                        }
                        ?>
                        <h3 class="panel-title">
                            <?= lang('attach_file_list') ?>

                            <a data-toggle="tooltip" data-placement="top"
                               href="<?= base_url('admin/global_controller/download_all_attachment/project_id/' . $project_details->project_id) ?>"
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
                                <a href="<?= base_url() ?>admin/projects/new_attachment/<?= $project_details->project_id ?>"
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
                                var module = 'projects';
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
                            <?php $this->load->view('admin/projects/attachment_list', array('project_files_info' => $project_files_info)) ?>
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
                                                            <?php echo ajax_anchor(base_url("admin/projects/delete_files/" . $files_info[$key]->task_attachment_id), "<i class='text-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#media_list_container-" . $files_info[$key]->task_attachment_id)); ?>
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
                                                                                        href="<?= base_url() ?>admin/projects/attachment_details/r/<?= $files_info[$key]->task_attachment_id . '/' . $v_files->uploaded_files_id ?>">
                                                                                    <img
                                                                                            style="width: 50px;border-radius: 5px;"
                                                                                            src="<?= base_url() . $v_files->files ?>"/></a>
                                                                            </div>
                                                                        <?php else : ?>
                                                                            <div class="file-icon"><i
                                                                                        class="fa fa-file-o"></i>
                                                                                <a data-toggle="modal"
                                                                                   data-target="#myModal_extra_lg"
                                                                                   href="<?= base_url() ?>admin/projects/attachment_details/r/<?= $files_info[$key]->task_attachment_id . '/' . $v_files->uploaded_files_id ?>"><?= $v_files->file_name ?></a>
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
                                                                           href="<?= base_url() ?>admin/projects/download_files/<?= $v_files->uploaded_files_id ?>"><i
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

            <div class="tab-pane <?= $active == 5 ? 'active' : '' ?>" id="milestones" style="position: relative;">
                <div class="box" style="border: none; " data-collapsed="0">
                    <?php
                    $kanban = $this->session->userdata('milestone_kanban');
                    $uri_segment = $this->uri->segment(6);

                    if (!empty($kanban)) {
                        $k_milestone = 'kanban';
                    } elseif ($uri_segment == 'kanban') {
                        $k_milestone = 'kanban';
                    } else {
                        $k_milestone = 'list';
                    }
                    if ($k_milestone == 'kanban') {
                        $text = 'list';
                        $btn = 'purple';
                    } else {
                        $text = 'kanban';
                        $btn = 'danger';
                    }
                    ?>
                    <div class="mb-lg ">
                        <div class="pr-lg">
                            <a href="<?= base_url() ?>admin/projects/project_details/<?= $project_details->project_id ?>/5/<?= $text ?>"
                               class="btn btn-xs btn-<?= $btn ?> "
                               data-toggle="tooltip"
                               data-placement="top" title="<?= lang('switch_to_' . $text) ?>">
                                <i class="fa fa-undo"> </i><?= ' ' . lang('switch_to_' . $text) ?>
                            </a>
                        </div>
                    </div>
                    <?php if ($k_milestone == 'kanban') { ?>
                        <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/kanban/kan-app.css"/>
                        <div class="app-wrapper">
                            <p class="total-card-counter" id="totalCards"></p>
                            <div class="board" id="board"></div>
                        </div>
                        <?php include_once 'assets/plugins/kanban/milestone_kan-app.php'; ?>
                    <?php } else { ?>
                        <div class="nav-tabs-custom">
                            <!-- Tabs within a box -->
                            <ul class="nav nav-tabs">
                                <li class="<?= $miles_active == 1 ? 'active' : ''; ?>"><a href="#manage_milestone"
                                                                                          data-toggle="tab"><?= lang('milestones') ?></a>
                                </li>
                                <li class="<?= $miles_active == 2 ? 'active' : ''; ?>"><a href="#create_milestone"
                                                                                          data-toggle="tab"><?= lang('add_milestone') ?></a>
                                </li>
                            </ul>
                            <div class="tab-content bg-white">
                                <!-- ************** general *************-->
                                <div class="tab-pane <?= $miles_active == 1 ? 'active' : ''; ?>" id="manage_milestone">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th><?= lang('milestone_name') ?></th>
                                                <th class="col-date"><?= lang('start_date') ?></th>
                                                <th class="col-date"><?= lang('due_date') ?></th>
                                                <th><?= lang('progress') ?></th>
                                                <th><?= lang('action') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if (!empty($all_milestones_info)) {
                                                foreach ($all_milestones_info as $key => $v_milestones) {
                                                    $progress = $this->items_model->calculate_milestone_progress($v_milestones->milestones_id);
                                                    $all_milestones_task = $this->db->where('milestones_id', $v_milestones->milestones_id)->get('tbl_task')->result();
                                                    ?>
                                                    <tr id="table-milestones-<?= $v_milestones->milestones_id ?>">
                                                        <td><a class="text-info" href="#"
                                                               data-original-title="<?= $v_milestones->description ?>"
                                                               data-toggle="tooltip" data-placement="top"
                                                               title=""><?= $v_milestones->milestone_name ?></a></td>
                                                        <td><?= strftime(config_item('date_format'), strtotime($v_milestones->start_date)) ?></td>
                                                        <td><?php
                                                            $due_date = $v_milestones->end_date;
                                                            $due_time = strtotime($due_date);
                                                            $current_time = strtotime(date('Y-m-d'));
                                                            ?>
                                                            <?= strftime(config_item('date_format'), strtotime($due_date)) ?>
                                                            <?php if ($current_time > $due_time && $progress < 100) { ?>
                                                                <span
                                                                        class="label label-danger"><?= lang('overdue') ?></span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <div class="inline ">
                                                                <div class="easypiechart text-success"
                                                                     style="margin: 0px;"
                                                                     data-percent="<?= $progress ?>" data-line-width="5"
                                                                     data-track-Color="#f0f0f0" data-bar-color="#<?php
                                                                if ($progress >= 100) {
                                                                    echo '8ec165';
                                                                } else {
                                                                    echo 'fb6b5b';
                                                                }
                                                                ?>" data-rotate="270" data-scale-Color="false"
                                                                     data-size="50" data-animate="2000">
                                                                    <span class="small text-muted"><?= $progress ?>
                                                                        %</span>
                                                                </div>
                                                            </div>

                                                        </td>
                                                        <td>
                                                            <?php echo btn_edit('admin/projects/project_details/' . $v_milestones->project_id . '/milestone/' . $v_milestones->milestones_id) ?>
                                                            <?php echo ajax_anchor(base_url("admin/projects/delete_milestones/" . $v_milestones->project_id . '/' . $v_milestones->milestones_id), "<i class='btn btn-danger btn-xs fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table-milestones-" . $v_milestones->milestones_id)); ?>
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
                                <div class="tab-pane <?= $miles_active == 2 ? 'active' : ''; ?>" id="create_milestone">
                                    <form role="form" enctype="multipart/form-data" id="form"
                                          action="<?php echo base_url(); ?>admin/projects/save_milestones/<?php
                                          if (!empty($milestones_info)) {
                                              echo $milestones_info->milestones_id;
                                          }
                                          ?>" method="post" class="form-horizontal  ">

                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('milestone_name') ?> <span
                                                        class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="hidden" class="form-control"
                                                       value="<?= $project_details->project_id ?>"
                                                       name="project_id">
                                                <input type="text" class="form-control" value="<?php
                                                if (!empty($milestones_info)) {
                                                    echo $milestones_info->milestone_name;
                                                }
                                                ?>" placeholder="<?= lang('milestone_name') ?>" name="milestone_name"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('description') ?> <span
                                                        class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <textarea name="description" class="form-control"
                                                          placeholder="<?= lang('description') ?>" required><?php
                                                    if (!empty($milestones_info->description)) {
                                                        echo $milestones_info->description;
                                                    }
                                                    ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('start_date') ?> <span
                                                        class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <input type="text" name="start_date" class="form-control start_date"
                                                           value="<?php
                                                           if (!empty($milestones_info->start_date)) {
                                                               echo $milestones_info->start_date;
                                                           }
                                                           ?>"
                                                           data-date-format="<?= config_item('date_picker_format'); ?>"
                                                           required>
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('end_date') ?> <span
                                                        class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <input type="text" name="end_date" class="form-control end_date"
                                                           value="<?php
                                                           if (!empty($milestones_info->end_date)) {
                                                               echo $milestones_info->end_date;
                                                           }
                                                           ?>"
                                                           data-date-format="<?= config_item('date_picker_format'); ?>"
                                                           required="">
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="project_id" value="<?php
                                        if (!empty($project_details->project_id)) {
                                            echo $project_details->project_id;
                                        }
                                        ?>" class="form-control">
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('responsible') ?> <span
                                                        class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <select name="user_id" style="width: 100%" class="select_box"
                                                        required="">
                                                    <optgroup label="<?= lang('admin_staff') ?>">
                                                        <?php
                                                        $user_info = $this->items_model->allowed_user('57');
                                                        if (!empty($user_info)) {
                                                            foreach ($user_info as $key => $v_user) {
                                                                ?>
                                                                <option value="<?= $v_user->user_id ?>" <?php
                                                                if (!empty($milestones_info->user_id)) {
                                                                    echo $v_user->user_id == $milestones_info->user_id ? 'selected' : '';
                                                                }
                                                                ?>><?= ucfirst($v_user->username) ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('visible_to_client') ?>
                                                <span class="required">*</span></label>
                                            <div class="col-sm-6">
                                                <input data-toggle="toggle" name="client_visible" value="Yes" <?php
                                                if (!empty($milestones_info) && $milestones_info->client_visible == 'Yes') {
                                                    echo 'checked';
                                                }
                                                ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                       data-onstyle="success" data-offstyle="danger" type="checkbox">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-lg-3"></div>
                                            <div class="col-lg-6">
                                                <button type="submit"
                                                        class="btn btn-sm btn-primary"><?= lang('add_milestone') ?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- End milestones-->

            <!-- Start Tasks Management-->
            <div class="tab-pane <?= $active == 6 ? 'active' : '' ?>" id="task" style="position: relative;">
                <div class="box" style="border: none; " data-collapsed="0">
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="<?= $task_active == 1 ? 'active' : ''; ?>"><a href="#manage_task"
                                                                                     data-toggle="tab"><?= lang('task') ?></a>
                            </li>
                            <li class=""><a
                                        href="<?= base_url() ?>admin/tasks/all_task/project/<?= $project_details->project_id ?>"><?= lang('new_task') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content bg-white">
                            <!-- ************** general *************-->
                            <div class="tab-pane <?= $task_active == 1 ? 'active' : ''; ?>" id="manage_task">
                                <div class="table-responsive">
                                    <table id="table-tasks" class="table table-striped">
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
                                        if (!empty($all_task_info)):foreach ($all_task_info as $key => $v_task):
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="complete checkbox c-checkbox">
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
                            <!-- End Tasks Management-->

                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 9 ? 'active' : '' ?>" id="bugs" style="position: relative;">
                <div class="box" style="border: none; " data-collapsed="0">
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="<?= $bugs_active == 1 ? 'active' : ''; ?>"><a href="#manage_bugs"
                                                                                     data-toggle="tab"><?= lang('all_bugs') ?></a>
                            </li>
                            <li class=""><a
                                        href="<?= base_url() ?>admin/bugs/index/project/<?= $project_details->project_id ?>"><?= lang('new_bugs') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content bg-white">
                            <!-- ************** general *************-->
                            <div class="tab-pane <?= $task_active == 1 ? 'active' : ''; ?>" id="manage_task">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th><?= lang('bug_title') ?></th>
                                            <th><?= lang('status') ?></th>
                                            <th><?= lang('priority') ?></th>
                                            <th><?= lang('reporter') ?></th>
                                            <th><?= lang('action') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $b_edited = can_action('58', 'edited');
                                        $b_deleted = can_action('58', 'deleted');
                                        if (!empty($all_bugs_info)):foreach ($all_bugs_info as $key => $v_bugs):
                                            $reporter = $this->db->where('user_id', $v_bugs->reporter)->get('tbl_users')->row();
                                            if (!empty($reporter->role_id) && $reporter->role_id == '1') {
                                                $badge = 'danger';
                                            } elseif (!empty($reporter->role_id) && $reporter->role_id == '2') {
                                                $badge = 'info';
                                            } else {
                                                $badge = 'primary';
                                            }
                                            ?>
                                            <tr id="table-bugs-<?= $v_bugs->bug_id ?>">
                                                <td><a class="text-info" style="<?php
                                                    if ($v_bugs->bug_status == 'resolve') {
                                                        echo 'text-decoration: line-through;';
                                                    }
                                                    ?>"
                                                       href="<?= base_url() ?>admin/bugs/view_bug_details/<?= $v_bugs->bug_id ?>"><?php echo $v_bugs->bug_title; ?></a>
                                                </td>
                                                </td>
                                                <td><?php
                                                    if ($v_bugs->bug_status == 'unconfirmed') {
                                                        $label = 'warning';
                                                    } elseif ($v_bugs->bug_status == 'confirmed') {
                                                        $label = 'info';
                                                    } elseif ($v_bugs->bug_status == 'in_progress') {
                                                        $label = 'primary';
                                                    } else {
                                                        $label = 'success';
                                                    }
                                                    ?>
                                                    <span
                                                            class="label label-<?= $label ?>"><?= lang("$v_bugs->bug_status") ?></span>
                                                </td>
                                                <td><?= ucfirst($v_bugs->priority) ?></td>
                                                <td>
                                                    <span class="badge btn-<?= $badge ?> "><?= fullname($v_bugs->reporter) ?></span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($b_edited)) { ?>
                                                        <?php echo btn_edit('admin/bugs/index/' . $v_bugs->bug_id) ?>
                                                    <?php }
                                                    if (!empty($b_deleted)) { ?>
                                                        <?php echo ajax_anchor(base_url("admin/bugs/delete_bug/" . $v_bugs->bug_id), "<i class='btn btn-danger btn-xs fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table-bugs-" . $v_bugs->bug_id)); ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- End Tasks Management-->

                        </div>
                    </div>
                </div>
            </div>


            <?php
            $direction = $this->session->userdata('direction');
            if (!empty($direction) && $direction == 'rtl') {
                $RTL = 'on';
            } else {
                $RTL = config_item('RTL');
            }
            if (!empty($RTL)) {
                ?>
            <link href="<?php echo base_url() ?>assets/plugins/ganttView/jquery.ganttViewRTL.css?ver=3.0.0"
                  rel="stylesheet">
                <script src="<?php echo base_url() ?>assets/plugins/ganttView/jquery.ganttViewRTL.js"></script>
            <?php }else{
            ?>
            <link href="<?php echo base_url() ?>assets/plugins/ganttView/jquery.ganttView.css?ver=3.0.0"
                  rel="stylesheet">
                <script src="<?php echo base_url() ?>assets/plugins/ganttView/jquery.ganttView.js"></script>
            <?php } ?>
            <div class="tab-pane <?= $active == 13 ? 'active' : '' ?>" id="gantt" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('gantt') ?>
                            <span class="pull-right">
            <div class="btn-group pull-right-responsive margin-right-3">
                <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                    <?= lang('show_gantt_by'); ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu animated zoomIn pull-right" role="menu">
                    <li><a href="#"
                           class="resize-gantt"><?= lang('milestones'); ?></a></li>
                    <li><a href="#" class="user-gantt"><?= lang('project_members'); ?></a></li>
                    <li><a href="#" class="status-gantt"><?= lang('status'); ?></a></li>
                </ul>
            </div>
      </span>
                        </h3>
                    </div>
                    <div class="">
                        <?php
                        //get gantt data for Milestones
                        $gantt_data = '{name: "' . $project_details->project_name  . '", desc: "", values: [{
                                label: "", from: "' . $project_details->start_date . '", to: "' . $project_details->end_date . '", customClass: "gantt-headerline"
                                }]},  ';
                        foreach ($all_task_info as $g_task) {
                            if (!empty($g_task)) {
                                if ($g_task->milestones_id == 0) {
                                    $tasks_result['uncategorized'][] = $g_task->task_id;
                                } else {
                                    $milestones_info = $this->db->where('milestones_id', $g_task->milestones_id)->get('tbl_milestones')->row();
                                    $tasks_result[$milestones_info->milestone_name][] = $g_task->task_id;
                                }
                            }
                        }
                        if (!empty($tasks_result)) {
                            foreach ($tasks_result as $cate => $tasks_info):
                                $counter = 0;
                                if (!empty($tasks_info)) {
                                    foreach ($tasks_info as $tasks_id):
                                        $task = $this->db->where('task_id', $tasks_id)->get('tbl_task')->row();
                                        if ($cate != 'uncategorized') {
                                            $milestone = $this->db->where('milestones_id', $task->milestones_id)->get('tbl_milestones')->row();
                                            if (!empty($milestone)) {
                                                $m_start_date = $milestone->start_date;
                                                $m_end_date = $milestone->end_date;
                                            }
                                            $classs = 'gantt-timeline';
                                        } else {
                                            $cate = lang($cate);
                                            $m_start_date = null;
                                            $m_end_date = null;
                                            $classs = '';
                                        }
                                        $milestone_Name = "";
                                        if ($counter == 0) {
                                            $milestone_Name = $cate;
                                            $gantt_data .= '
                                {
                                  name: "' . $milestone_Name . '", desc: "", values: [';

                                            $gantt_data .= '{
                                label: "", from: "' . $m_start_date . '", to: "' . $m_end_date . '", customClass: "' . $classs . '"
                                }';
                                            $gantt_data .= ']
                                },  ';
                                        }

                                        $counter++;
                                        $start = ($task->task_start_date) ? $task->task_start_date : $m_end_date;
                                        $end = ($task->due_date) ? $task->due_date : $m_end_date;
                                        if ($task->task_status == "completed") {
                                            $class = "ganttGrey";
                                        } elseif ($task->task_status == "in_progress") {
                                            $class = "ganttin_progress";
                                        } elseif ($task->task_status == "not_started") {
                                            $class = "gantt_not_started";
                                        } elseif ($task->task_status == "deferred") {
                                            $class = "gantt_deferred";
                                        } else {
                                            $class = "ganttin_progress";
                                        }
                                        $gantt_data .= '
                          {
                            name: "", desc: "' . $task->task_name . '", values: [';

                                        $gantt_data .= '{
                          label: "' . $task->task_name . '", from: "' . $start . '", to: "' . $end . '", customClass: "' . $class . '"
                          }';
                                        $gantt_data .= ']
                          },  ';
                                    endforeach;
                                }
                            endforeach;
                        }

                        //get gantt data for status
                        $tasks_status = array('not_started', 'in_progress', 'completed', 'deferred', 'waiting_for_someone');
                        $all_task = $this->db->where('project_id', $project_details->project_id)->get('tbl_task')->result();

                        foreach ($tasks_status as $key => $t_status) {
                            foreach ($all_task as $v_task) {
                                if ($v_task->task_status == $t_status) {
                                    $task_with_status[$t_status][] = $v_task;
                                }

                                # code...
                            }
                        }

                        $gantt_data2 = '{name: "' . $project_details->project_name . '", desc: "", values: [{
                                label: "", from: "' . $project_details->start_date . '", to: "' . $project_details->end_date . '", customClass: "gantt-headerline"
                                }]},  ';
                        if (!empty($task_with_status)) {
                            foreach ($task_with_status as $status => $task_info):
                                $counter = 0;
                                foreach ($task_info as $t_info):

                                    if ($counter == 0) {
                                        $user_name = $status;
                                        $gantt_data2 .= '
                                {
                                  name: "' . lang($status) . '", desc: "", values: [';

                                        $gantt_data2 .= '{
                                label: "", from: "' . $project_details->start_date . '", to: "' . $project_details->end_date . '", customClass: "gantt-timeline"
                                }';
                                        $gantt_data2 .= ']
                                },  ';
                                    }
                                    $counter++;
                                    $start = ($t_info->task_start_date) ? $t_info->task_start_date : " ";
                                    $end = ($t_info->due_date) ? $t_info->due_date : " ";
                                    if ($t_info->task_status == "completed") {
                                        $class = "ganttGrey";
                                    } elseif ($t_info->task_status == "in_progress") {
                                        $class = "ganttin_progress";
                                    } elseif ($t_info->task_status == "not_started") {
                                        $class = "gantt_not_started";
                                    } elseif ($t_info->task_status == "deferred") {
                                        $class = "gantt_deferred";
                                    } else {
                                        $class = "ganttin_progress";
                                    }
                                    $gantt_data2 .= '
                          {
                            name: "", desc: "' . $t_info->task_name . '", values: [';

                                    $gantt_data2 .= '{
                          label: "' . $t_info->task_name . '", from: "' . $start . '", to: "' . $end . '", customClass: "' . $class . '"
                          }';
                                    $gantt_data2 .= ']
                          },  ';
                                endforeach;
                            endforeach;
                        }
                        // all task wise user id
                        $all_task = $this->db->where('project_id', $project_details->project_id)->get('tbl_task')->result();
                        if (!empty($all_task)) {
                            foreach ($all_task as $v_task) {
                                if ($v_task->permission == 'all') {
                                    $t_permission_user = $this->items_model->all_permission_user('54');
                                    // get all admin user
                                    $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
                                    // if not exist data show empty array.
                                    if (!empty($permission_user)) {
                                        $permission_user = $permission_user;
                                    } else {
                                        $permission_user = array();
                                    }
                                    if (!empty($admin_user)) {
                                        $admin_user = $admin_user;
                                    } else {
                                        $admin_user = array();
                                    }
                                    $t_assign_user = array_merge($admin_user, $t_permission_user);
                                    foreach ($t_assign_user as $t_users) {
                                        $user_id[] = $t_users->user_id;
                                    }
                                    $task_user[$v_task->task_id] = array_unique($user_id);

                                } else {
                                    $task_permission = json_decode($v_task->permission);
                                    foreach ($task_permission as $t_user_id => $v_permission) {
                                        $task_user[$v_task->task_id][] = $t_user_id;

                                    }
                                }
                            }
                            foreach ($task_user as $task_id => $users_id) {
                                foreach ($users_id as $key => $u_id) {
                                    $all_task_by_user[$u_id][] = $task_id;
                                }
                            }
                            // print_r($value);
                            $permission = $project_details->permission;
                            if ($permission == 'all') {
                                $permission_user = $this->items_model->all_permission_user('57');
                                // get all admin user
                                $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
                                // if not exist data show empty array.
                                if (!empty($permission_user)) {
                                    $permission_user = $permission_user;
                                } else {
                                    $permission_user = array();
                                }
                                if (!empty($admin_user)) {
                                    $admin_user = $admin_user;
                                } else {
                                    $admin_user = array();
                                }
                                $assign_user = array_merge($admin_user, $permission_user);
                                foreach ($assign_user as $users) {
                                    $p_user[] = $users->user_id;
                                }
                                $project_user = array_unique($p_user);
                            } else {
                                $get_permission = json_decode($project_details->permission);
                                foreach ($get_permission as $p_user_id => $v_permission) {
                                    $project_user[] = $p_user_id;
                                }
                            }

                            $gantt_data3 = '{name: "' . $project_details->project_name . '", desc: "", values: [{
                                label: "", from: "' . $project_details->start_date . '", to: "' . $project_details->end_date . '", customClass: "gantt-headerline"
                                }]},  ';
                            foreach ($project_user as $key => $user):
                                $counter = 0;
                                foreach ($all_task_by_user as $t_userid => $task_by_user):
                                    if ($user == $t_userid) {
                                        $user_info = $this->db->where('user_id', $user)->get('tbl_account_details')->row();
                                        if ($counter == 0) {
                                            $user_name = $status;
                                            $gantt_data3 .= '
                                {
                                  name: "' . $user_info->fullname . '", desc: "", values: [';

                                            $gantt_data3 .= '{
                                label: "", from: "' . $project_details->start_date . '", to: "' . $project_details->end_date . '", customClass: "gantt-timeline"
                                }';
                                            $gantt_data3 .= ']
                                },  ';
                                        }
                                        $counter++;
                                        foreach ($task_by_user as $task_id) {
                                            $t_info = $this->db->where('task_id', $task_id)->get('tbl_task')->row();
                                            $start = ($t_info->task_start_date) ? $t_info->task_start_date : " ";
                                            $end = ($t_info->due_date) ? $t_info->due_date : " ";
                                            if ($t_info->task_status == "completed") {
                                                $class = "ganttGrey";
                                            } elseif ($t_info->task_status == "in_progress") {
                                                $class = "ganttin_progress";
                                            } elseif ($t_info->task_status == "not_started") {
                                                $class = "gantt_not_started";
                                            } elseif ($t_info->task_status == "deferred") {
                                                $class = "gantt_deferred";
                                            } else {
                                                $class = "ganttin_progress";
                                            }
                                            $gantt_data3 .= '
                          {
                            name: "", desc: "' . $t_info->task_name . '", values: [';

                                            $gantt_data3 .= '{
                          label: "' . $t_info->task_name . '", from: "' . $start . '", to: "' . $end . '", customClass: "' . $class . '"
                          }';
                                            $gantt_data3 .= ']
                          },  ';
                                        }
                                    }
                                endforeach;
                            endforeach;
                        }
                        ?>

                        <div class="gantt"></div>
                        <div id="gantData">

                            <script type="text/javascript">
                                function ganttChart(ganttData) {
                                    $(function () {
                                        "use strict";
                                        $(".gantt").gantt({
                                            source: ganttData,
                                            minScale: "years",
                                            maxScale: "years",
                                            navigate: "scroll",
                                            itemsPerPage: 30,
                                            onItemClick: function (data) {
                                                console.log(data.id);
                                            },
                                            onAddClick: function (dt, rowId) {
                                            },
                                            onRender: function () {
                                                console.log("chart rendered");
                                            }
                                        });

                                    });
                                }

                                ganttData = [<?=$gantt_data;?>];
                                ganttChart(ganttData);

                                $(document).on("click", '.resize-gantt', function (e) {
                                    ganttData = [<?=$gantt_data;?>];
                                    ganttChart(ganttData);
                                });
                                $(document).on("click", '.status-gantt', function (e) {
                                    ganttData = [<?=$gantt_data2;?>];
                                    ganttChart(ganttData);
                                });
                                <?php if(!empty($gantt_data3)){?>
                                $(document).on("click", '.user-gantt', function (e) {
                                    ganttData = [<?=$gantt_data3;?>];
                                    ganttChart(ganttData);
                                });
                                <?php }?>

                            </script>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?= $active == 17 ? 'active' : '' ?>" id="project_settings"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('project_settings') ?></h3>
                    </div>
                    <div class="panel-body">

                        <form action="<?= base_url() ?>admin/projects/update_settings/<?php
                        if (!empty($project_details)) {
                            echo $project_details->project_id;
                        }
                        ?>" enctype="multipart/form-data" method="post" id="form" class="form-horizontal">
                            <?php
                            $project_permissions = $this->db->get('tbl_project_settings')->result();
                            if (!empty($project_details->project_settings)) {
                                $current_permissions = $project_details->project_settings;
                                if ($current_permissions == NULL) {
                                    $current_permissions = '{"settings":"on"}';
                                }
                                $get_permissions = json_decode($current_permissions);
                            }

                            foreach ($project_permissions as $v_permissions) {
                                ?>
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input name="<?= $v_permissions->settings_id ?>"
                                               value="<?= $v_permissions->settings ?>" <?php
                                        if (!empty($project_details->project_settings)) {
                                            if (in_array($v_permissions->settings, $get_permissions)) {
                                                echo "checked=\"checked\"";
                                            }
                                        } else {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> type="checkbox">
                                        <span class="fa fa-check"></span>
                                        <?= lang($v_permissions->settings) ?>
                                    </label>
                                </div>
                                <hr class="mt-sm mb-sm"/>
                            <?php } ?>

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
            <div class="tab-pane <?= $active == 8 ? 'active' : '' ?>" id="task_notes"
                 style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('notes') ?></h3>
                    </div>
                    <div class="panel-body">

                        <form action="<?= base_url() ?>admin/projects/save_project_notes/<?php
                        if (!empty($project_details)) {
                            echo $project_details->project_id;
                        }
                        ?>" enctype="multipart/form-data" method="post" id="form" class="form-horizontal">
                            <div class="form-group">
                                <div class="col-lg-12">
                                                <textarea class="form-control textarea"
                                                          name="notes"><?= $project_details->notes ?></textarea>
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

            <div class="tab-pane <?= $active == 14 ? 'active' : '' ?>" id="project_tickets" style="position: relative;">
                <div class="box" style="border: none; " data-collapsed="0">
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="<?= $task_active == 1 ? 'active' : ''; ?>"><a href="#manage_tickets"
                                                                                     data-toggle="tab"><?= lang('tickets') ?></a>
                            </li>
                            <li class=""><a
                                        href="<?= base_url() ?>admin/tickets/index/project_tickets/0/<?= $project_details->project_id ?>"><?= lang('new_ticket') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content bg-white">
                            <!-- ************** general *************-->
                            <div class="tab-pane <?= $task_active == 1 ? 'active' : ''; ?>" id="manage_tickets">
                                <div class="table-responsive">
                                    <table id="table-tickets" class="table table-striped ">
                                        <thead>
                                        <tr>
                                            <th><?= lang('ticket_code') ?></th>
                                            <th><?= lang('subject') ?></th>
                                            <th class="col-date"><?= lang('date') ?></th>
                                            <?php if ($this->session->userdata('user_type') == '1') { ?>
                                                <th><?= lang('reporter') ?></th>
                                            <?php } ?>
                                            <th><?= lang('department') ?></th>
                                            <th><?= lang('status') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if (!empty($all_tickets_info)) {
                                            foreach ($all_tickets_info as $v_tickets_info) {
                                                $can_edit = $this->items_model->can_action('tbl_tickets', 'edit', array('tickets_id' => $v_tickets_info->tickets_id));
                                                $can_delete = $this->items_model->can_action('tbl_tickets', 'delete', array('tickets_id' => $v_tickets_info->tickets_id));
                                                if ($v_tickets_info->status == 'open') {
                                                    $s_label = 'danger';
                                                } elseif ($v_tickets_info->status == 'closed') {
                                                    $s_label = 'success';
                                                } else {
                                                    $s_label = 'default';
                                                }
                                                $profile_info = $this->db->where(array('user_id' => $v_tickets_info->reporter))->get('tbl_account_details')->row();
                                                $dept_info = $this->db->where(array('departments_id' => $v_tickets_info->departments_id))->get('tbl_departments')->row();
                                                if (!empty($dept_info)) {
                                                    $dept_name = $dept_info->deptname;
                                                } else {
                                                    $dept_name = '-';
                                                } ?>

                                                <tr>

                                                    <td><span
                                                                class="label label-success"><?= $v_tickets_info->ticket_code ?></span>
                                                    </td>
                                                    <td><a class="text-info"
                                                           href="<?= base_url() ?>admin/tickets/index/tickets_details/<?= $v_tickets_info->tickets_id ?>"><?= $v_tickets_info->subject ?></a>
                                                    </td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($v_tickets_info->created)); ?></td>
                                                    <?php if ($this->session->userdata('user_type') == '1') { ?>

                                                        <td>
                                                            <a class="pull-left recect_task  ">
                                                                <?php if (!empty($profile_info)) {
                                                                    ?>
                                                                    <img style="width: 30px;margin-left: 18px;
                                                         height: 29px;
                                                         border: 1px solid #aaa;"
                                                                         src="<?= base_url() . $profile_info->avatar ?>"
                                                                         class="img-circle">


                                                                    <?=
                                                                    ($profile_info->fullname)
                                                                    ?>
                                                                <?php } else {
                                                                    echo '-';
                                                                } ?>
                                                            </a>
                                                        </td>

                                                    <?php } ?>
                                                    <td><?= $dept_name ?></td>
                                                    <?php
                                                    if ($v_tickets_info->status == 'in_progress') {
                                                        $status = 'In Progress';
                                                    } else {
                                                        $status = $v_tickets_info->status;
                                                    }
                                                    ?>
                                                    <td><span
                                                                class="label label-<?= $s_label ?>"><?= ucfirst($status) ?></span>
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
                            <!-- End Tasks Management-->

                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($all_invoice_info)) { ?>
                <div class="tab-pane <?= $active == 11 ? 'active' : '' ?>" id="invoice" style="position: relative;">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?= lang('invoice') ?></h3>
                        </div>
                        <div class="panel-body">

                            <div class="table-responsive">
                                <table id="table-invoice" class="table table-striped ">
                                    <thead>
                                    <tr>
                                        <th><?= lang('invoice') ?></th>
                                        <th class="col-date"><?= lang('due_date') ?></th>
                                        <th class="col-currency"><?= lang('amount') ?></th>
                                        <th class="col-currency"><?= lang('due_amount') ?></th>
                                        <th><?= lang('status') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($all_invoice_info as $v_invoices) {
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
                                        <tr>
                                            <td><a class="text-info"
                                                   href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $v_invoices->invoices_id ?>"><?= $v_invoices->reference_no ?></a>
                                            </td>
                                            <td><?= strftime(config_item('date_format'), strtotime($v_invoices->due_date)) ?>
                                                <?php
                                                $payment_status = $this->invoice_model->get_payment_status($v_invoices->invoices_id);
                                                if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) { ?>
                                                    <span
                                                            class="label label-danger "><?= lang('overdue') ?></span>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                            <td><?= display_money($this->invoice_model->calculate_to('invoice_cost', $v_invoices->invoices_id), $currency->symbol) ?></td>
                                            <td><?= display_money($this->invoice_model->calculate_to('invoice_due', $v_invoices->invoices_id), $currency->symbol) ?></td>
                                            <td><span
                                                        class="label label-<?= $label ?>"><?= $invoice_status ?></span>
                                                <?php if ($v_invoices->recurring == 'Yes') { ?>
                                                    <span data-toggle="tooltip" data-placement="top"
                                                          title="<?= lang('recurring') ?>"
                                                          class="label label-primary"><i
                                                                class="fa fa-retweet"></i></span>
                                                <?php } ?>

                                            </td>

                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="tab-pane <?= $active == 18 ? 'active' : '' ?>" id="credit_note" style="position: relative;">
                <div class="box" style="border: none; " data-collapsed="0">
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="<?= $estimate == 1 ? 'active' : ''; ?>"><a href="#manage_credit_note"
                                                                                  data-toggle="tab"><?= lang('credit_note') ?></a>
                            </li>
                            <li class=""><a
                                        href="<?= base_url() ?>admin/credit_note/index/project/<?= $project_details->project_id ?>"><?= lang('new_estimate') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content bg-white">
                            <!-- ************** general *************-->
                            <div class="tab-pane <?= $estimate == 1 ? 'active' : ''; ?>" id="manage_credit_note">
                                <div class="table-responsive">
                                    <table id="table-credit_note" class="table table-striped ">
                                        <thead>
                                        <tr>
                                            <th><?= lang('credit_note') ?> #</th>
                                            <th><?= lang('credit_note') . ' ' . lang('date') ?></th>
                                            <th><?= lang('client_name') ?></th>
                                            <th><?= lang('status') ?></th>
                                            <th><?= lang('amount') ?></th>
                                            <th><?= lang('remaining') . ' ' . lang('amount') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($all_credit_note_info as $v_credit_note) {
                                            if ($v_credit_note->status == 'refund') {
                                                $label = "info";
                                            } elseif ($v_credit_note->status == 'open') {
                                                $label = "success";
                                            } else {
                                                $label = "danger";
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <a class="text-info"
                                                       href="<?= base_url() ?>admin/credit_note/index/credit_note_details/<?= $v_credit_note->credit_note_id ?>"><?= $v_credit_note->reference_no ?></a>
                                                </td>
                                                <td><?= display_date($v_credit_note->credit_note_date) ?></td>
                                                <td><?= client_name($v_credit_note->client_id) ?></td>
                                                <td><span
                                                            class="label label-<?= $label ?>"><?= lang($v_credit_note->status) ?></span>
                                                </td>
                                                <td><?= display_money($this->credit_note_model->credit_note_calculation('total', $v_credit_note->credit_note_id), client_currency($v_credit_note->client_id)) ?></td>
                                            </tr>
                                            <?php
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
            <div class="tab-pane <?= $active == 12 ? 'active' : '' ?>" id="estimates" style="position: relative;">
                <div class="box" style="border: none; " data-collapsed="0">
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs">
                            <li class="<?= $estimate == 1 ? 'active' : ''; ?>"><a href="#manage_estimates"
                                                                                  data-toggle="tab"><?= lang('estimates') ?></a>
                            </li>
                            <li class=""><a
                                        href="<?= base_url() ?>admin/estimates/index/project/<?= $project_details->project_id ?>"><?= lang('new_estimate') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content bg-white">
                            <!-- ************** general *************-->
                            <div class="tab-pane <?= $estimate == 1 ? 'active' : ''; ?>" id="manage_estimates">
                                <div class="table-responsive">
                                    <table id="table-estimates" class="table table-striped ">
                                        <thead>
                                        <tr>
                                            <th><?= lang('estimate') ?></th>
                                            <th><?= lang('due_date') ?></th>
                                            <th><?= lang('amount') ?></th>
                                            <th><?= lang('status') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($all_estimates_info as $v_estimates) {
                                            if ($v_estimates->status == 'Pending') {
                                                $label = "info";
                                            } elseif ($v_estimates->status == 'Accepted') {
                                                $label = "success";
                                            } else {
                                                $label = "danger";
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <a class="text-info"
                                                       href="<?= base_url() ?>admin/estimates/index/estimates_details/<?= $v_estimates->estimates_id ?>"><?= $v_estimates->reference_no ?></a>
                                                </td>
                                                <td><?= strftime(config_item('date_format'), strtotime($v_estimates->due_date)) ?>
                                                    <?php
                                                    if (strtotime($v_estimates->due_date) < strtotime(date('Y-m-d')) && $v_estimates->status == 'Pending') { ?>
                                                        <span
                                                                class="label label-danger "><?= lang('expired') ?></span>
                                                    <?php }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= display_money($this->estimates_model->estimate_calculation('estimate_amount', $v_estimates->estimates_id), $currency->symbol); ?>
                                                </td>
                                                <td><span
                                                            class="label label-<?= $label ?>"><?= lang(strtolower($v_estimates->status)) ?></span>
                                                </td>
                                            </tr>
                                            <?php
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
            <div class="tab-pane <?= $active == 7 ? 'active' : '' ?>" id="timesheet"
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
                                <table id="table-tasks-timelog" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th><?= lang('user') ?></th>
                                        <th><?= lang('start_time') ?></th>
                                        <th><?= lang('stop_time') ?></th>

                                        <th><?= lang('project_name') ?></th>
                                        <th class="col-time"><?= lang('time_spend') ?></th>
                                        <th><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($total_timer)) {
                                        foreach ($total_timer as $v_timer) {
                                            $aproject_info = $this->db->where(array('project_id' => $v_timer->project_id))->get('tbl_project')->row();
                                            if (!empty($aproject_info)) {
                                                if ($v_timer->start_time != 0 && $v_timer->end_time != 0) {
                                                    ?>
                                                    <tr id="table-timesheet-<?= $v_timer->tasks_timer_id ?>">
                                                        <td class="small">

                                                            <a class="pull-left recect_task  ">
                                                                <?php
                                                                $profile_info = $this->db->where(array('user_id' => $v_timer->user_id))->get('tbl_account_details')->row();
                                                                $user_info = $this->db->where(array('user_id' => $v_timer->user_id))->get('tbl_users')->row();
                                                                if (!empty($user_info)) {
                                                                    ?>
                                                                    <img style="width: 30px;margin-left: 18px;
                                                                             height: 29px;
                                                                             border: 1px solid #aaa;"
                                                                         src="<?= base_url() . $profile_info->avatar ?>"
                                                                         class="img-circle">
                                                                <?php } else {
                                                                    echo '-';
                                                                } ?>
                                                            </a>


                                                        </td>

                                                        <td><span
                                                                    class="label label-success"><?= display_datetime($v_timer->start_time, true) ?></span>
                                                        </td>
                                                        <td><span
                                                                    class="label label-danger"><?= display_datetime($v_timer->end_time, true); ?></span>
                                                        </td>

                                                        <td>
                                                            <a href="<?= base_url() ?>admin/projects/project_details/<?= $v_timer->project_id ?>"
                                                               class="text-info small"><?= $project_details->project_name  ?>
                                                                <?php
                                                                if (!empty($v_timer->reason)) {
                                                                    $edit_user_info = $this->db->where(array('user_id' => $v_timer->edited_by))->get('tbl_users')->row();
                                                                    echo '<i class="text-danger" data-html="true" data-toggle="tooltip" data-placement="top" title="Reason : ' . $v_timer->reason . '<br>' . ' Edited By : ' . $edit_user_info->username . '">Edited</i>';
                                                                }
                                                                ?>
                                                            </a></td>
                                                        <td>
                                                            <small
                                                                    class="small text-muted"><?= $this->items_model->get_time_spent_result($v_timer->end_time - $v_timer->start_time) ?></small>
                                                        </td>
                                                        <td>
                                                            <?= btn_edit('admin/projects/project_details/' . $v_timer->project_id . '/7/' . $v_timer->tasks_timer_id) ?>
                                                            <?php echo ajax_anchor(base_url("admin/projects/update_project_timer/" . $v_timer->tasks_timer_id . '/delete_task_timmer'), "<i class='btn btn-danger btn-xs fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table-timesheet-" . $v_timer->tasks_timer_id)); ?>
                                                        </td>

                                                    </tr>
                                                    <?php
                                                }
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
                                  action="<?php echo base_url(); ?>admin/projects/update_project_timer/<?php
                                  if (!empty($project_timer_info)) {
                                      echo $project_timer_info->tasks_timer_id;
                                  }
                                  ?>" method="post" class="form-horizontal">
                                <?php
                                if (!empty($project_timer_info)) {
                                    $start_date = date('Y-m-d', $project_timer_info->start_time);
                                    $start_time = date('H:i', $project_timer_info->start_time);
                                    $end_date = date('Y-m-d', $project_timer_info->end_time);
                                    $end_time = date('H:i', $project_timer_info->end_time);
                                } else {
                                    $start_date = '';
                                    $start_time = '';
                                    $end_date = '';
                                    $end_time = '';
                                }
                                ?>
                                <?php if ($this->session->userdata('user_type') == '1' && empty($project_timer_info->tasks_timer_id)) { ?>
                                    <div class="form-group margin">
                                        <div class="col-sm-8 center-block">
                                            <label
                                                    class="control-label"><?= lang('select') . ' ' . lang('project') ?>
                                                <span
                                                        class="required">*</span></label>
                                            <select class="form-control select_box" name="project_id"
                                                    required="" style="width: 100%">
                                                <?php
                                                $all_tasks_info = $this->db->get('tbl_project')->result();
                                                if (!empty($all_tasks_info)):foreach ($all_tasks_info as $v_task_info):
                                                    ?>
                                                    <option
                                                            value="<?= $v_task_info->project_id ?>"
                                                            <?= $v_task_info->project_id == $project_details->project_id ? 'selected' : null ?>><?= $v_task_info->project_name  ?></option>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <input type="hidden" name="project_id"
                                           value="<?= $project_details->project_id ?>">
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
                                        <label class="control-label"><?= lang('edit_reason') ?> <span
                                                    class="required">*</span></label>
                                        <div>
                                                <textarea class="form-control" name="reason" required="" rows="6"><?php
                                                    if (!empty($project_timer_info)) {
                                                        echo $project_timer_info->reason;
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
        </div>
    </div>
</div>