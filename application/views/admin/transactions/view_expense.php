<?php
$account_info = $this->transactions_model->check_by(array('account_id' => $expense_info->account_id), 'tbl_accounts');
$currency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

$category = lang('undefined_category');

if($expense_info->type == 'Income'){
    $category_info = $this->db->where('income_category_id', $expense_info->category_id)->get('tbl_income_category')->row();
    if (!empty($category_info)) {
        $category = $category_info->income_category;
    }
}else{
    $category_info = $this->db->where('expense_category_id', $expense_info->category_id)->get('tbl_expense_category')->row();
    if (!empty($category_info)) {
        $category = $category_info->expense_category;
    }
}
$client_name = $this->db->where('client_id', $expense_info->paid_by)->get('tbl_client')->row();
$active = 1;
$all_task_info = $this->db->where('transactions_id', $expense_info->transactions_id)->order_by('transactions_id', 'DESC')->get('tbl_task')->result();
$notified_reminder = count($this->db->where(array('module' => 'expense', 'module_id' => $expense_info->transactions_id, 'notified' => 'No'))->get('tbl_reminders')->result());
$can_edit = $this->transactions_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $expense_info->transactions_id));
$edited = can_action('30', 'edited');
?>
<div class="row mt-lg">
    <div class="col-sm-3">
        <ul class="nav nav-pills nav-stacked navbar-custom-nav">
            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#expense_details"
                                                               data-toggle="tab"><?= lang('details') ?></a></li>
            <li class="<?= $active == 6 ? 'active' : '' ?>"><a href="#tasks"
                                                               data-toggle="tab"><?= lang('tasks') ?><strong
                            class="pull-right"><?= (!empty($all_task_info) ? count($all_task_info) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 'reminder' ? 'active' : '' ?>"><a href="#reminder" data-toggle="tab"
                                                                        aria-expanded="false"><?= lang('reminder') ?>
                    <strong
                            class="pull-right"><?= (!empty($notified_reminder) ? $notified_reminder : null) ?></strong>
                </a>
            </li>
        </ul>
    </div>
    <div class="col-sm-9">
        <div class="tab-content" style="border: 0;padding:0;">
            <!-- Expense Details tab Starts -->
            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="expense_details" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">

                        <h4 class="modal-title"
            id="myModalLabel"><?php echo $title;
            if (!empty($expense_info->reference)) {
                echo '#' . $expense_info->reference;
            }
            ?>
            <div class="pull-right">
                <?php if (!empty($can_edit) && !empty($edited)) {
                    echo btn_edit('admin/transactions/expense/' . $expense_info->transactions_id);
                } ?>
                <?php

                if ($expense_info->billable == 'Yes' && $expense_info->project_id != 0 && $expense_info->invoices_id == 0) { ?>
                    <a onclick="return confirm('Are you sure to <?= lang('convert') ?> This <?= (!empty($expense_info->name) ? $expense_info->name : '-') ?> ?')"
                       href="<?= base_url() ?>admin/transactions/convert/<?= $expense_info->transactions_id ?>"
                       class="btn btn-purple btn-xs"><i
                                class="fa fa-copy"></i> <?= lang('convert_to_invoice') ?></a>

                <?php } ?>
                <?= btn_pdf('admin/transactions/download_pdf/' . $expense_info->transactions_id) ?>
            </div>
        </h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <div class="panel-body form-horizontal">
            <?php
            if ($expense_info->recurring == 'Yes' || $expense_info->recurring_from != NULL) {
                $show_recurring_expense_info = true;
                $recurring_from = $expense_info;
                $recurring_from->recurring = 1;
                if ($expense_info->recurring_from != NULL) {
                    $recurring_from = get_row('tbl_transactions', array('transactions_id' => $expense_info->transactions_id));
                    // Maybe recurring expense not longer recurring?
                    if ($recurring_from->recurring == 'No') {
                        $show_recurring_expense_info = false;
                    } else {
                        $next_recurring_date_compare = $recurring_from->last_recurring_date;
                    }
                } else {
                    $next_recurring_date_compare = $recurring_from->date;
                    if ($recurring_from->last_recurring_date) {
                        $next_recurring_date_compare = $recurring_from->last_recurring_date;
                    }
                }
                if ($show_recurring_expense_info) {
                    $next_date = date('Y-m-d', strtotime('+' . $recurring_from->recurring . ' ' . strtoupper($recurring_from->recurring_type), strtotime($next_recurring_date_compare)));
                }

                ?>
                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('recurring') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static">
                            <a class="label label-success" href="#"> <?= lang('yes') ?></a>
                            <?php if ($expense_info->recurring_from == null && $recurring_from->total_cycles > 0 && $recurring_from->total_cycles == $recurring_from->done_cycles) { ?>
                                <span class="label label-info ml-lg">
                            <?php echo lang('recurring_has_ended', lang('expense')); ?>
                                </span>
                            <?php } else if ($show_recurring_expense_info) { ?>
                                <span class="label label-default ml-lg">
                                <?php echo lang('cycles_remaining'); ?>:
                                <strong>
                                    <?php
                                    echo $recurring_from->total_cycles == 0 ? lang('infinity') : $recurring_from->total_cycles - $recurring_from->done_cycles;
                                    ?>
                                </strong>
                            </span>
                                <?php if ($recurring_from->total_cycles == 0 || $recurring_from->total_cycles != $recurring_from->done_cycles) {
                                    echo '<span class="label label-default  ml-lg"><i class="fa fa-question-circle fa-fw" data-toggle="tooltip" data-title="' . lang('recurring_recreate_hour_info', lang('expense')) . '"></i> ' . lang('next_expense_date', '<b>' . display_date($next_date) . '</b>') . '</span>';
                                }
                            }
                            if ($expense_info->recurring_from != NULL) { ?>
                                <?php echo '<p class="text-muted no-mbot' . ($show_recurring_expense_info ? ' mtop15' : '') . '">' . lang('expense_recurring_from', '<a href="' . base_url('admin/transactions/view_details/' . $expense_info->recurring_from) . '" >' . $recurring_from->name . (!empty($recurring_from->reference) ? ' (' . '#' . $recurring_from->reference . ')' : '') . '</a></p>'); ?>
                            <?php } ?>
                        </p>
                    </div>
                </div>
            <?php } ?>
            <?php
            if ($expense_info->invoices_id != 0) {
                $payment_status = $this->invoice_model->get_payment_status($expense_info->invoices_id);
                $invoice_info = $this->db->where('invoices_id', $expense_info->invoices_id)->get('tbl_invoices')->row();
                if ($payment_status == lang('fully_paid')) {
                    $p_label = "success";
                } elseif ($payment_status == lang('draft')) {
                    $p_label = "default";
                    $text = lang('status_as_draft');
                } elseif ($payment_status == lang('cancelled')) {
                    $p_label = "danger";
                } elseif ($payment_status == lang('partially_paid')) {
                    $p_label = "warning";
                } else {
                    $p_label = "primary";
                }
                $paid_amount = $this->invoice_model->calculate_to('paid_amount', $expense_info->invoices_id);
                ?>

                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('reference_no') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static"><a class="label label-success"
                                                          href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $expense_info->invoices_id ?>"> <?= $invoice_info->reference_no ?>
                            </a>
                        </p>
                    </div>
                </div>
                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('payment_status') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static"><span class="label label-<?= $p_label ?>">
                            <?= $payment_status ?>
                            </span>
                        </p>
                    </div>
                </div>
                <?php if ($paid_amount > 0) { ?>
                    <div class="col-md-12 notice-details-margin">
                        <div class="col-sm-4 text-right">
                            <label class="control-label"><strong><?= lang('paid_amount') ?> :</strong></label>
                        </div>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= display_money($paid_amount, $currency->symbol); ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php
            if ($expense_info->project_id != 0) {
                $project = $this->db->where('project_id', $expense_info->project_id)->get('tbl_project')->row();
                ?>
                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('project_name') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= ($project->project_name ? $project->project_name : '-') ?></p>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('name') . '/' . lang('title') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= (!empty($expense_info->name) ? $expense_info->name : '-') ?></p>
                </div>
            </div>

            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('date') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= strftime(config_item('date_format'), strtotime($expense_info->date)); ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('account') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php
                        if (!empty($account_info->account_name)) {
                            echo $account_info->account_name;
                        } else {
                            echo '-';
                        }
                        ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('amount') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= display_money($expense_info->amount, $currency->symbol) ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('categories') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $category ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('paid_by') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= (!empty($client_name->name) ? $client_name->name : '-'); ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('payment_method') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php
                        if (!empty($expense_info->payment_methods_id)) {
                            $payment_methods = $this->db->where('payment_methods_id', $expense_info->payment_methods_id)->get('tbl_payment_methods')->row();
                        }
                        if (!empty($payment_methods)) {
                            echo $payment_methods->method_name;
                        } else {
                            echo '-';
                        }
                        ?></p>
                </div>
            </div>
            <?php if ($expense_info->type == 'Expense') { ?>
                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('status') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?php
                            $status = $expense_info->status;
                            if ($expense_info->project_id != 0) {
                                if ($expense_info->billable == 'No') {
                                    $status = 'not_billable';
                                    $label = 'primary';
                                    $title = lang('not_billable');
                                    $action = '';
                                } else {
                                    $status = 'billable';
                                    $label = 'success';
                                    $title = lang('billable');
                                    $action = '';
                                }
                            } else {
                                if ($expense_info->status == 'non_approved') {
                                    $label = 'danger';
                                    $title = lang('get_approved');
                                    $action = 'approved';
                                } elseif ($expense_info->status == 'unpaid') {
                                    $label = 'warning';
                                    $title = lang('get_paid');
                                    $action = 'paid';
                                } else {
                                    $label = 'success';
                                    $title = '';
                                    $action = '';
                                }
                            }
                            $check_head = $this->db->where('department_head_id', $this->session->userdata('user_id'))->get('tbl_departments')->row();
                            $role = $this->session->userdata('user_type');
                            if ($role == 1 || !empty($check_head)) {
                                ?>
                                <a data-toggle="tooltip" data-placement="top" title="<?= $title ?>"
                                   class="label label-<?= $label ?>"
                                   href="
                                               <?php
                                   if (!empty($action)) {
                                       echo base_url() ?>admin/transactions/set_status/<?= $action . '/' . $expense_info->transactions_id;
                                   } else {
                                       echo '#';
                                   }
                                   ?>">

                                    <?= lang($status) ?>

                                </a>
                            <?php } else { ?>
                                <span class="label label-<?= $label ?>">
                                                <?= lang($status); ?>
                                            </span>

                            <?php } ?></p>
                    </div>
                </div>
            <?php } ?>
            <?php
            if ($expense_info->type == 'Income') {
                $view = 1;
            } else {
                $view = 2;
            }
            $show_custom_fields = custom_form_label($view, $expense_info->transactions_id);

            if (!empty($show_custom_fields)) {
                foreach ($show_custom_fields as $c_label => $v_fields) {
                    if (!empty($v_fields)) {
                        ?>
                        <div class="col-md-12 notice-details-margin">
                            <div class="col-sm-4 text-right">
                                <label class="control-label"><strong><?= $c_label ?> :</strong></label>
                            </div>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?= $v_fields ?></p>
                            </div>
                        </div>
                    <?php }
                }
            }
            ?>
            <?php
            $uploaded_file = json_decode($expense_info->attachement);
            ?>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('attachment') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <ul class="mailbox-attachments clearfix mt">
                        <?php
                        if (!empty($uploaded_file)):
                            foreach ($uploaded_file as $v_files):

                                if (!empty($v_files)):

                                    ?>
                                    <li>
                                        <?php if ($v_files->is_image == 1) : ?>
                                            <span class="mailbox-attachment-icon has-img"><img
                                                        src="<?= base_url() . $v_files->path ?>"
                                                        alt="Attachment"></span>
                                        <?php else : ?>
                                            <span class="mailbox-attachment-icon"><i
                                                        class="fa fa-file-pdf-o"></i></span>
                                        <?php endif; ?>
                                        <div class="mailbox-attachment-info">
                                            <a href="<?= base_url() ?>admin/transactions/download/<?= $expense_info->transactions_id . '/' . $v_files->fileName ?>"
                                               class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>
                                                <?= $v_files->fileName ?></a>
                                            <span class="mailbox-attachment-size">
                          <?= $v_files->size ?> <?= lang('kb') ?>
                            <a href="<?= base_url() ?>admin/transactions/download/<?= $expense_info->transactions_id . '/' . $v_files->fileName ?>"
                               class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                        </span>
                                        </div>
                                    </li>
                                <?php
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('notes') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <blockquote style="font-size: 12px"><?php echo $expense_info->notes; ?></blockquote>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
    </div>
</div>
            </div>
            <!-- Expense Details tab Starts -->
            <!-- Tasks tab Starts -->
            <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="tasks"
                 style="position: relative;">
                <div class="nav-tabs-custom ">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#manageTasks" data-toggle="tab"><?= lang('all_task') ?></a>
                        </li>
                        <li class=""><a
                                    href="<?= base_url() ?>admin/tasks/all_task/expense/<?= $expense_info->transactions_id ?>"><?= lang('new_task') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane active" id="manageTasks"
                             style="position: relative;">

                            <div class="box" style="border: none; padding-top: 15px;" data-collapsed="0">
                                <div class="box-body">
                                    <table class="table table-hover" id="">
                                        <thead>
                                        <tr>
                                            <th data-check-all>

                                            </th>
                                            <th class="col-sm-4"><?= lang('task_name') ?></th>
                                            <th class="col-sm-2"><?= lang('due_date') ?></th>
                                            <th class="col-sm-1"><?= lang('status') ?></th>
                                            <th class="col-sm-1"><?= lang('progress') ?></th>
                                            <th class="col-sm-3"><?= lang('changes/view') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if (!empty($all_task_info)):foreach ($all_task_info as $key => $v_task):
                                            ?>
                                            <tr id="leads_tasks_<?= $v_task->task_id ?>">
                                                <td class="col-sm-1">
                                                    <div class="is_complete checkbox c-checkbox">
                                                        <label>
                                                            <input type="checkbox"
                                                                   data-id="<?= $v_task->task_id ?>"
                                                                   style="position: absolute;" <?php
                                                            if ($v_task->task_progress >= 100) {
                                                                echo 'checked';
                                                            }
                                                            ?>>
                                                            <span class="fa fa-check"></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a style="<?php
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
                                                        <span
                                                                class="label label-danger"><?= lang('overdue') ?></span>
                                                    <?php } ?></td>
                                                <td><?php
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
                                                    <div class="inline ">
                                                        <div class="easypiechart text-success"
                                                             style="margin: 0px;"
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
                                                    <?php echo ajax_anchor(base_url("admin/tasks/delete_task/" . $v_task->task_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#leads_tasks_" . $v_task->task_id)); ?>
                                                    <?php echo btn_edit('admin/tasks/all_task/' . $v_task->task_id) ?>
                                                    <?php

                                                    if ($v_task->timer_status == 'on') { ?>
                                                        <a class="btn btn-xs btn-danger"
                                                           href="<?= base_url() ?>admin/tasks/tasks_timer/off/<?= $v_task->task_id ?>"><?= lang('stop_timer') ?> </a>

                                                    <?php } else { ?>
                                                        <a class="btn btn-xs btn-success"
                                                           href="<?= base_url() ?>admin/tasks/tasks_timer/on/<?= $v_task->task_id ?>"><?= lang('start_timer') ?> </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Tasks tab Starts -->
            <!-- Reminder tab Starts -->
            <div class="tab-pane <?= $active == 3 ? 'active' : '' ?>" id="reminder">
                <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#reminder_manage"
                                              data-toggle="tab"><?= lang('reminder') . ' ' . lang('list') ?></a>
                        </li>
                        <li class=""><a href="#reminder_create"
                                        data-toggle="tab"><?= lang('set') . ' ' . lang('reminder') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane active" id="reminder_manage">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th><?= lang('description') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('remind') ?></th>
                                        <th><?= lang('notified') ?></th>
                                        <th class="col-options no-sort"><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $all_reminder = $this->db->where(array('module' => 'expense', 'module_id' => $expense_info->transactions_id))->get('tbl_reminders')->result();
                                    if (!empty($all_reminder)) {
                                        foreach ($all_reminder as $v_reminder):
                                            $remind_user_info = $this->db->where('user_id', $v_reminder->user_id)->get('tbl_account_details')->row();
                                            ?>
                                            <tr id="leads_reminder_<?= $v_reminder->reminder_id ?>">
                                                <td><?= $v_reminder->description ?></td>
                                                <td><?= strftime(config_item('date_format'), strtotime($v_reminder->date)) . ' ' . display_time($v_reminder->date) ?></td>
                                                <td>
                                                    <a href="<?= base_url() ?>admin/user/user_details/<?= $v_reminder->user_id ?>"> <?= $remind_user_info->fullname ?></a>
                                                </td>
                                                <td><?= $v_reminder->notified ?></td>
                                                <td>
                                                    <?php echo ajax_anchor(base_url("admin/invoice/delete_reminder/" . $v_reminder->module . '/' . $v_reminder->module_id . '/' . $v_reminder->reminder_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#leads_reminder_" . $v_reminder->reminder_id)); ?>
                                                </td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="5"><?= lang('nothing_to_display') ?></td>
                                        </tr>
                                    <?php }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="reminder_create">
                            <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                                  id="form"
                                  action="<?php echo base_url(); ?>admin/invoice/reminder/expense/<?= $expense_info->transactions_id ?>/<?php
                                  if (!empty($reminder_info)) {
                                      echo $reminder_info->reminder_id;
                                  }
                                  ?>" method="post" class="form-horizontal  ">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('date_to_notified') ?> <span
                                                class="text-danger">*</span></label>
                                    <div class="col-lg-5">
                                        <div class="input-group">
                                            <input type="text" name="date"
                                                   class="form-control datetimepicker"
                                                   value="<?php
                                                   if (!empty($reminder_info->date)) {
                                                       echo $reminder_info->date;
                                                   } else {
                                                       echo date('Y-m-d h:i');
                                                   }
                                                   ?>"
                                                   data-date-min-date="<?= date('Y-m-d'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- End discount Fields -->
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                                    <div class="col-lg-5">
                        <textarea name="description" class="form-control"><?php
                            if (!empty($reminder_info)) {
                                echo $reminder_info->description;
                            }
                            ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('set_reminder_to') ?> <span
                                                class="text-danger">*</span></label>
                                    <div class="col-lg-5">
                                        <select class="form-control select_box" name="user_id" style="width: 100%">
                                            <?php
                                            $permission_user = $this->transactions_model->all_permission_user('4');
                                            if (!empty($permission_user)) {
                                                foreach ($permission_user as $key => $v_users) {
                                                    ?>
                                                    <option <?php
                                                    if (!empty($reminder_info)) {
                                                        echo $reminder_info->user_id == $v_users->user_id ? 'selected' : null;
                                                    }
                                                    ?> value="<?= $v_users->user_id ?>"><?= $v_users->fullname ?></option>
                                                <?php }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-5">
                                        <div class="checkbox c-checkbox">
                                            <label class="needsclick">
                                                <input type="checkbox" value="Yes"
                                                    <?php if (!empty($reminder_info) && $reminder_info->notify_by_email == 'Yes') {
                                                        echo 'checked';
                                                    } ?> name="notify_by_email">
                                                <span class="fa fa-check"></span>
                                                <?= lang('send_also_email_this_reminder') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-5">
                                        <button type="submit" class="btn btn-purple"><?= lang('update') ?></button>
                                        <button type="button" class="btn btn-primary pull-right"
                                                data-dismiss="modal"><?= lang('close') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <link rel="stylesheet"
                      href="<?= base_url() ?>assets/plugins/datetimepicker/jquery.datetimepicker.min.css">
                <?php include_once 'assets/plugins/datetimepicker/jquery.datetimepicker.full.php'; ?>

                <script type="text/javascript">
                    init_datepicker();

                    // Date picker init with selected timeformat from settings
                    function init_datepicker() {
                        var datetimepickers = $('.datetimepicker');
                        if (datetimepickers.length == 0) {
                            return;
                        }
                        var opt_time;
                        // Datepicker with time
                        $.each(datetimepickers, function () {
                            opt_time = {
                                lazyInit: true,
                                scrollInput: false,
                                format: 'Y-m-d H:i',
                            };

                            opt_time.formatTime = 'H:i';
                            // Check in case the input have date-end-date or date-min-date
                            var max_date = $(this).data('date-end-date');
                            var min_date = $(this).data('date-min-date');
                            if (max_date) {
                                opt_time.maxDate = max_date;
                            }
                            if (min_date) {
                                opt_time.minDate = min_date;
                            }
                            // Init the picker
                            $(this).datetimepicker(opt_time);
                        });
                    }
                </script>
            </div>
            <!-- Reminder tab Starts -->
        </div>
    </div>
</div>
